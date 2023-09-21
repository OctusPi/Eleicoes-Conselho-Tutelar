<?php
namespace App\Utils;

use App\Utils\Log;
use App\Utils\Utils;

class Uploads
{
	const PATH = __DIR__ . '/../../storage/uploads/';
	const URL  = 'storage/uploads/';

	private array $status;

	public function __construct()
	{
		$this->status = [
			'status' => [],
			'file'	 => [],
			'info'	 => []
		];
	}

	private function getAccept(): array
	{
		return [
			'image/png' => '.png',
			'image/jpeg' => '.jpeg',
			'image/jpg' => '.jpg',
			'image/gif' => '.gif',
			'image/webp' => '.webp',
			'image/svg' => '.svg'
		];
	}

	private function isAccept(string $type): bool
	{
		return array_key_exists($type, $this->getAccept());
	}

	private function getExt(string $type): ?string
	{
		return Utils::at($type, $this->getAccept());
	}

	private function randonName(?string $name = null): string
	{
		return $name == null
			? md5(uniqid(rand(), true))
			: $name;
	}

	private function feedStatus(bool $status, string $name, string $info): void
	{
		$this->status['status'][] = $status;
		$this->status['file'][] = $name;
		$this->status['info'][] = $info;
	}

	private function upMulti(?array $files, bool $randname = true, ?string $fixedrand = null): void
	{
		if ($files != null) {
			foreach ($files['name'] as $key => $value) {

				//stores type and name file in variable to reuse
				$type = $files['type'][$key];
				$name = ($randname ? $this->randonName($fixedrand) : $value) . $this->getExt($type);

				if ($this->isAccept($type)) {
					if (move_uploaded_file($files['tmp_name'][$key], self::PATH . $name)) {
						$this->feedStatus(true, $name, 'Upload realizado com Sucesso');
					} else {
						$this->feedStatus(false, $name, $files['error'][$key]);
					}
				} else {
					$this->feedStatus(false, $name, 'Tipo de arquivo não aceito!');
				}
			}
		}
	}

	private function upSingle(?array $files, bool $randname = true, ?string $fixedrand = null): void
	{
		if ($files != null) {
			//stores type and name file in variable to reuse
			$type = $files['type'];
			$name = ($randname ? $this->randonName($fixedrand) : $files['name']) . $this->getExt($type);

			if ($this->isAccept($type)) {
				if (move_uploaded_file($files['tmp_name'], self::PATH . $name)) {
					$this->feedStatus(true, $name, 'Upload realizado com sucesso!');
				} else {
					$this->feedStatus(false, $name, $files['error']);
				}
			} else {
				$this->feedStatus(false, $name, 'Tipo de arquivo não aceito!'.$type);
			}
		}
	}

	public function up(?array $files, bool $randname = true, ?string $fixedrand = null): ?array
	{
		if ($files['tmp_name'] != null) {
			//exec upload files
			(is_array($files['name'])) ?
				$this->upMulti($files, $randname, $fixedrand) :
				$this->upSingle($files, $randname, $fixedrand);

			//write log to any file attempt upload
			if ($this->status != null):
				foreach ($this->status['status'] as $key => $value) {
					Log::info(($value ? 'SUCCESS: ' : 'ERROR: ') . $this->status['info'][$key]);
				}
			endif;
		} else {
			$this->feedStatus(false, 'Undefined', 'Nemhum arquivo enviado...');
		}

		//return status upload array
		return $this->status;
	}

	public function getstatus(): array
	{
		return $this->status;
	}
}