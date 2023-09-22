<?php
namespace App\Controllers\Reports;
use App\Utils\Html;

class Resultado extends Report
{
	public function report(string $title, ?array $values): void
	{
		$this->export($title, $this->render($values));
	}

	private function render(?array $values): string
	{
		$params = [
			'list_itens' => $this->renderList($values)
		];
		return $this->getReport('Relatório Resultado Apuracao', 'reports/resultado', $params);
	}


	private function renderList(?array $values):string
	{
		return Html::genericTable($values['header'], $values['body']);
	}

}