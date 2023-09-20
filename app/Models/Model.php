<?php
namespace App\Models;

use App\Utils\Uploads;
use App\Utils\Utils;

abstract class Model implements Imodel
{
	protected int $id;

	public function __construct()
	{
		$this->set('id', 0);
	}

	public function classname(): string
	{
		return static::class;
	}

	public function set(string $attr, mixed $value): void
	{
		$ref = 'set' . ucfirst($attr);

		if (property_exists($this, $attr)) {
			method_exists($this, $ref) ? $this->$ref($value) : $this->$attr = $value;
		}
	}

	public function get(string $attr, bool $brute = false): mixed
	{
		$ref = 'get' . ucfirst($attr);

		if (property_exists($this, $attr)) {
			if (isset($this->$attr)) {
				if($brute){
					return $this->$attr;
				}
				return method_exists($this, $ref) ? $this->$ref() : $this->$attr;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	public function getProps(): array
	{
		return array_keys(get_class_vars(get_class($this)));
	}

	public static function getObrProps(): array
	{
		return [];
	}

	public function getExclusiveProps(): array
	{
		return [];
	}

	public function getValues(?array $attrs = null): array
	{
		$clvalues = get_object_vars($this);
		if ($attrs != null) {
			$values = [];
			foreach ($attrs as $key) {
				if (array_key_exists($key, $clvalues)) {
					$values[$key] = $clvalues[$key];
				}
			}

			return $values;
		}

		return array_values($clvalues);
	}

	public function getPropsValues(): array
	{
		return get_object_vars($this);
	}

	public function getPropsValuesNormalize(): array
	{
		$props = [];
		foreach (array_keys(get_object_vars($this)) as $attr) {
			$props[$attr] = $this->get($attr);
		}
		return $props;
	}

	public function getPropsValuesDB(): array
	{
		$props = [];
		foreach (get_object_vars($this) as $key => $value) {
			$props[$key] = match (gettype($value)) {
				'array', 'object' => serialize($value),
				'boolean' => intval($value),
				default => $value
			};
		}

		return $props;
	}

	

	public static function getJoinProps(): array
	{
		return [];
	}

	public static function getTable(): ?string
	{
		return null;
	}

	public function feeds(?array $params): void
	{
		if ($params != null) {
			foreach ($params as $attr => $value) {
				$value = Utils::isSerial($value) ? unserialize($value) : $value;
				$this->set($attr, $value);
			}
		}
	}

	public function feedsJoin(?array $params): void
	{

		$joins = $this->getJoinProps()['entitys'];
		
		if ($params != null) {
			foreach ($params as $attr => $value) {
				$index = 0;
				//feed main entity
				if(property_exists($this, $attr)){
					if (is_array($value)) {
						$tmpvalue = Utils::isSerial($value[0]) ? unserialize($value[0]) : $value[0];
						$this->set($attr, $tmpvalue);
						$index++;
					} else {
						$tmpvalue = Utils::isSerial($value) ? unserialize($value) : $value;
						$this->set($attr, $tmpvalue);
					}
				}

				//feed join entitys
				foreach ($joins as $entity) {
					if(property_exists($entity, $attr)){
						if (is_array($value)) {
							$index = key_exists($index, $value) ? $index : count($value) - 1;
							$tmpvalue = Utils::isSerial($value[$index]) ? unserialize($value[$index]) : $value[$index];
							$entity->set($attr, $tmpvalue);
							$index++;
						} else {
							$tmpvalue = Utils::isSerial($value) ? unserialize($value) : $value;
							$entity->set($attr, $tmpvalue);
						}
					}
				}
			}
		}

		//set joins im main entity
		foreach ($joins as $key => $entity){
			$this->set($key, $entity);
		}
	}

	public static function toArray(?array $entitys = [], array $fields = [], array $options = []):array
	{
		$toarray = [];
		if($entitys != null){
			foreach ($entitys as $entity) {
				//estructure and normalize props to string values
				$props  = [];
				foreach (array_keys($entity->getPropsValues()) as $key) {
					$props[$key] = $entity->get($key);
				}
				$dados  = $fields != null ? Utils::cleansearch($props, $fields) : $props;
				$action = [];

				foreach ($options as $option) {
					$attrs = match ($option) {
						 "edit"     => ["data-edit"   	=> $entity->get("id")],
						 "delete"   => ["data-delete" 	=> $entity->get("id")],
						 "report"   => ["data-report" 	=> $entity->get("id")],
						 "download" => ["data-href"   	=> Uploads::URL.$entity->get("arquivo")],
						 "viewchamado" 	=> ["data-href" => "?app=historys&key=".$entity->get("id")],
						 default    => []
					};
					$action[$option] = $attrs;
				}

				$toarray[] = array_merge($dados, ['actions'=>$action]);
			}
		}

		return $toarray;
	}
}