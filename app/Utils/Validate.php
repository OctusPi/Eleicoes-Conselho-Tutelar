<?php
namespace App\Utils;

class Validate
{

	public static function check(array $values, array $rules, string $idtoken = 'token'): array
	{
		$status   = [];
		$messages = [];

		foreach ($rules as $input => $rule) {
			$diretives = explode('|', $rule);
			if (array_key_exists($input, $values)) {
				foreach ($diretives as $r) {
					if (!self::isvalid($values[$input], $r)) {
						$status[] = false;
						$messages[] = self::strMsg($r);
					}
				}
			}
		}

		$isToken = self::isToken(Utils::at($idtoken, $values));

		return [
			'status' => (!in_array(false, $status) && $isToken['status']),
			'messages' => implode('<br>', array_merge($messages, [$isToken['message']]))
		];
	}

	private static function isvalid($value, $rule): bool
	{
		return match ($rule) {
			'cpf' => preg_match("/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/", $value ?? '') ? true : false,
			'cnpj' => preg_match("/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/", $value ?? '') ? true : false,
			'email' => preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $value ?? '') ? true : false,
			'password' => preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $value ?? '') ? true : false,
			'required' => strlen($value ?? '') ? true : false,
			default => true,
		};
	}

	private static function isToken(?string $value):array
	{
		$isvalid = Forms::validToken($value);
		return [
			'status'  => $isvalid,
			'message' => $isvalid ? '' : 'Token CRF Inválido'
		];
	}

	private static function strMsg(string $rule): string
	{
		return match ($rule) {
			'cpf' => 'CPF inválido',
			'cnpj' => 'CNPJ inválido',
			'email' => 'E-mail inválido',
			'password' => 'Senha não atende aos critérios de Segurança',
			'required' => 'Campo Obrigatório não Informado',
			default => '',
		};
	}
}