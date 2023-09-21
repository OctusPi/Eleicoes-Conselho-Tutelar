<?php
namespace App\Models;

class Abstencao extends Model
{
    protected int|string|Sessao $sessao;
    protected int $nulos;
    protected int $brancos;

    public static function getTable():string
    {
        return "elct_abstencoes";
    }

    public function getExclusiveProps(): array
	{
		return ['sessao'];
	}

    public static function getObrProps(): array
	{
		return ['sessao'];
	}
}