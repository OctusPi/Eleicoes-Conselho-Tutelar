<?php
namespace App\Data;

use App\Models\Model;
use App\Utils\Log;
use Exception;
use PDO;

abstract class Dao
{
	protected Model $model;

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	public function getModel(): Model
	{
		return $this->model;
	}

	public function isExists(): bool
	{
		$sql = 'SELECT * FROM ' . $this->getModel()->getTable() . ' WHERE id = ' . $this->getModel()->get('id');
		return $this->execBool($sql);
	}

	/**
	 * Undocumented function
	 *
	 * @return boolean
	 */
	public function isOnly(): bool
	{
		$params = $this->getModel()->getExclusiveProps();
		$search = $this->getModel()->getValues($params);

		$binds = array_map(function ($item) {
			return $item . '=?'; }, $params);
		$fields = $params != null ? ' WHERE ' . implode(' AND ', $binds) . ' AND ' : '';
		
		$sql = 'SELECT * FROM ' . $this->getModel()->getTable() . $fields . 'id != ' . $this->getModel()->get('id');

		if ($params != null) {
			$food = $this->execFetch($sql, $search);
			$this->getModel()->feeds($food);
			return $food == null;
		}

		return true;
	}

	public function daoIn(): bool
	{
		$params = $this->getModel()->getPropsValuesDB();

		$binds = implode(',', array_keys($params));
		$values = implode(',', array_map(function ($item) {
			return '?'; }, $params));
		$sql = 'INSERT INTO ' . $this->getModel()->getTable() . ' (' . $binds . ') VALUES (' . $values . ')';

		return $this->execFeed($sql, $params);
	}

	public function daoUp(): bool
	{
		$params = $this->getModel()->getPropsValuesDB();
		$binds = implode(',', array_map(function ($item) {
			return $item . '=?'; }, array_keys($params)));
		$sql = 'UPDATE ' . $this->getModel()->getTable() . ' SET ' . $binds . ' WHERE id = ' . $this->getModel()->get('id');

		return $this->execBool($sql, $params);
	}

	public function daoDel(?array $params = null, bool $all = false): bool
	{
		if ($all) {
			$sql = 'DELETE FROM ' . $this->getModel()->getTable();
		} else {
			$where = $params == null
				? 'id = ' . $this->getModel()->get('id')
				: implode(' AND ', array_map(function ($col, $val) {
					return $col . " = '" . $val . "'";
				}, array_keys($params), $params));

			$sql = 'DELETE FROM ' . $this->getModel()->getTable()
				. ' WHERE ' . $where;
		}

		return $this->execBool($sql);
	}

	public function daoGetOne(array $params = [], string $mode = ' AND ', string $columns = '*'): ?Model
	{
		$binds = array_map($this->mapOperator(), array_keys($params), array_values($params));
		$fields = $params != null ? ' WHERE ' . implode($mode, $binds) : '';
		$search = array_map($this->mapWildCard(), array_values($params));

		//make sql
		$sql = 'SELECT ' . $columns . ' FROM ' . $this->getModel()->getTable() . $fields;

		//execute query and feed Model
		$food = $this->execFetch($sql, $search);
		if ($food != null) {
			$this->getModel()->feeds($food);
			return $this->getModel();
		} else {
			return null;
		}
	}

	public function daoGetAll(array $params = [], string $order = '', string $limit = '', string $mode = ' AND ', string $columns = '*'): ?array
	{
		$binds = array_map($this->mapOperator(), array_keys($params), array_values($params));
		$fields = $params != null ? ' WHERE ' . implode($mode, $binds) : '';
		$search = $this->lineWildCard(array_map($this->mapWildCard(), $params));

		//make fields order and limit
		$order = strlen($order) ? ' ORDER BY ' . $order : '';
		$limit = strlen($limit) ? ' LIMIT ' . $limit : '';

		//make sql
		$sql = 'SELECT ' . $columns . ' FROM ' . $this->getModel()->getTable() . $fields . $order . $limit;

		//execute query and get array statment
		$entft = [];
		$fetch = $this->execFetch($sql, $search, true);

		//loop statment end feed Model array
		if ($fetch != null) {
			foreach ($fetch as $food) {
				$nmModel = $this->getModel()->classname();
				$model = new $nmModel();
				$model->feeds($food);
				$entft[$model->get('id')] = $model;
			}
		}
		return $entft;
	}

	public function daoGetJoinOne(array $params = [], string $mode = ' AND ', string $columns = '*'): ?Model
	{
		if ($this->getModel()->getJoinProps() != null) {
			//make join values and concatene tables
			$models = $this->getModel()->getJoinProps()['Models'];
			$joins = $this->getModel()->getJoinProps()['joins'];

			$mapfields = array_map($this->mapJoin(), $models, $joins);
			$maptabs = array_map($this->mapOn(), $models, $mapfields);
			$innerjoin = ' INNER JOIN ' . implode(' INNER JOIN ', $maptabs);

			//map operators sql where
			$binds = array_map($this->mapOperator(), array_keys($params), array_values($params));

			//concatene binds in fields with mode search
			$fields = $params != null ? ' WHERE ' . implode($mode, $binds) : '';
			$search = $this->lineWildCard(array_map($this->mapWildCard(), $params));

			//make sql
			$sql = 'SELECT ' . $columns . ' FROM ' . $this->getModel()->getTable() . $innerjoin . $fields;

			//execute query and feed Model
			$food = $this->execFetch($sql, $search);
			if ($food != null) {
				$this->getModel()->feedsJoin($food);
				return $this->getModel();
			}
		}

		return null;
	}

	public function daoGetJoinAll(array $params = [], string $order = '', string $limit = '', string $mode = ' AND ', string $columns = '*'): ?array
	{

		if ($this->getModel()->getJoinProps() != null) {

			//make join values and concatene tables
			$models = $this->getModel()->getJoinProps()['Models'];
			$joins   = $this->getModel()->getJoinProps()['joins'];

			$mapfields = array_map($this->mapJoin(), $models, $joins);
			$maptabs   = array_map($this->mapOn(), $models, $mapfields);
			$innerjoin = ' INNER JOIN ' . implode(' INNER JOIN ', $maptabs);

			//map operators sql where
			$binds = array_map($this->mapOperator(), array_keys($params), array_values($params));

			//concatene binds in fields with mode search
			$fields = $params != null ? ' WHERE ' . implode($mode, $binds) : '';
			$search = $this->lineWildCard(array_map($this->mapWildCard(), $params));

			//make fields order and limit
			$order = strlen($order) ? ' ORDER BY ' . $order : '';
			$limit = strlen($limit) ? ' LIMIT ' . $limit : '';

			//make sql
			$sql = 'SELECT ' . $columns . ' FROM ' . $this->getModel()->getTable() . $innerjoin . $fields . $order . $limit;

			//loop statment end feed Model array
			$entft = [];
			$fetch = $this->execFetch($sql, $search, true);
			if ($fetch != null) {
				foreach ($fetch as $food) {
					$nmModel = $this->getModel()->classname();
					$model = new $nmModel();
					$model->feedsJoin($food);
					$entft[$model->get('id')] = $model;
				}
			}

			return $entft;
		}

		return null;
	}

	public function daoGetSUM(string $sum, array $params, string $mode = ' AND '): float|int
	{
		//create binds and fields dynamic by Model and params search
		$binds = array_map(function ($key, $value) {
			return $key . "='" . $value . "'"; }, array_keys($params), array_values($params));
		$fields = $params != null ? ' WHERE ' . implode($mode, $binds) : '';

		//make sql
		$sql = 'SELECT SUM(' . $sum . ') FROM ' . $this->getModel()->getTable() . $fields;

		//execute query and feed Model
		return $this->execSum($sql);
	}

	private function lineWildCard(?array $mapwild, array $merge = []): array
	{
		if ($mapwild != null) {

			foreach ($mapwild as $map) {
				if (is_array($map)) {
					$merge = array_merge($merge, $map);
				} else {
					$merge[] = $map;
				}
			}
		}

		return $merge;
	}

	private function execBool(string $sql, array $where = []): bool
	{
		//default return method
		$exec = false;

		try {
			$connDB = Conn::openConn();
			if($connDB != null){
				$where = $where != null ? array_values($where) : [];
				$execQuery = $connDB->prepare($sql);
				$execQuery->execute($where);
				$exec = $execQuery->rowCount() > 0;
			}
		} catch (Exception $e) {
			Log::warning($e->getMessage());
		} finally {
			Conn::closeConn();
		}

		return $exec;
	}

	private function execFetch(string $sql, array $params = [], bool $all = false): ?array
	{
		$exec = [];

		try {
			$connDB = Conn::openConn();
			if($connDB != null){
				$params = $params != null ? array_values($params) : [];
				$execQuery = $connDB->prepare($sql);
				$execQuery->execute($params);
				
				if ($execQuery->rowCount() > 0) {
					$exec = $all ? $execQuery->fetchAll(PDO::FETCH_NAMED) : $execQuery->fetch(PDO::FETCH_NAMED);
				}
			}
		} catch (Exception $e) {
			Log::warning($e->getMessage());
			throw new Exception($e->getMessage());
		} finally {
			Conn::closeConn();
		}

		return $exec;
	}

	private function execFeed(string $sql, array $params = []): bool
	{
		//default return method
		$exec = false;

		try {
			$connDB = Conn::openConn();
			if($connDB != null){
				$params = $params != null ? array_values($params) : [];
				$execQuery = $connDB->prepare($sql);
				$execQuery->execute($params);
				$exec = $execQuery->rowCount() > 0;

				if ($exec) {
					$this->getModel()->set('id', $connDB->lastInsertId());
				}
			}

		} catch (Exception $e) {
			Log::warning($e->getMessage());
			throw new Exception($e->getMessage());
		} finally {
			Conn::closeConn();
		}

		return $exec;
	}

	private function execSum(string $sql): float|int
	{
		//default return method
		$exec = 0;

		try {
			$connDB = Conn::openConn();
			if($connDB != null){
				$execQuery = $connDB->query($sql);
				$exec = $execQuery->fetchColumn() ?? 0;
			}
		} catch (Exception $e) {
			Log::warning($e->getMessage());
		} finally {
			Conn::closeConn();
		}

		return $exec;
	}

	private function mapOperator(): callable
	{
		$defineOperator = function ($item, $value) {
			$operator = match (true) {
				is_array($value) => ' BETWEEN ? AND ? ',
				is_numeric($value) => ' = ? ',
				is_string($value) => ' LIKE ? ',
				default => ' = ? '
			};

			return $item . $operator;
		};

		return $defineOperator;
	}

	private function mapWildCard(): callable
	{
		$defineWC = function ($item) {
			return match (true) {
				is_numeric($item) => $item,
				is_string($item) => '%' . $item . '%',
				default => $item
			};
		};

		return $defineWC;
	}

	private function mapJoin(): callable
	{
		$mapJoin = function ($model, $campos) {
			//initialize arrays to insert rigth values to join operation and define primary table
			$binds = [];
			$join  = [];
			$tabprimary = $this->getModel()->getTable();

			//concatene tables with field by position in array
			foreach ($campos as $key => $campo) {
				if ($key == 0 || $key % 2 == 0) {
					$binds[] = $tabprimary . '.' . $campo;
				} else {
					$binds[] = $model->getTable() . '.' . $campo;
				}
			}

			//unify fields and feed array join
			foreach (array_keys($binds) as $k) {
				if ($k > 0 && $k % 2 != 0) {
					$join[] = $binds[$k - 1] . ' = ' . $binds[$k];
				}
			}

			return implode(' AND ', $join);
		};

		return $mapJoin;
	}

	private function mapOn(): callable
	{
		$mapOn = function ($model, $campos) {
			return $model->getTable() . ' ON ' . $campos;
		};

		return $mapOn;
	}
}