<?php
namespace App\Controllers\Pages;

use App\Data\ImpDao;
use App\Models\Abstencao;
use App\Models\Apuracao;
use App\Models\Candidato;
use App\Models\Sessao;
use App\Utils\Utils;

class Dashboard extends Page
{

	public function index(): string
	{
		return $this->getPage('Dashboard', 'pages/dashboard');
	}

	public function data():string
	{
		//initialize vars to increment
		$votos   = 0;
		$brancos = 0;
		$nulos   = 0;
		$contagem = [];
		$votacao  = [];

		//request SQL
		$sessoes    = count((new ImpDao(new Sessao()))->readData(all:true) ?? []);
		$abstencoes = (new ImpDao(new Abstencao()))->readData(all:true) ?? [];
		$apuracoes  = (new ImpDao(new Apuracao()))->readData(all:true) ?? [];
		$candidatos = (new ImpDao(new Candidato()))->readData(all:true, order:'nome') ?? [];

		//Map params cadidate
		$nomes   = Utils::selectObj($candidatos, 'id', ['nome']);
		$fotos   = Utils::selectObj($candidatos, 'id', ['foto']);
		$numeros = Utils::selectObj($candidatos, 'id', ['numero']);

		//sum votes to count and count by candidate
		foreach ($apuracoes as $ap) {
			$votos += $ap->get('votos');

			if(key_exists($ap->get('candidato'), $contagem)){
				$contagem[$ap->get('candidato')] = $contagem[$ap->get('candidato')] += $ap->get('votos');
			}else{
				$contagem[$ap->get('candidato')] = $ap->get('votos');
			}
		}

		//sort by votes
		arsort($contagem);

		//make data to return json votacao
		foreach ($contagem as $key => $value) {
			$votacao[] = [
				'foto' => $fotos[$key],
				'nome' => $nomes[$key].' - '.$numeros[$key],
				'votos'  => $value
			];
		}
		
		//sum brancos & nulos
		foreach ($abstencoes as $ab) {
			$brancos += $ab->get('brancos');
			$nulos   += $ab->get('nulos');
		}
		
		$sintenco = [
			'urnas' => $sessoes.'/'.count($abstencoes),
			'votos' => $votos,
			'brancos' => $brancos,
			'nulos' => $nulos
		];


		return json_encode([
			'sintetico' => $sintenco,
			'votacao'   => $votacao
		]);
	}
}