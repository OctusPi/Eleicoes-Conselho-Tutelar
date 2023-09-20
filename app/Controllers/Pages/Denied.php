<?php
namespace App\Controllers\Pages;
use App\Utils\Session;

class Denied extends Page
{
    

    public function index():string
    {
        return $this->getPage('Acesso Nao Autorizado', 'pages/401', [], false, false);
    }

    public function callBack():string
    {
        return (new NotFound())->index();
    }
}