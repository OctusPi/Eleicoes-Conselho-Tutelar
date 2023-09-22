<?php
namespace App\Models;

class Apuracao extends Model
{
    protected int|string|Cadidato $candidato;
    protected int|string|Sessao $sessao;
    protected int $votos;

    public static function getTable():string
    {
        return "elct_apuracoes";
    }

    public function getExclusiveProps(): array
	{
		return ['candidato', 'sessao'];
	}

    public static function getObrProps(): array
	{
		return ['candidato', 'sessao', 'votos'];
	}

    public static function getJoinProps(): array
	{
		return [
			'entitys' => [
                'cadidato'     => new Candidato(),
                'sessao'       => new Sessao()
            ],
			'joins'   => [
                ['candidato', 'id'],
                ['sessao', 'id']
            ]
		];
	}
}