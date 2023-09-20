<?php
namespace App\Models;

class Sessao extends Model
{
    protected string $local;
    protected string $numero;

    public static function getTable():string
    {
        return "elct_sessoes";
    }

    public function getExclusiveProps(): array
	{
		return ['numero'];
	}

    public static function getObrProps(): array
	{
		return ['local', 'numero'];
	}
}