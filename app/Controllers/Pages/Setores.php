<?php
namespace App\Controllers\Pages;

use App\Utils\Alerts;
use App\Utils\Forms;
use App\Utils\Utils;
use App\Utils\Validate;

class Setores extends Page
{

	public function index(): string
	{
		$params = [
			
		];
		return $this->getPage('Apuracao', 'pages/apuracao', $params);
	}

	public function submit(): string
	{
		

		return json_encode([
			'message' => Alerts::msg(Alerts::WARNING, ""),
		]);
	}

	public function data(bool $json = true): string|array
	{
		$header = [
			'cod'			 => 'CÓDIGO',
			'nome'			 => 'SETOR',
			'endereco'		 => 'ENDEREÇO',
			'telefone'		 => 'TELEFONE',
			'encarregado'	 => 'RESP.',
			'actions'		 => ''
		];
		// $setores = (new ImpDao(new EntitySetor()))->readData($this->search(), true, 'nome');

		$data = [
			'header' => $header,
			// 'body' => EntitySetor::toArray($setores, array_keys($header), ["edit", "delete"]),
		];

		return $json ? json_encode(['dataview' => $data]) : $data;
	}

	public function dataone(?array $params = null):string
	{
		// $search = $params ?? ['id' => Route::only('key')];
		// $setor  = (new ImpDao(new EntitySetor()))->readData($search);
		// if($setor != null){
		// 	return json_encode([
		// 		'dataobj' => $setor->getPropsValues()
		// 	]);
		// }

		// return json_encode([
		// 	'message' => Alerts::msg(Alerts::WARNING, 'Dados Não Localizados...')
		// ]);
		return "";
	}

	public function erase():string
	{
		$values = Forms::all();
		$isvalid = Validate::check($values, [
			'id'		  => 'required',
			'passconfirm' => 'required|password'
		]);

		// if ($isvalid['status']) {
		// 	$checkPass = Security::checkPass(Forms::only('passconfirm'), $this->user);
		// 	if($checkPass){
		// 		$setor = new EntitySetor();
		// 		$setor->feeds($values);
		// 		$dao = new ImpDao($setor);
		// 		$erase = $dao->delData();

		// 		return json_encode([
		// 			'message' => Alerts::msg(
		// 				$erase['code'],
		// 				$erase['status'] ? '' : 'Item Referenciado em Outras Instancias'
		// 			),
		// 			'dataview' => $this->data(false)
		// 		]);
		// 	}

		// 	return json_encode([
		// 		'message' => Alerts::msg(Alerts::WARNING, "Senha de Usuário não Confere")
		// 	]);

		// }

		return json_encode([
			'message' => Alerts::msg(Alerts::WARNING, $isvalid['messages']),
		]);
	}

	private function search(): array
	{

		$fields = ['cod', 'nome', 'zona'];

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