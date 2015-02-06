<?php
require_once DIR_SAITE.'ReadFile.php';

class Proxy{

	/*
	 Tudo vai ser colocado em cache
	 */
	function cache(){
		return true;
	}

	/*
	 Para responder
	 www.meusite.com.br/core/com/x/y
	 Com arquivos retirados de SaiteCore/COM/COM_x/y
	 */
	function com($p){
		$c = DIR_SAITE.'COM/COM_'.$p;

		Readfile::read($c, true);

		/* tentar resposta em classe */
		$p = explode('/', $p);
		$c = DIR_SAITE.'COM/COM_'.$p[0].'/'.$p[0].'.php';

		if (file_exists($c)){
			require $c;
			$o = new $p[0];
			$m = !empty($_SERVER['REDIRECT_REQUEST_METHOD']) ? $_SERVER['REDIRECT_REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
			$metodo = $m.'_'.$p[1];
			if (method_exists($o, $metodo)){
				call_user_func(array(&$o, $metodo), array_slice($p, 2));
				exit;
			}
			$metodo = $m;
			if (method_exists($o, $metodo)){
				call_user_func(array(&$o, $metodo), array_slice($p, 2));
				exit;
			}
		}
		header('HTTP/1.0 404 Not Found');
	}

}

?>