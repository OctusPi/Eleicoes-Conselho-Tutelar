<?php
namespace App\Utils;
use App\Controllers\Api\Api;
use App\Controllers\Api\Usuarios;
use App\Models\EntityUser;


class RouteApi
{
    const NAMESPACE = 'App\\Controllers\\Api\\';
    const DEFAULTAPI = 'login';

    private ?EntityUser $usuario;

    public function __construct()
    {
        $this->usuario = Usuarios::getUserApi();
    }

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
            $values['app'] = self::DEFAULTAPI;
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
    

    private function destiny():?Api
    {
        $api     = self::only('app') ?? self::DEFAULTAPI;
        $destiny =  self::NAMESPACE.ucfirst($api);
        
        if(class_exists($destiny)){
            return new $destiny($this->usuario);
        }

        return null;
    }

    public function go():string
    {
        $destiny = $this->destiny();
        $action  = self::only('action');
        $method  = match($action){
            null     => 'index',
            default  => $action
        };

        if($destiny != null){
            return method_exists($destiny, $method) 
            ? $destiny->$method()
            : json_encode([]);
        }else{
            return json_encode([]);
        }
        
    }
}