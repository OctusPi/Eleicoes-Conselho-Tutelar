<?php
namespace App\Data;

use App\Modes\Entity;

interface IDao
{

    public function getEntity():Entity;

    public function isExists():bool;

    public function isOnly():bool;

    public function daoIn():bool;

    public function daoUp():bool;

    public function daoDel(?array $params = null, bool $all = false):bool;

    public function daoGetOne(array $params = [], string $mode = ' AND ', string $columns = '*'):?Entity;

    public function daoGetAll(array $params = [], string $order = '', string $limit = '', string $mode = ' AND ', string $columns = '*'):?array;

    public function daoGetJoinOne(array $params = [], string $mode = ' AND ', string $columns = '*'):?Entity;

    public function daoGetJoinAll(array $params = [], string $order = '', string $limit = '', string $mode = ' AND ', string $columns = '*'):?array;

}