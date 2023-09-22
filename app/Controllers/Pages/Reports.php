<?php
namespace App\Controllers\Pages;

use App\Controllers\Reports\Resultado;
use App\Data\ImpDao;
use App\Models\Apuracao;
use App\Models\Candidato;
use App\Models\Sessao;
use App\Utils\Dates;
use App\Utils\Forms;
use App\Utils\Html;
use App\Utils\Utils;
use App\Utils\Validate;

class Reports extends Page
{
	

	public function index(): string
	{
		$cadidatos = (new ImpDao(new Candidato()))->readData(all:true, order:'nome');
		$sessoes   = (new ImpDao(new Sessao()))->readData(all:true, order:'numero');

		$params = [
            'form_candidatos' => Html::selectObj($cadidatos, 'id', ['nome', 'numero']),
            'form_sessoes'    => Html::selectObj($sessoes, 'id', ['numero', 'local']),
		];
		return $this->getPage('Relatórios', 'pages/reports', $params);
	}

	public function submit(): void
	{
		$report = new Resultado();
		$report->report('Relatório Resultado Apuracao', $this->data());
	}

	private function data():array
	{
		//searchs
		$search = $this->search();
		$searchCandidato = Utils::at('candidato', $search) ? ['id'=>$search['candidato']] : [];
		$searchSessao    = Utils::at('sessao', $search)   ? ['id'=>$search['sessao']]    : [];

		//consult SQL
		$candidatos = (new ImpDao(new Candidato()))->readData($searchCandidato, true, 'nome') ?? [];
		$sessoes    = (new ImpDao(new Sessao()))->readData($searchSessao, true, 'numero') ?? [];

		//Maps
		$mapCandidatos = Utils::selectObj($candidatos, 'id', ['nome', 'numero']);

		//Orde Position Winners
		$winners = [];
		foreach ($candidatos as $candidato) {
			$totalVotos = (new ImpDao(new Apuracao()))->readSUM('votos', ['candidato' => $candidato->get('id')]);
			$winners[$candidato->get('id')] = $totalVotos;
		}
		arsort($winners);

		//initialize header and body make table
		$header = array_merge(['0' => 'CANDIDATO/SEÇÃO'], Utils::selectObj($sessoes, 'id', ['numero', 'local']), ['TOTAL'=>'TOTAL VOTOS']);
		$body   = [];

		//feed body with values
		foreach ($winners as $key=>$winner) {
			$apuracao = [];
			$apuracao['candidato'] = $mapCandidatos[$key];
			foreach ($sessoes as $sessao) {
				$votos = (new ImpDao(new Apuracao()))->readData(['candidato' => $key, 'sessao' => $sessao->get('id')]);
				$total = $votos != null ? $votos->get('votos') : 0;
				$apuracao[] = $total; 
			}
			$apuracao['total'] = $winner;

			$body[] = $apuracao;
		}

        return [
			'header' => $header,
			'body'   => $body
		];
	}

	private function search(): array
	{

        $values = Forms::all();
		$fields = ['candidato', 'sessao'];

		$isvalid  = Validate::check($values, []);

		if ($isvalid['status']) {
            return array_filter(Forms::all($fields));
		}

		return [];
	}
}