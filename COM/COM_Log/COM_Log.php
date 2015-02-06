<?php

class COM_Log{

	function erro($msg){
		die($msg);
		COM_Log::escrever('[ERRO] '.$msg);
		COM_Log::enviarEmail($msg);
		COM_Log::gerarTela($msg);
	}

	function alerta($msg){
		COM_Log::escrever('[ALERTA] '.$msg);
		COM_Log::enviarEmail($msg);
	}

	function info($msg){
		COM_Log::escrever('[INFO] '.$msg);
	}

	function escrever($msg){
		if (!COM_Log::criarDiretorio(DIR_LOGS) or !error_log(date('H:i:s ').$msg."\n", 3, DIR_LOGS.date('Ymd').'.log'))
		COM_Log::gerarTela('Não foi possivel salvar o log: '.$msg);
	}

	function criarDiretorio($dir){
		@is_dir($pDir = dirname($dir)) or COM_Log::criarDiretorio($pDir);
		return is_dir($dir) or @mkdir($dir);
	}

	function gerarTela($msg){
		echo "<h1>Erro</h1><pre>{$msg}</pre>";
		exit;
	}

	function enviarEmail($msg){
		require_once DIR_SAITE.'COM/COM_Email/COM_Email.php';
		$email = new COM_Email();
		$email->defineAssunto('Erro em '.$_SERVER['HTTP_HOST']);
		$email->adicionaPara(EMAIL_ADMINISTRADOR);
		$email->defineMensagem($msg);
		if (!$email->Send())
		COM_Log::gerarTela('Não foi possivel enviar o email com a mensagem: '.$msg);
	}

}

?>