<?php
namespace App\Models;

class Cadidato extends Model
{
    protected string $nome;
    protected string $numero;
    protected string $foto;

    public static function getTable():string
    {
        return "elct_candidatos";
    }

    public function getExclusiveProps(): array
	{
		return ['numero'];
	}

    public static function getObrProps(): array
	{
		return ['nome', 'numero'];
	}
}