<?php
require_once 'class.phpmailer.php';

class COM_Email extends PHPMailer{

	static function &getPHPMailer($servidor, $user = null, $password = null){
		$i = new PHPMailer();
		$i->Host = $servidor;
		$i->IsSMTP();
		$i->IsHTML(true);
		$i->AddCustomHeader('MIME-Version: 1.0');
		$i->AddCustomHeader('X-Mailer: News-Vetorial');
		if (!empty($user)){
			$i->SMTPAuth = true;
			$i->Username = $user;
			$i->Password = $password;
		}
		return $i;
	}

	function COM_Email(){
		$this->Host = SERVIDOR_EMAIL;
		$this->IsSMTP();
		$this->IsHTML(true);

		$this->AddCustomHeader('MIME-Version: 1.0');
		$this->AddCustomHeader('X-Mailer: News-Vetorial');

		$this->AddReplyTo(EMAIL_ADMINISTRADOR);
		/*
		 From: Usar um e-mail do próprio domínio
		 Return-Path: Usar o mesmo igual utilizado no From
		 Reply-To:
		 */

		$this->defineDe('', EMAIL_ADMINISTRADOR);
		$this->Subject = 'Sem assunto';
		$this->Body = 'Nenhum mensagem foi definida!';

		if (defined('SMTP_USUARIO') and defined('SMTP_SENHA')){
			$this->SMTPAuth = true;
			$this->Username = SMTP_USUARIO;
			$this->Password = SMTP_SENHA;
		}
		if (defined('ENCODING'))
			$email->CharSet = ENCODING;
	}

	function defineDe($nome, $email = null){
		$this->FromName = $nome;
		if ($email != null)
		$this->From = $email;
	}

	function adicionaPara($email, $nome = ''){
		$this->AddAddress($email, $nome);
	}

	function defineAssunto($assunto){
		$this->Subject = $assunto;
	}

	function defineMensagem($msg){
		$this->Body = $msg;
	}

}

?>