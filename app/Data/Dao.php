<?php
namespace App\Data;

use App\Models\Entity;
use App\Utils\Log;
use Exception;
use PDO;

abstract class Dao
{
	protected Entity $entity;

	public function __construct(Entity $entity)
	{
		$this->entity = $entity;
	}

	public function getEntity(): Entity
	{
		return $this->entity;
	}

	public function isExists(): bool
	{
		$sql = 'SELECT * FROM ' . $this->getEntity()->getTable() . ' WHERE id = ' . $this->getEntity()->get('id');
		return $this->execBool($sql);
	}

	/**
	 * Undocumented function
	 *
	 * @return boolean
	 */
	public function isOnly(): bool
	{
		$params = $this->getEntity()->getExclusiveProps();
		$search = $this->getEntity()->getValues($params);

		$binds = array_map(function ($item) {
			return $item . '=?'; }, $params);
		$fields = $params != null ? ' WHERE ' . implode(' AND ', $binds) . ' AND ' : '';
		
		$sql = 'SELECT * FROM ' . $this->getEntity()->getTable() . $fields . 'id != ' . $this->getEntity()->get('id');

		if ($params != null) {
			$food = $this->execFetch($sql, $search);
			$this->getEntity()->feeds($food);
			return $food == null;
		}

		return true;
	}

	public function daoIn(): bool
	{
		$params = $this->getEntity()->getPropsValuesDB();

		$binds = implode(',', array_keys($params));
		$values = implode(',', array_map(function ($item) {
			return '?'; }, $params));
		$sql = 'INSERT INTO ' . $this->getEntity()->getTable() . ' (' . $binds . ') VALUES (' . $values . ')';

		return $this->execFeed($sql, $params);
	}

	public function daoUp(): bool
	{
		$params = $this->getEntity()->getPropsValuesDB();
		$binds = implode(',', array_map(function ($item) {
			return $item . '=?'; }, array_keys($params)));
		$sql = 'UPDATE ' . $this->getEntity()->getTable() . ' SET ' . $binds . ' WHERE id = ' . $this->getEntity()->get('id');

		return $this->execBool($sql, $params);
	}

	public function daoDel(?array $params = null, bool $all = false): bool
	{
		if ($all) {
			$sql = 'DELETE FROM ' . $this->getEntity()->getTable();
		} else {
			$where = $params == null
				? 'id = ' . $this->getEntity()->get('id')
				: implode(' AND ', array_map(function ($col, $val) {
					return $col . " = '" . $val . "'";
				}, array_keys($params), $params));

			$sql = 'DELETE FROM ' . $this->getEntity()->getTable()
				. ' WHERE ' . $where;
		}

		return $this->execBool($sql);
	}

	public function daoGetOne(array $params = [], string $mode = ' AND ', string $columns = '*'): ?Entity
	{
		$binds = array_map($this->mapOperator(), array_keys($params), array_values($params));
		$fields = $params != null ? ' WHERE ' . implode($mode, $binds) : '';
		$search = array_map($this->mapWildCard(), array_values($params));

		//make sql
		$sql = 'SELECT ' . $columns . ' FROM ' . $this->getEntity()->getTable() . $fields;

		//execute query and feed entity
		$food = $this->execFetch($sql, $search);
		if ($food != null) {
			$this->getEntity()->feeds($food);
			return $this->getEntity();
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
		$sql = 'SELECT ' . $columns . ' FROM ' . $this->getEntity()->getTable() . $fields . $order . $limit;

		//execute query and get array statment
		$entft = [];
		$fetch = $this->execFetch($sql, $search, true);

		//loop statment end feed entity array
		if ($fetch != null) {
			foreach ($fetch as $food) {
				$nmentity = $this->getEntity()->classname();
				$entity = new $nmentity();
				$entity->feeds($food);
				$entft[$entity->get('id')] = $entity;
			}
		}
		return $entft;
	}

	public function daoGetJoinOne(array $params = [], string $mode = ' AND ', string $columns = '*'): ?Entity
	{
		if ($this->getEntity()->getJoinProps() != null) {
			//make join values and concatene tables
			$entitys = $this->getEntity()->getJoinProps()['entitys'];
			$joins = $this->getEntity()->getJoinProps()['joins'];

			$mapfields = array_map($this->mapJoin(), $entitys, $joins);
			$maptabs = array_map($this->mapOn(), $entitys, $mapfields);
			$innerjoin = ' INNER JOIN ' . implode(' INNER JOIN ', $maptabs);

			//map operators sql where
			$binds = array_map($this->mapOperator(), array_keys($params), array_values($params));

			//concatene binds in fields with mode search
			$fields = $params != null ? ' WHERE ' . implode($mode, $binds) : '';
			$search = $this->lineWildCard(array_map($this->mapWildCard(), $params));

			//make sql
			$sql = 'SELECT ' . $columns . ' FROM ' . $this->getEntity()->getTable() . $innerjoin . $fields;

			//execute query and feed entity
			$food = $this->execFetch($sql, $search);
			if ($food != null) {
				$this->getEntity()->feedsJoin($food);
				return $this->getEntity();
			}
		}

		return null;
	}

	public function daoGetJoinAll(array $params = [], string $order = '', string $limit = '', string $mode = ' AND ', string $columns = '*'): ?array
	{

		if ($this->getEntity()->getJoinProps() != null) {

			//make join values and concatene tables
			$entitys = $this->getEntity()->getJoinProps()['entitys'];
			$joins   = $this->getEntity()->getJoinProps()['joins'];

			$mapfields = array_map($this->mapJoin(), $entitys, $joins);
			$maptabs   = array_map($this->mapOn(), $entitys, $mapfields);
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
			$sql = 'SELECT ' . $columns . ' FROM ' . $this->getEntity()->getTable() . $innerjoin . $fields . $order . $limit;

			//loop statment end feed entity array
			$entft = [];
			$fetch = $this->execFetch($sql, $search, true);
			if ($fetch != null) {
				foreach ($fetch as $food) {
					$nmentity = $this->getEntity()->classname();
					$entity = new $nmentity();
					$entity->feedsJoin($food);
					$entft[$entity->get('id')] = $entity;
				}
			}

			return $entft;
		}

		return null;
	}

	public function daoGetSUM(string $sum, array $params, string $mode = ' AND '): float|int
	{
		//create binds and fields dynamic by entity and params search
		$binds = array_map(function ($key, $value) {
			return $key . "='" . $value . "'"; }, array_keys($params), array_values($params));
		$fields = $params != null ? ' WHERE ' . implode($mode, $binds) : '';

		//make sql
		$sql = 'SELECT SUM(' . $sum . ') FROM ' . $this->getEntity()->getTable() . $fields;

		//execute query and feed entity
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
					$this->getEntity()->set('id', $connDB->lastInsertId());
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
		$mapJoin = function ($entity, $campos) {
			//initialize arrays to insert rigth values to join operation and define primary table
			$binds = [];
			$join  = [];
			$tabprimary = $this->getEntity()->getTable();

			//concatene tables with field by position in array
			foreach ($campos as $key => $campo) {
				if ($key == 0 || $key % 2 == 0) {
					$binds[] = $tabprimary . '.' . $campo;
				} else {
					$binds[] = $entity->getTable() . '.' . $campo;
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
		$mapOn = function ($entity, $campos) {
			return $entity->getTable() . ' ON ' . $campos;
		};

		return $mapOn;
	}
}