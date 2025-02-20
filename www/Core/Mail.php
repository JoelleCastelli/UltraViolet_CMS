<?php

namespace App\Core;

use App\Core\PhpMailer\PHPMailer;
use App\Core\PhpMailer\SMTP;
use App\Core\PhpMailer\Exception;

class Mail
{

	private string $Host;
	private string $Port;
    private string $SMTPSecure;
	private string $Username;
	private string $Password;

	public function __construct(){
		$this->Host = HOST_MAIL;
		$this->Port = PORT_MAIL;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$this->Username = MAIL_ACCOUNT;
		$this->Password = PASSWORD_ACCCOUNT;
	}

	public function sendMail($to, $from, $from_name, $subject, $msg) {
		$mail = new PHPMailer();
        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                      
            $mail->SMTPAuth = true;

            $mail->SMTPSecure = $this->SMTPSecure; 
            $mail->Host = $this->Host;
            $mail->Port = $this->Port;  
            $mail->Username = $this->Username;
            $mail->Password = $this->Password;
            
            $mail->IsHTML(true);
            $mail->CharSet = 'utf-8';
            $mail->From=$this->Username;
            $mail->FromName=$from_name;
            $mail->Sender=$from;
            $mail->AddReplyTo($from, $from_name);
            $mail->Subject = $subject;
            $mail->Body = $msg;
            $mail->AddAddress($to);
            $mail->send();

            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
	}

}