<?php
namespace App\Controllers\Pages;

use App\Data\ImpDao;
use App\Models\Candidato;
use App\Utils\Alerts;
use App\Utils\Forms;
use App\Utils\Route;
use App\Utils\Uploads;
use App\Utils\Utils;
use App\Utils\Validate;
use App\Utils\View;

class Candidatos extends Page
{

	public function index(): string
	{
		$params = [
			'form_search' => View::renderView('pages/forms/search/candidatos')
		];
		return $this->getPage('Candidatos', 'pages/candidatos', $params);
	}

	public function submit(): string
	{
		$values   = Forms::all();
		$validate = Validate::check($values, [
			'numero' => 'required',
			'nome'   => 'required'
		]);

		if($validate['status']){
			$candidado = new Candidato();
			$candidado->feeds($values);

			// up photo
			if(isset($_FILES['foto']) && !empty($_FILES['foto'])){
				$up = new Uploads();
				$up->up($_FILES['foto']);
				$upstatus = $up->getstatus();
				if($upstatus['status'][0]){
					$candidado->set('foto', $upstatus['file'][0]);
				}else{
					return json_encode([
						'message' => Alerts::msg(Alerts::WARNING, $upstatus['info'][0]),
					]); 
				}
			}

			$dao = new ImpDao($candidado);
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
			'foto'			 => 'FOTO',
			'nome'			 => 'CANDIDATO',
			'numero'		 => 'NÚMERO',
			'actions'		 => ''
		];
		$candidatos = (new ImpDao(new Candidato()))->readData($this->search(), true, 'nome');

		$data = [
			'header' => $header,
			'body' => Candidato::toArray($candidatos, array_keys($header), ["edit", "delete"]),
		];

		return $json ? json_encode(['dataview' => $data]) : $data;
	}

	public function dataone(?array $params = null):string
	{
		$search = $params ?? ['id' => Route::only('key')];
		$candiadato  = (new ImpDao(new Candidato()))->readData($search);
		if($candiadato != null){
			return json_encode([
				'dataobj' => $candiadato->getPropsValues()
			]);
		}

		return json_encode([
			'message' => Alerts::msg(Alerts::WARNING, 'Dados Não Localizados...')
		]);
	}

	public function erase():string
	{
		$values = Forms::all();

		$candidato = new Candidato();
		$candidato->feeds($values);
		$dao = new ImpDao($candidato);
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

		$fields = ['nome', 'numero'];

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