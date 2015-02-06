<?php

class sTemplateManipulacaoBase{

	var $c = '';

	function set($m, $v = '', $raw = false){
		if (is_a($m, 'sBloco')){
			$this->set($m->marcacao, $m->html, true);
		}else{
			if (!$raw)
			$v = str_replace(array('{','}'), array('&#123;','&#125;'), htmlentities($v, ENT_QUOTES, defined('ENCODING') ? ENCODING : 'ISO-8859-1'));
			$this->c = str_replace('{'.$m.'}', $v, $this->c);
		}
	}

	function obtemBloco($nome, &$c){
		$s = '<!-- bloco: '.$nome.' -->';
		$l = strlen($s);
		if(($posI = strpos($c, $s)) !== false and ($posF = strpos($c, $s, $posI+$l))){
			$m = '__bloco'.((int)$GLOBALS['_contadorBloco']++);
			$pI = $posI+$l;
			$b = new sBloco($m, substr($c, $pI, $posF-$pI));
			$c = substr($c, 0, $posI).'{'.$m.'}'.substr($c, $posF+$l);
			return $b;
		}else{
			return null;
		}
	}

}

class sTemplate extends sTemplateManipulacaoBase{

	var $js = array();
	var $css = array();
	var $script = '';

	function sTemplate($a){
		$cache = DIR_CACHE.'templates/'.md5($a).'.html';
		if (DESENVOLVIMENTO !== true and file_exists($cache)){
			$this->c = file_get_contents($cache);
		}else{
			require_once DIR_SAITE.'Interpretador.php';
			$i = new Interpretador();
			$i->interpretar($a);
			$this->c = $i->monta();
			
			if (DESENVOLVIMENTO !== true){
				$this->criarDiretorio(dirname($cache));
				$fp = fopen($cache, 'w');
				fwrite($fp, $this->c);
				fclose($fp);
			}
		}
	}

	function criarDiretorio($dirname){
		is_dir($pDir = dirname($dirname)) or $this->criarDiretorio($pDir);
		return is_dir($dirname) or mkdir($dirname);
	}

	function &obtemBloco($nome){
		return parent::obtemBloco($nome, $this->c);
	}

	function addJS($js){
		if (is_array($js))
		$this->js = array_merge($this->js, $js);
		else
		$this->js[] = $js;
	}

	function addScript($s){
		$this->script .= (!empty($this->script) ? ';' : '') . $s;
	}

	function addCSS($css){
		if (is_array($css))
		$this->css = array_merge($this->css, $css);
		else
		$this->css[] = $css;
	}

	function monta($return = false){
		$head = '';
		$this->css = array_unique($this->css);
		foreach ($this->css as $css)
		$head .= '<link rel="stylesheet" href="'.$css.'" type="text/css" />';

		$this->js = array_unique($this->js);
		foreach ($this->js as $js)
		$head .= '<script type="text/javascript" src="'.$js.'"></script>';

		if (!empty($this->script))
		$head .= '<script type="text/javascript">'.$this->script.'</script>';

		$this->set('SAITE_HEAD', $head, true);
		$this->set('URL', URL);

		if ($return)
		return $this->c;
		echo $this->c;
	}

}

class sBloco extends sTemplateManipulacaoBase{

	var $marcacao;
	var $htmlBase;
	var $html;

	function sBloco($marcacao, $conteudo){
		$this->htmlBase = $conteudo;
		$this->marcacao = $marcacao;
	}

	function &obtemBloco($nome){
		return parent::obtemBloco($nome, $this->htmlBase);
	}

	function set($m, $v = '', $raw = false){
		if ($this->c == null)
		$this->c = $this->htmlBase;
		parent::set($m, $v, $raw);
	}

	function parse(){
		if ($this->c == null){
			$this->html .= $this->htmlBase;
		}else{
			$this->html .= $this->c;
			$this->c = null;
		}
	}

	function clear(){
		$this->c = null;
		$this->html = '';
	}
}

?>