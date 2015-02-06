<?php

class Interpretador {

	var $body = null;
	var $arquivo;
	var $arquivosImportados = array();
	var $marcacoes = array();
	var $quemTemOBody = null;

	function interpretar($a){
		$this->arquivo = $a;
		if (($c = $this->abrir($a)) === false)
			return false;

		$imports = array();
		if (preg_match_all('/<!-- importar: ([^\s]+) -->/i', $c , $imports)){
			$dirBase = dirname($this->arquivo).'/';
			$i = 0;
			foreach($imports[1] as $a){
				$this->arquivosImportados[$i] = new Interpretador();
				if ($this->arquivosImportados[$i]->interpretar($dirBase.$a) !== true)
					return false;
				$i++;
			}
		}
		$imports = null;
		$marcacoes = array();
		if (preg_match_all('/<!-- conteudo: ([^\s]*) -->/i', $c , $marcacoes)){
			foreach($marcacoes[1] as $a){
				if (($v = $this->extrair($a, $c)) === false)
					return false;
				if ($a == 'body')
					$this->body = $v;
				else
					$this->marcacoes['{'.$a.'}'] = $v;
			}
		}else{
			$this->body = $c;
		}
		$marcacoes = null;
		return true;
	}

	function monta(){
		if ($this->body !== null)
			return $this->replaceMarcacoes($this->body, $this->marcacoes);
		if (!sizeof($this->arquivosImportados))
			return $this->erro('Não foi localizado o body');
		$body = $this->arquivosImportados[0]->monta();
		$q = sizeof($this->arquivosImportados);
		for($i=1;$i<$q;$i++)
			$body = $this->replaceMarcacoes($body, $this->arquivosImportados[$i]->marcacoes);
		return $this->replaceMarcacoes($body, $this->marcacoes);
	}

	function extrairMarcacoes($c){
		if (preg_match_all('/\{(\w+)\}/i', $c , $marcacoes))
			return $marcacoes[1];
		return array();
	}

	function montarParaEdicao($l, $v){
		$c = $this->monta();
		$m = $this->extrairMarcacoes($c);
		foreach ($m as $marc)
			$c = str_replace('{'.$marc.'}', $v[$marc].'<img src="img/bte.gif" alt="'.$marc.'" title="'.$marc.'" onclick="'.$l.'(\''.$marc.'\')" />', $c);
		return $c;
	}

	function replaceMarcacoes($c, $m){
		if (sizeof($m))
			return str_replace(array_keys($m), array_values($m), $c);
		return $c;
	}

	function erro($m){
		echo $this->arquivo .': ' .$m."\n";
		return false;
	}

	function extrair($m, $c){
		$ta = '<!-- conteudo: '.$m.' -->';
		$pos = strpos($c, $ta) + strlen($ta);
		if (($posFecha = (strpos($c, '<!-- conteudo -->', $pos))) === false)
			return $this->erro('O conteudo '.$m.' está mau fechado');
		return trim(substr($c, $pos, $posFecha-$pos));
	}

	function abrir($a){
		static $arquivos = array();
		if (isset($arquivos[$a]))
			return $this->erro('O arquivo '.$a.' já foi lido');
		$arquivos[$a] = 1;
		if (!file_exists($a))
			return $this->erro('O arquivo '.$a.' não existe, ou não está acessivel');
		return file_get_contents($a);
	}

}

?>