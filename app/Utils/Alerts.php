<?php
namespace App\Utils;

class Alerts
{
	const SUCCESS = 'success';
	const WARNING = 'warning';
	const DANGER = 'danger';
	const INFO = 'info';

	public static function msg(string $type, string $info = ''): array
	{
		$logmsg = $info . ' - '.$_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];

		switch ($type) {
			case self::WARNING:
				Log::warning($logmsg);
				break;
			case self::DANGER:
				Log::critical($logmsg);
				break;
			case self::INFO:
				Log::notice($logmsg);
				break;
			default:
				break;
		}

		return [
			'type' => $type,
			'info' => $info
		];
	}

	public static function infoDAO(string $code):string
	{
		return match ($code) {
			Alerts::DANGER  => 'Falha Sistemica',
			Alerts::WARNING => 'Tentativa de duplicação de Dados!',
			default 		=> ''
	   };
	}
}