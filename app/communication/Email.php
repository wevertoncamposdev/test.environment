<?php

namespace app\communication;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Email{

    /** Credenciais de acesso SMTP
     * @var strings
    */
    const HOST = 'mail.crechealvorada.org';
    const USERNAME = 'ti@crechealvorada.org';
    const PASSWORD = 'alvorada@email@ti';
    const SECURE = 'TLS'; //TLS - SSL
    const PORT = '587'; //587 - 465 
    const CHARSET = 'utf8';

    /** Dados do remetente 
     * @var strings
    */
    const FROM_EMAIL = 'ti@crechealvorada.org';
    const FROM_NAME = 'SIA - Sistema de Indicadores da Alvorada';

    /** Mensagem de erro do envio 
     * @var strings
    */
    private $error;

    /** Método responsável por retornar a mensagem de erro de envio
     * @return strings 
     */
    public function getError() {
        return $this->error;
    }

    /** Método responsável por enviar um email
     * @param strings|arrays $addresses
     * @param strings $subject
     * @param strings $body
     * @param strings $attachments
     * @param strings $ccs
     * @param strings $bccs
     * @return booleans 
    */

    public function sendEmail($addresses, $subject, $body, $attachments = [], $ccs = [], $bccs = []){
        
        //LIMPAR A MENSAGEM DE ERRO
        $this->error = '';

        //INSTANCIA DE PHPMailer
        $mail = new PHPMailer(true); //true para executar as exceptions
        try{

            //CREDENCIAIS DE ACESSO AO SMTP
            $mail->isSMTP(true);
            $mail->Host = self::HOST;
            $mail->SMTPAuth = true;
            $mail->Username = self::USERNAME;
            $mail->Password = self::PASSWORD;
            $mail->SMTPSecure = self::SECURE;
            $mail->Port = self::PORT;
            $mail->Charset = self::CHARSET;

            //REMETENTE
            $mail->setFrom(self::FROM_EMAIL, self::FROM_NAME);

            //DESTINATÁRIOS
            $addresses = is_array($addresses) ? $addresses : [$addresses];
            foreach ($addresses as $address){
                $mail->addAddress($address);
            }

            //ANEXOS
            $attachments = is_array($attachments) ? $attachments : [$attachments];
            foreach ($attachments as $attachment){
                $mail->addAttachment($attachment);
            }

            //CÓPIA CC
            $ccs = is_array($ccs) ? $ccs : [$ccs];
            foreach ($ccs as $cc){
                $mail->addCC($cc);
            }

            //CÓPIA OCULTA BCC
            $bccs = is_array($bccs) ? $bccs : [$bccs];
            foreach ($bccs as $bcc){
                $mail->addBCC($bcc);
            }

            //CONTEÚDO DO E-MAIL
            $mail->isHTML(true);
            $mail->Subject = $subject; //assunto
            $mail->Body = $body;

            //ENVIA O EMAIL
            return $mail->send();


        }catch(PHPMailerException $e){
            $this->error = $e->getMessage();
            return false;
        }
  
    }

    public static function sendEmailAuth($email){

  
    }
}