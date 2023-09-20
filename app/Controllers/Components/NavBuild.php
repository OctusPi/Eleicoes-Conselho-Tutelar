<?php
namespace App\Controllers\Components;
use App\Utils\Route;
use App\Utils\View;

class NavBuild
{
    public static function build():?string
    {
        $links = "";
        foreach (self::navstruct() as $key => $nav) {
            $links .= View::renderView('layouts/fragments/navs/main-nav-item', $nav);
        }

        return View::renderView('layouts/fragments/navs/main-nav', ['nav_itens' => $links]);
    }

    private static function navstruct():array
    {
        function is_active(string $app){
            return $app == Route::only('app') ? 'active' : '';
        }

        return [
            'dashboard' => [
                'nav_active' => is_active('dashboard'),
                'nav_link'   => "?app=dashboard",
                'nav_icon'   => "bi-menu-down",
                'nav_title'  => "Dashboard"
            ],
            'apuracoes' => [
                'nav_active' => is_active('apuracoes'),
                'nav_link'   => "?app=dashboard",
                'nav_icon'   => "bi-arrow-down-square",
                'nav_title'  => "Apuração"
            ],
            'candidatos' => [
                'nav_active' => is_active('candidatos'),
                'nav_link'   => "?app=chamados",
                'nav_icon'   => "bi-person-square",
                'nav_title'  => "Candidatos"
            ],
            'sessoes' => [
                'nav_active' => is_active('sessoes'),
                'nav_link'   => "?app=setores",
                'nav_icon'   => "bi-buildings",
                'nav_title'  => "Sessões"
            ],
            'reports' => [
                'nav_active' => is_active('reports'),
                'nav_link'   => "?app=reports",
                'nav_icon'   => "bi-bar-chart-line",
                'nav_title'  => "Relatórios"
            ],
        ];
    }
}