<?php

/**
Quando é acessado /maria/jose

ordem de busca
- na pasta /paginas/maria uma classe jose com a função post (Apenas se post)
- na pasta /paginas/maria uma classe jose com a função get
- na pasta /paginas uma classe maria com a função post_jose (Apenas se post)
- na pasta /paginas uma classe maria com a função get_jose
- na pasta /paginas uma classe maria com a função jose (Apenas se post)
- na pasta /paginas uma classe maria com a função post e passar jose como parametro (Apenas se post)
- na pasta /paginas uma classe maria com a função get e passar jose como parametro
- na pasta /paginas uma classe Saite com a função post_maria e passar jose como parametro (Apenas se post)
- na pasta /paginas uma classe Saite com a função get_maria e passar jose como parametro
- na pasta /paginas uma classe Saite com a função maria e passar jose como parametro
- na pasta /paginas uma classe Saite com a função post e passar maria e jose como parametro (Apenas se post)
- na pasta /paginas uma classe Saite com a função get e passar maria e jose como parametro
*/
class RespotaClasses {

	var $parametros = array();
	var $contParametros = 0;
	var $cache;
	var $uri;

	static function executar($uri){
		$r = new RespotaClasses();
		$r->_executar($uri);
	}

	function _executar($uri){
		if (($pos = strpos($uri, '?')) !== false)
			$uri = substr($uri, 0, $pos);
		$uri = preg_replace('/(^'.addcslashes(URL, '/.').'?|\/$)/', '', $uri);
		$this->uri = $uri;

		$this->runCoreProxy();

		$uri = explode('/', $uri);
		$todos = sizeof($uri);

		while(($quantos = $todos--) >= 0 and !$this->run($uri)){
			$this->parametros[$this->contParametros++] = $uri[--$quantos];
			unset($uri[$quantos]);
		}
	}

	function runCoreProxy(){
		/* proxy para o core */
		if (substr($this->uri, 0, 9) == 'core/com/'){
			require_once DIR_SAITE.'Proxy.php';
			$o = new Proxy();
			$this->parametros = substr($this->uri, 9);
			$this->call($o, 'com', false);
			exit;
		}
	}

	function run($dados){
		$ultimo = sizeof($dados);
		if ($ultimo == 0)
			return $this->loadExec(DIR_CLASSES.'Saite.php', 'Saite', $this->parametros[$this->contParametros-1]);
		$arq = DIR_CLASSES.implode('/', $dados).'.php';
		return $this->loadExec($arq, $dados[$ultimo-1], $this->parametros[$this->contParametros-1]);
	}

	function loadExec($arquivo, $classe, $metodo){
		if (file_exists($arquivo)){
			require_once $arquivo;
			$classe = str_replace('-', '_', $classe);
			$metodo = str_replace('-', '_', $metodo);
			$o = new $classe;
			$m = !empty($_SERVER['REDIRECT_REQUEST_METHOD']) ? $_SERVER['REDIRECT_REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
			if (!empty($metodo) and method_exists($o, $m.'_'.$metodo)){
				unset($this->parametros[$this->contParametros-1]);
				return $this->call($o, $m.'_'.$metodo);
			}
			if (method_exists($o, $metodo)){
				unset($this->parametros[$this->contParametros-1]);
				return $this->call($o, $metodo);
			}
			if (method_exists($o, $m))
				return $this->call($o, $m);
		}
		return false;
	}

	function call(&$o, $metodo, $reverse = true){
		$cache = (DESENVOLVIMENTO !== true and method_exists($o, 'cache') and $o->cache($metodo));

		if($cache){
			$arquivo = DIR_CACHE.$this->uri;
			if (!preg_match('/\.(jpe?g|gif|png|css|js|swf)$/i', $this->uri))
				$arquivo .= '/index.html';
			$this->criarDiretorio(dirname($arquivo));
			$this->cache = fopen($arquivo, 'w');
			ob_start(array(&$this, 'escreverCache'));
		}

		call_user_func(array(&$o, $metodo), $reverse ? array_reverse($this->parametros) : $this->parametros);

		if ($cache){
			ob_end_flush();
			@fclose($this->cache);
		}

		return true;
	}

	function escreverCache($s){
		fwrite($this->cache, $s);
		return $s;
	}

	function criarDiretorio($dirname){
		is_dir($pDir = dirname($dirname)) or $this->criarDiretorio($pDir);
		return is_dir($dirname) or mkdir($dirname);
	}

}

?>