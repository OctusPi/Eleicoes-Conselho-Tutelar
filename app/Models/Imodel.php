<?php
namespace App\Models;

interface Imodel {

    public function classname():string;
    public function set(string $attr, mixed $value):void;
    public function get(string $attr):mixed;
    public function getProps():array;
    public function getExclusiveProps():array;
    public function getValues(?array $attrs = null):array;
    public function getPropsValues():array;
    public function getPropsValuesDB():array;
    public function getPropsValuesNormalize():array;
    public static function getObrProps():array;
    public static function getJoinProps():array;
    public static function getTable(): ?string;
    public function feeds(?array $params):void;
    public function feedsJoin(?array $params):void;
    public static function toArray(?array $entitys = [], array $fields = [], array $options = []):array;
    
}