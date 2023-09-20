<?php
namespace App\Utils;


class View
{
    const VIEW_PATH = __DIR__.'/../../views/';

    private static function getContentView(string $view): string{
        $file = self::VIEW_PATH.$view.'.html';
        $rollback = self::VIEW_PATH.'pages/404.html';
        return file_exists($file) ? file_get_contents($file) : file_get_contents($rollback);
    }

    public static function renderView(string $view, $data = []): string{
        $content = self::getContentView($view);
        $keys    = array_keys($data);
        $keys    = array_map(function($item){
            return '{{'.$item.'}}';
        }, $keys);

        return str_replace($keys, $data, $content);
    }

}