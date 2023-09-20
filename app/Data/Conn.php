<?php
namespace App\Data;

use App\Utils\Log;
use Exception;
use PDO;
use PDOException;
use Throwable;

class Conn
{
	private static ?PDO $conn = null;

	public static function openConn(): ?PDO
	{
		$config = __DIR__.'/../../config/connection.php';

		if(file_exists($config)){
			if (self::$conn == null ) {
				require_once($config);
				
				try {
					self::$conn = new PDO(TYPE.':host='.HOST.';port='.PORT.';dbname='.DATA, USER, PASS);
	
				} catch (PDOException $e) {
					Log::critical($e->getMessage());
					die('Falha ao conectar com banco de dados...');
				}
			}
		}else{
			die('Falha DB credenciais ausentes...');
		}

		return self::$conn;
	}

	public static function closeConn(): void
	{
		if (self::$conn != null) {
			self::$conn = null;
		}
	}
}