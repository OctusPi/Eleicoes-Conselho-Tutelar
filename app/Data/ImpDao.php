<?php
namespace App\Data;

use App\Models\Model;
use Exception;
use App\Utils\Alerts;
use App\Data\Dao;

class ImpDao extends Dao
{
	public function __construct(Model $model)
	{
		parent::__construct($model);
	}

	public function writeData(): array
	{
		$isOnly = $this->isOnly();
		if ($isOnly) {
			try {
				$write = $this->isExists() ? $this->daoUp() : $this->daoIn();
				return [
					'code' => Alerts::SUCCESS,
					'status' => $write
				];
			} catch (Exception $e) {
				return [
					'code' => Alerts::DANGER,
					'status' => false
				];
			}
		} else {
			return [
				'code' => Alerts::WARNING,
				'status' => false
			];
		}
	}

	public function readData(array $params = [], bool $all = false, string $order = '', string $limit = '', string $mode = ' AND ', string $columns = '*'): null|Model|array
	{
		return $all ?
			$this->daoGetAll($params, $order, $limit, $mode, $columns) :
			$this->daoGetOne($params, $mode, $columns);
	}

	public function readDataJoin(array $params = [], bool $all = false, string $order = '', string $limit = '', string $mode = ' AND ', string $columns = '*'): null|Model|array
	{
		return $all ?
			$this->daoGetJoinAll($params, $order, $limit, $mode, $columns) :
			$this->daoGetJoinOne($params, $mode, $columns);
	}

	public function readSUM(string $sum, array $params, string $mode = ' AND '): float|int
	{
		return $this->daoGetSUM($sum, $params, $mode);
	}

	public function delData(?array $params = null, bool $all = false): array
	{
		$delete = $this->daoDel($params, $all);
		return [
			'code' => $delete ? Alerts::SUCCESS : Alerts::WARNING,
			'status' => $delete
		];
	}
}