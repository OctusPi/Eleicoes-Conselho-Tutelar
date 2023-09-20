<?php
namespace App\Controllers\Pages;
use App\Controllers\Components\NavBuild;
use App\Utils\Forms;
use App\Utils\Route;
use App\Utils\View;

abstract class Page{
    const APPNAME    = 'EleiçoesCT2023';
    const APPDESC    = 'Eleiçoes Conselho Tutelar 2023';
    const APPVERSION = 'v1.0.0 Beta';
    const APPURL = 'https://localhost/eleicoesct/';


    public function getConfig():?object{
        $config = __DIR__ . '/../../storage/configs/config.json';
		if (file_exists($config)) {
			return json_decode(file_get_contents($config));
		}

		return null;
    }

    public function getPage(string $title, string $content, array $params = [], bool $hshow = true, bool $fshow = true):string
    {
        //set token exclusive request page
        Forms::setToken();

        $base = [
            
            'title'      => self::APPNAME.' '.$title,
            'header'     => $this->getHeader($hshow),
            'footer'     => $this->getFooter($fshow),
            'content'    => View::renderView($content, $params),
            'modal'      => View::renderView('components/modal_delete'),
            
            'sys_name'   => self::APPNAME,
            'sys_desc'   => self::APPDESC,
            'sys_copy'   => "DTI Campos Sales ".date('Y'),

            'token'      => Forms::getToken(),
            'route_action' => Route::route(['action' => 'submit']),
            'route_search' => Route::route(['action' => 'data']),
            'route_delete' => Route::route(['action' => 'erase']),
        ];

        return View::renderView('layouts/master', $base);
    }

    private function getHeader(bool $hshow = true):string
    {   
        $params  = [
            'sys_version'    => self::APPVERSION,
            'nav_builder'    => NavBuild::build()
        ];

        return $hshow ? View::renderView('layouts/fragments/header', $params) : '';
    }

    public function getFooter(bool $fshow = true):string
    {
        return $fshow ? View::renderView('layouts/fragments/footer') : '';
    }
}