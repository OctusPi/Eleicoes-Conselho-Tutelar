<?php
namespace App\Utils;

use App\Utils\Utils;
use App\Utils\Security;
use App\Controllers\Pages\Page;

class Forms
{
	private static string $nmtoken = Page::APPNAME . '69232f6d364071ea4a9ea8c38d023919';
	private static string $vltoken;

	public static function setToken(): void
	{
		self::$vltoken = md5(uniqid(rand(), true));
		setcookie(
			self::$nmtoken, //name
			self::$vltoken,
				//value
			[
				'expires' => 0,
				'path' => '/',
				'secure' => false,
				'httponly' => false,
				'samesite' => 'Strict',
			]
		);
	}

	public static function getToken(): string
	{
		return isset(self::$vltoken) ? self::$vltoken : '';
	}

	public static function validToken(?string $token): bool
	{
		return Utils::at(self::$nmtoken, $_COOKIE) == $token;
	}

	public static function all(?array $fkeys = null): array
	{
		$post = [];
		if (isset($_POST) && !empty($_POST)) {
			foreach ($_POST as $key => $value) {
				if ($fkeys == null) {
					$post[Security::sanitize($key)] = Security::sanitize($value);
				} else {
					if (in_array($key, $fkeys)) {
						$post[Security::sanitize($key)] = Security::sanitize($value);
					}
				}
			}
		}
		return $post;
	}

	public static function only(string $key): mixed
	{
		if (isset($_POST) && !empty($_POST)) {
			if (key_exists($key, $_POST)) {
				return Security::sanitize($_POST[$key]);
			}
		}
		return null;
	}
}