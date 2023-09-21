<?php
namespace App\Controllers\Pages;

use App\Data\ImpDao;
use App\Models\Abstencao;
use App\Models\Candidato;
use App\Models\Sessao;
use App\Utils\Alerts;
use App\Utils\Forms;
use App\Utils\Html;
use App\Utils\Route;
use App\Utils\Utils;
use App\Utils\Validate;

class Apuracao extends Page
{
	private ?array $candidatos;

	public function __construct()
	{
		$this->candidatos = (new ImpDao(new Candidato()))->readData(all:true, order:'nome') ?? [];
	}

	public function index(): string
	{
		$sessoes = (new ImpDao(new Sessao()))->readData(all:true, order:'numero'); 
		$params  = [
			'form_sessoes' 	  => Html::selectObj($sessoes, "id", ["numero", "local"]),
			'form_candidatos' => Html::inputvoto($this->candidatos)
		];
		return $this->getPage('Apuracao', 'pages/apuracao', $params);
	}

	public function submit(): string
	{
		$values   = Forms::all();
		$validate = Validate::check($values, [
			'sessao'  => 'required',
			'nulos'   => 'required',
			'brancos' => 'required',
		]);

		if($validate['status']){
			
			$abstencao = new ImpDao(new Abstencao());
			$abstencao -> readData(['sessao'=>$values['sessao']]);
			$abstencao -> getModel()->feeds($values);
			$abstencao -> writeData();
		
			$success = [];
			foreach ($this->candidatos as $candidato) {
				$apuracao = new ImpDao(new \App\Models\Apuracao());
				$apuracao -> readData(['candidato' => $candidato->get('id'), 'sessao' => $values['sessao']]);
				$apuracao -> getModel()->set('candidato', $candidato->get('id'));
				$apuracao -> getModel()->set('sessao', $values['sessao']);
				$apuracao -> getModel()->set('votos', $values['votos_'.$candidato->get('id')]);
				$write = $apuracao ->writeData();

				$success[] = $write['status'];
			}

			if(in_array(false, $success)){
				return json_encode([
					'message' => Alerts::msg(Alerts::WARNING, "Falha ao gravar alguns dados..."),
				]);
			}else{
				return json_encode([
					'message' => Alerts::msg(Alerts::SUCCESS, "Votos gravados com sucesso..."),
				]);
			}
		}

		return json_encode([
			'message' => Alerts::msg(Alerts::WARNING, "Dados inválidos..."),
		]);
	}

	public function dataone(?array $params = null):string
	{
		$search = $params ?? ['sessao' => Route::only('key')];
		$abstencao = (new ImpDao(new Abstencao()))->readData($search);
		$apuracao = (new ImpDao(new \App\Models\Apuracao()))->readData($search, true);
		$normalize = [];
		if($abstencao != null && $apuracao != null){
			$normalize['sessao']  = $abstencao->get('sessao');
			$normalize['brancos'] = $abstencao->get('brancos');
			$normalize['nulos']   = $abstencao->get('nulos');

			foreach ($apuracao as $ap) {
				$normalize['votos_'.$ap->get('candidato')] = $ap->get('votos');
			}
		}

		return json_encode([
			'dataobj' => $normalize
		]);

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