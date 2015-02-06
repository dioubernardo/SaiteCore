<?php

class COM_HTMLTag{

	var $att = array();
	var $tag;
	var $valor;
	var $fechada;

	function COM_HTMLTag($t, $fechada){
		$this->tag = $t;
		$this->fechada = $fechada;
	}

	function valor($v){
		$this->valor = $v;
	}

	function att($c, $v = null){
		if (is_null($v))
		return $this->att[$c];
		$c = strtolower($c);
		if ($c == 'style' or substr($c,0,2) == 'on'){
			if (!empty($v))
			$this->att[$c] .= (empty($this->att[$c]) ? '' : ';') . $v;
		}elseif($c == 'class'){
			if (!empty($v))
			$this->att['class'] .= (empty($this->att['class']) ? '' : ' ') . $v;
		}else{
			$this->att[$c] = $v;
		}
	}

	function strAtts(){
		$r = '';
		foreach($this->att as $c => $v)
		if (!empty($c) and $v != '')
		$r .= ' '.$this->strAtt($c, $v);
		return $r;
	}

	function strAtt($c, $v){
		return $c . '="' .htmlentities($v, ENT_COMPAT, defined('ENCODING') ? ENCODING : 'ISO-8859-1'). '"';
	}

	function monta(){
		$r = '<'.$this->tag.$this->strAtts();
		if ($this->fechada)
		return $r.' />';
		return $r.'>'.htmlentities($this->valor, ENT_COMPAT, defined('ENCODING') ? ENCODING : 'ISO-8859-1').'</'.$this->tag.'>';
	}

	function getId(){
		static $c = 0;
		return 'scid'.$c++;
	}
}

?>