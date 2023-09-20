<?php
namespace App\Controllers\Pages;

use App\Controllers\Reports\Chamados;
use App\Utils\Dates;
use App\Utils\Forms;
use App\Utils\Validate;

class Reports extends Page
{
	

	public function index(): string
	{
       

		$params = [
            
		];
		return $this->getPage('Relatórios', 'pages/reports', $params);
	}

	public function submit(): void
	{
		$report = new Chamados();
		$report->report('Relatório Chamados', $this->data());
	}

    public function options(?array $params = null):string
	{
		return "";
	}

	private function data():array
	{
        return [];
	}

	private function search(): array
	{

        $values = Forms::all();
		$fields = ['status', 'setor', 'equipamento', 'tecnico'];

		$isvalid  = Validate::check($values, [
            'dataini' => 'required',
            'datafin' => 'required'
        ]);

		if ($isvalid['status']) {
			$datas = ['dataabr' => [Dates::fmtDB($values['dataini']), Dates::fmtDB($values['datafin'])]];
            return array_merge($datas, array_filter(Forms::all($fields)));
		}

		return [];
	}
}