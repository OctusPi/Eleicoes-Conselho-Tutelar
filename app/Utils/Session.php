<?php
namespace App\Utils;

use App\Data\ImpDao;
use Exception;
use App\Controllers\Pages\Page;
use App\Utils\Log;
use App\Utils\Utils;
use App\Models\EntityUser;

class Session
{
	const SISNAME = Page::APPNAME;
	const TIME = 3600;

	private string $sessionName;
	private string $sessionUnq;
	private string $sessionTime;
	private string $sessionUid;
	private string $sessionPid;

	public function __construct()
	{
		$this->sessionName = md5(self::SISNAME . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
		$this->sessionUnq = md5(self::SISNAME . 'IDUNQ');
		$this->sessionTime = md5(self::SISNAME . 'TIME');
		$this->sessionUid = md5(self::SISNAME . 'USER');
		$this->sessionPid = md5(self::SISNAME . 'PASS');

		$this->initialize();
	}

	private function initialize(): void
	{
		if (!$this->isActive()) {
			session_name($this->sessionName);
			session_cache_limiter('no-cache');
		}

		@session_start();
	}

	private function isActive(): bool
	{
		return (session_status() == PHP_SESSION_ACTIVE);
	}

	private function isCreate(): bool
	{
		$sessions = [$this->sessionUnq, $this->sessionUid, $this->sessionPid, $this->sessionTime];

		foreach ($sessions as $s) {
			if (!isset($_SESSION[$s])) {
				return false;
			}
		}

		return true;
	}

	private function isAuth(?EntityUser $usuario): bool
	{
		return (
			(Utils::at($this->sessionUnq, $_SESSION) == $this->sessionName) &&
			(Utils::at($this->sessionUid, $_SESSION) == $usuario->get('uid')) &&
			(Utils::at($this->sessionPid, $_SESSION) == $usuario->get('pid'))
		);
	}

	private function inTime(): bool
	{
		$sTime = Utils::at($this->sessionTime, $_SESSION) ?? 0;

		if ($this->isCreate() && $sTime > time()) {
			$_SESSION[$this->sessionTime] = time() + self::TIME;
			return true;
		} else {
			return false;
		}
	}

	public function isAllowed(?EntityUser $usuario): bool
	{
		return (
			($usuario != null) &&
			($this->isActive()) &&
			($this->isCreate()) &&
			($this->isAuth($usuario)) &&
			($this->inTime())
		);
	}

	public function create(EntityUser $usuario): bool
	{

		if ($this->isActive()) {
			$_SESSION[$this->sessionUnq] = $this->sessionName;
			$_SESSION[$this->sessionUid] = $usuario->get('uid');
			$_SESSION[$this->sessionPid] = $usuario->get('pid');
			$_SESSION[$this->sessionTime] = time() + self::TIME;

			return true;
		}

		return false;
	}

	public function destroy(): void
	{
		try {
			session_destroy();
			unset($_SESSION);
		} catch (Exception $e) {
			Log::warning('Erro: Falha ao finalizar sessão - ' . $e->getMessage());
		}
	}

	public function getUser(): ?EntityUser
	{
		$params = [
			'uid' => Utils::at($this->sessionUid, $_SESSION),
			'pid' => Utils::at($this->sessionPid, $_SESSION) 
		];

		return (new ImpDao(new EntityUser()))->readData($params);
	}

}