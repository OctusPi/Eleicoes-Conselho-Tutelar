<?php
namespace App\Utils;
use App\Controllers\Pages\Page;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Postman{
    private function getType(string $type):string
    {
        return match($type){
            'newuser' => 'emails/newuser',
            'recover' => 'emails/recover',
            'renew'   => 'emails/renew',
            'newcall' => 'emails/newcall',
            default   => ''
        };
    }

    private function getSubject(string $type):string
    {
        return match($type){
            'newuser' => 'Seja Bem Vindo',
            'recover' => 'Recuperacao de Senha',
            'renew'   => 'Sua Senha foi Alterada',
            'newcall' => 'Novo Chamado',
            default   => ''
        };
    }

    private function buildHtml(string $type, array $params):string
    {

        $content    = View::renderView($this->getType($type), $params);
        $mailParams = [
            'title'    => $this->getSubject($type),
            'content'  => $content,
            'dev_name' => Page::APPDESC,
            'sys_name' => Page::APPNAME,
            'sys_link' => Page::APPURL
        ];

        return View::renderView('emails/master', $mailParams);
    }

    public function mail(string $destiny, string $type, array $params):bool
    {
        $credentials = __DIR__.'/../../config/smtp.php';
        if(file_exists($credentials)){
            require_once($credentials);

            $mail = new PHPMailer(false);

            try {

                //Server settings
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USER;
                $mail->Password   = SMTP_PASS;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = SMTP_PORT;

                //Recipients
                $mail->setFrom(SMTP_FROM, 'Depto Tecnologia Araripe');
                $mail->addAddress($destiny);

                //Content
                $mail->isHTML(true);
                $mail->Subject = $this->getSubject($type);
                $mail->Body    = $this->buildHtml($type, $params);

                $mail->send();

                return true;

            }catch (Exception $e) {
                Log::critical("Messagem não pode ser enviada. Mail Error: {$e->getMessage()}");
            }
            
        }

        return false;
    }
}