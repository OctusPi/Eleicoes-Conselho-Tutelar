<?php
namespace App\Utils;

use App\Models\EntityUser;
use App\Utils\Route;
use App\Utils\Utils;
use App\Controllers\Pages\Page;

class Security
{
    private static string $token = Page::APPNAME.'966d1c46d9f4a6c8eaa5e6d0a40e8a3e';
    private static int $offtime  = 0;
    private static int $attempts = 5;


    public static function guardian(Page $page, ?Session $session, ?EntityUser $usuario = null, bool $security = true):void
    {
        if($security){

            //case attempt use brute force login
            if(self::isBrute()){
                self::redirect(Route::route(['app'=>'jail']));
                exit;
            }

            //case user null or session denied
            if($usuario == null || !$session->isAllowed($usuario)){
                self::redirect(Route::route(['app'=>'login', 'action'=>'logoff']));
                exit;
            }

            //case user change password mandatory
            if(Utils::ob('passchange', $usuario) == 1 && Route::gets()['app'] != 'passchange'){
                self::redirect(Route::route(['app'=>'passchange']));
                exit;
            }

            //case access deined or user bloqued
            if(!self::isAuth($page, $usuario)){
                self::redirect(Route::route(['app'=>'denied']));
                exit;
            }
        }
    }

    /**
    * Method checks credential access user and page to grant access if authorized
    *
    * @param Page $page
    * @param EntityUser|null $usuario
    * @return bool
    */
    public static function isAuth(Page $page, ?EntityUser $user):bool
    {
        if($user != null && $user->get('status') == 1){
            
            $crPage = $page->getCredentials();

            return (
                ($crPage['perfil'] == 0 || $user->get('perfil') <= $crPage['perfil'])
                && 
                (in_array($crPage['nivel'], $user->get('niveis') ?? []))
            );
        }

        return false;
    }

    /**
     * Method clean malicius text of external fonts (manual clean and sanitize string PHP)
     *
     * @param mixed $input
     * @return null|string
     */
    public static function sanitize(mixed $input):null|string|array
    {
        if($input != null){
            if(is_array($input)){
                $clear = [];
                foreach($input as $key => $item){
                    $clear[$key] = self::sanitize($item);
                }
                return $clear;
            }else{
                $input = preg_replace("/(from|FROM|script|SCRIPT|select|SELECT|insert|INSERT|delete|DELETE|truncate|TRUNCATE|where|WHERE|drop|DROP|drop table|DROP TABLE|show tables|SHOW TABLES|#|\$|-\$-|\*|--|\\\\)/","",$input);
                return strip_tags($input);
            }
        }else{
            return null;
        }
    }

    /**
     * Method redirect page url using heders PHP
     *
     * @param string $url
     * @return void
     */
    public static function redirect(string $url = 'index.php'):void
    {
        header("Location:$url");
        exit;
    }

    /**
     * Method generate security password randomic
     *
     * @return string
     */
    public static function randonPass(): string
    {
        $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $pass = '';
        for($i=0; $i<12; $i++):
            $pass .= $char[rand(0, strlen($char)-1)];
        endfor;

        return $pass;
    }

    /**
     * Method apply security policy in change passward user request
     * The password is validate with 8 characters or more and numeric and symbols
     * @param string $newPass
     * @param string $repPass
     * @param string $oldPass
     * @return array
     */
    public static function isPassValid(string $newPass, string $repPass, string $oldPass = ''): array
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';

        if(!preg_match($pattern, $newPass)){
            return [
                'status' =>false, 
                'message'=>'Regras de segurança não satisfeitas para a nova senha!'
            ];
        }

        //checks if new password equal temp password
        if($newPass == $oldPass){
            return [
                'status' =>false, 
                'message'=>'A nova senha não pode ser igual a senha temporária'
            ];
        }

        if($newPass != $repPass){
            return [
                'status' =>false, 
                'message'=>'Falha ao validar nova senha, senhas não conferem!'
            ];
        }

        return [
            'status' => true, 
            'message'  => 'Nova senha aceita'
        ];
    }

    public static function checkPass(?string $pass, ?EntityUser $user){
        if($user != null){
            return $user->get('pid') === hash('sha256', $pass ?? "");
        }
        return false;
    }

    /**
     * Method count and record in cookie number attempts access login fail in system
     *
     * @return void
     */
    public static function countAttempts():void
    {
        $attempt = isset($_COOKIE[self::$token]) ? $_COOKIE[self::$token] + 1 : 1;
        setcookie(
            self::$token, //name
            $attempt, //value
            [
                'expires'  => self::$offtime,
                'path'     => '/',
                'secure'   => false,
                'httponly' => false,
                'samesite' => 'Strict',
            ]
        );
    }

    /**
     * Show rest attempts logind before bloq
     *
     * @return string|null
     */
    public static function viewAttempts():?string
    {
        return isset($_COOKIE[self::$token])
                ? 'Tentativa '.$_COOKIE[self::$token].' de '.self::$attempts
                : null;
    }

    /**
     * Method ficalize if numb attempts logins fail is not greater than allowed
     *
     * @return bool
     */
    public static function isBrute():bool
    {
        return Utils::at(self::$token, $_COOKIE) > self::$attempts;
    }

    /**
     * checks if user is auth to list data content view
     *
     * @param EntityUser|null $user
     * @param integer $orgao
     * @param integer $unidade
     * @return boolean
     */
    public static function isAuthList(?EntityUser $user, int $orgao, int $unidade = 0):bool
    {

        // if($user != null){
        //     $perfil   = $user->get('perfil');
        //     $orgaos   = $user->get('orgaos');
        //     $unidades = $user->get('unidades');

        //     if(
        //         //is admin
        //         ($perfil == EntityUser::P_ADMIN)

        //         ||

        //         //gestor or tec consolidacao
        //         (($perfil == EntityUser::P_GESTOR || $perfil == EntityUser::P_TECNICO)
        //         && in_array($orgao, $orgaos ?? []))

        //         ||

        //         //tec unidade
        //         ($perfil == EntityUser::P_SECRETARIO
        //         && in_array($orgao, $orgaos ?? [])
        //         && ($unidade == 0 || in_array($unidade, $unidades ?? [])))

        //     ){
        //         return true;
        //     }
        // }

        return false;
        
    }
}