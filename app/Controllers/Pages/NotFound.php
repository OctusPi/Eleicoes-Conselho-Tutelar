<?php
namespace App\Controllers\Pages;
use App\Utils\Session;

class NotFound extends Page
{

    public function index():string
    {
        return $this->getPage('Perdeu-se?', 'pages/404', [], false, false);
    }

    public function callBack():string
    {
        return $this->index();
    }
}