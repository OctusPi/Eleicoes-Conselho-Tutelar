<?php
namespace App\Controllers\Reports;
use App\Data\ImpDao;
use App\Models\EntityChamado;
use App\Models\EntityEquipamento;
use App\Models\EntitySetor;
use App\Models\EntityUser;
use App\Utils\Html;
use App\Utils\Utils;

class Chamados extends Report
{
	public function report(string $title, ?array $values): void
	{
		$this->export($title, $this->render($values));
	}

	private function render(?array $values): string
	{
		$totals = $this->calcTotals($values);
		$params = [
			'list_itens' => $this->renderList($values)
		];
		return $this->getReport('Relatório Detalhado Chamados', 'reports/chamados', array_merge($totals, $params));
	}

	private function calcTotals(?array $values): array
	{
		$calls = [
			'opens_calls' => 0,
			'proccess_calls' => 0,
			'solved_calls' => 0,
			'reopens_calls' => 0,
			'finish_calls' => 0,
		];

		if ($values != null) {
			foreach ($values as $value) {
				match ($value->get('status')) {
					"Aberto" 	=> $calls['opens_calls'] 	= $calls['opens_calls'] + 1,
					"Em Atendimento" 	=> $calls['proccess_calls'] = $calls['proccess_calls'] + 1,
					"Solucionado" 	=> $calls['solved_calls'] 	= $calls['solved_calls'] + 1,
					"Reaberto" 	=> $calls['reopens_calls'] 	= $calls['reopens_calls'] + 1,
					default => $calls['finish_calls'] 	= $calls['finish_calls'] + 1,
				};
			}
		}
		return $calls;
	}

	private function renderList(?array $values):string
	{
		$sectors = Utils::selectObj((new ImpDao(new EntitySetor))->readData(all:true, order:'id') ?? [], "id", ['nome']);
		$equips  = Utils::selectObj((new ImpDao(new EntityEquipamento))->readData(all:true, order:'id') ?? [], "id", ['tipo', 'modelo', 'marca']);
		$tecsguy = Utils::selectObj((new ImpDao(new EntityUser))->readData(all:true, order:'id') ?? [], "id", ['nome']);

		$header = ['CÓDIGO', 'TIPO','DATA ABERTURA', 'DATA ATEDIMENTO', 'SETOR', 'EQUIPAMENTO', 'TÉCNICO', 'STATUS'];
		$body   = [];

		if($values != null){
			foreach ($values as $value) {
				$body[] = [
					'codigo'		=> $value->get('cod'),
					'tipo'			=> $value->get('tipo'),
					'dataabt'		=> $value->get('dataabr'),
					'dataatm'		=> $value->get('dataatm'),
					'setor'			=> Utils::at($value->get('setor'), $sectors),
					'equipamento'	=> Utils::at($value->get('equipamento'), $equips),
					'tecnico'		=> Utils::at($value->get('tecnico'), $tecsguy),
					'status'		=> $value->get('status'),
				];
			}
		}

		return Html::genericTable($header, $body);
	}

}