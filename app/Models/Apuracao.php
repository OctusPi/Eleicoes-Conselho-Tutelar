<?php
namespace App\Models;

class Sessao extends Model
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
}