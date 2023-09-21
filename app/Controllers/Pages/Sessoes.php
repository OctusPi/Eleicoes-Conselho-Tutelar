<?php
namespace App\Controllers\Pages;

use App\Data\ImpDao;
use App\Models\Candidato;
use App\Models\Sessao;
use App\Utils\Alerts;
use App\Utils\Forms;
use App\Utils\Route;
use App\Utils\Uploads;
use App\Utils\Utils;
use App\Utils\Validate;
use App\Utils\View;

class Sessoes extends Page
{

	public function index(): string
	{
		$params = [
			'form_search' => View::renderView('pages/forms/search/sessoes')
		];
		return $this->getPage('Sessoes', 'pages/sessoes', $params);
	}

	public function submit(): string
	{
		$values   = Forms::all();
		$validate = Validate::check($values, [
			'local' => 'required',
			'numero'   => 'required'
		]);

		if($validate['status']){
			$sessao = new Sessao();
			$sessao->feeds($values);
			$dao = new ImpDao($sessao);
			$wirte = $dao->writeData();

			return json_encode([
				'message'  => Alerts::msg($wirte['code'], Alerts::infoDAO($wirte['code'])),
				'dataview' => $this->data(false)
			]); 
		}

		return json_encode([
			'message' => Alerts::msg(Alerts::WARNING, "Dados inválidos..."),
		]);
	}

	public function data(bool $json = true): string|array
	{
		$header = [
			'local'			 => 'LOCAL',
			'numero'		 => 'SEÇÃO',
			'actions'		 => ''
		];
		$sessoes = (new ImpDao(new Sessao()))->readData($this->search(), true, 'local');

		$data = [
			'header' => $header,
			'body' => Candidato::toArray($sessoes, array_keys($header), ["edit", "delete"]),
		];

		return $json ? json_encode(['dataview' => $data]) : $data;
	}

	public function dataone(?array $params = null):string
	{
		$search = $params ?? ['id' => Route::only('key')];
		$sessao  = (new ImpDao(new Sessao()))->readData($search);
		if($sessao != null){
			return json_encode([
				'dataobj' => $sessao->getPropsValues()
			]);
		}

		return json_encode([
			'message' => Alerts::msg(Alerts::WARNING, 'Dados Não Localizados...')
		]);
	}

	public function erase():string
	{
		$values = Forms::all();

		$sessao = new Sessao();
		$sessao->feeds($values);
		$dao = new ImpDao($sessao);
		$erase = $dao->delData();

		return json_encode([
			'message' => Alerts::msg(
				$erase['code'],
				$erase['status'] ? '' : 'Item Referenciado em Outras Instancias'
			),
			'dataview' => $this->data(false)
		]);
	}

	private function search(): array
	{

		$fields = ['local', 'numero'];

		$values   = Forms::all();
		$insearch = Forms::only('search');
		$isvalid  = Validate::check($values, [], $insearch != null ? 'token' : 'token_search');

		if ($isvalid['status']) {
			if ($insearch != null) {
				return array_filter(Utils::urlsearch($insearch, $fields));
			}

			if(key_exists('token_search', $values)){
				return array_filter(Forms::all($fields));
			}
		}

		return [];
	}
}