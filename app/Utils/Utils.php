<?php
namespace App\Utils;

use App\Models\Entity;

class Utils
{
	public static function at(null|int|string $key, ?array $dados): mixed
	{
		if ($key != null && $dados != null) {
			return array_key_exists($key, $dados) ? $dados[$key] : null;
		} else {
			return null;
		}
	}

	public static function ob(?string $attr, ?Entity $object): mixed
	{
		return $object != null ? $object->get($attr) : null;
	}

	public static function isSerial(mixed $data, bool $strict = true): bool
	{
		// If it isn't a string, it isn't serialized.
		if (!is_string($data)) {
			return false;
		}
		$data = trim($data);
		if ('N;' === $data) {
			return true;
		}
		if (strlen($data) < 4) {
			return false;
		}
		if (':' !== $data[1]) {
			return false;
		}
		if ($strict) {
			$lastc = substr($data, -1);
			if (';' !== $lastc && '}' !== $lastc) {
				return false;
			}
		} else {
			$semicolon = strpos($data, ';');
			$brace = strpos($data, '}');
			// Either ; or } must exist.
			if (false === $semicolon && false === $brace) {
				return false;
			}
			// But neither must be in the first X characters.
			if (false !== $semicolon && $semicolon < 3) {
				return false;
			}
			if (false !== $brace && $brace < 4) {
				return false;
			}
		}
		$token = $data[0];
		switch ($token) {
			case 's':
				if ($strict) {
					if ('"' !== substr($data, -2, 1)) {
						return false;
					}
				} elseif (false === strpos($data, '"')) {
					return false;
				}
			// Or else fall through.
			case 'a':
			case 'O':
			case 'E':
				return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
			case 'b':
			case 'i':
			case 'd':
				$end = $strict ? '$' : '';
				return (bool) preg_match("/^{$token}:[0-9.E+-]+;$end/", $data);
		}

		return false;
	}

	public static function cleansearch(array $search, array $fields): array
	{
		$arrsearch = [];

		if ($search != null) {
			foreach ($search as $key => $item) {
				if(in_array($key, $fields)){
					$arrsearch[$key] = $item;
				}
			}
		}

		return $arrsearch;
	}

	public static function urlsearch(?string $search, array $fields):array
    {
        $arrsearch = [];
            
        if($search != null){
            $temp = explode('&', $search);
            foreach($temp as $item){
                $tempitem = explode('=', $item);
                $arrsearch[$tempitem[0]] = $tempitem[1];
            }
        }

        return self::cleansearch($arrsearch, $fields);
    }

	public static function prefixsearch(?array $search, string $prefix):array
    {
        $presearch = [];
		foreach ($search as $key => $value) {
			$presearch[$prefix.'.'.$key] = $value;
		}
		 return $presearch;
    }

	public static function selectObj(?array $objects, string $index, array $values):array
	{
		$toarray = [];
		if($objects != null){
			foreach ($objects as $object) {
				$opt = [];
				foreach ($values as $value) {
					$opt[] = $object->get($value);
				}
				$toarray[$object->get($index)] = implode(' - ', $opt);
			}
		}
		return $toarray;
	}
}