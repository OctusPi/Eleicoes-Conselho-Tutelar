<?php
namespace App\Utils;

use App\Controllers\Pages\Page;
use App\Utils\Utils;
use App\Utils\Security;
use App\Controllers\Pages\NotFound;

class Route
{
    const NAMESPACE = 'App\\Controllers\\Pages\\';
    const DEFAULTPG = 'dashboard';
    

    public static function gets():array
    {
        $values = [];
        
        if(isset($_GET) && !empty($_GET)){
            foreach (array_keys($_GET) as $key) {
                $values[Security::sanitize($key)] = Security::sanitize($_GET[$key]);
            }
        }

        // set default app key in array
        if(!key_exists('app', $values)){
            $values['app'] = self::DEFAULTPG;
        }

        return $values;
    }

    public static function only(string $key):mixed
    {
        return Utils::at($key, self::gets());
    }

    public static function route(array $mod):string
    {
        $modRoute = [];
        foreach ($mod as $key => $value) {
            $modRoute[Security::sanitize($key)] = Security::sanitize($value);
        }

        $newRoute = array_merge(self::gets(), $modRoute);

        return Security::sanitize('?'.implode('&', array_map(function($key, $value){
            return $key.'='.$value;
        }, array_keys($newRoute), array_values($newRoute))));
    }
    
    /**
     * Method create destiny with param app in url
     * @return Page
     */
    private function destiny():Page
    {
        $app  = self::only('app') != null ? self::only('app') : self::DEFAULTPG;
        $page = $app != null ? self::NAMESPACE.ucfirst($app) : null;
        
        if($page != null && class_exists($page)){
            return new $page();
        }else{
            return new NotFound();
        }
    }

    /**
     * Method identify destiny and execut action required
     *
     * @return string|null
     */
    public function go():?string
    {
        $page    = $this->destiny();
        $action  = self::only('action');
        
        $method  = match($action){
            null     => 'index',
            default  => $action
        };

        return method_exists($page, $method) 
        ? $page->$method()
        : (new NotFound())->callBack();
    }
}