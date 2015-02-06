<?php

class COM_Busca{

	function add(&$html){
		COM_jQuery::add($html);
		$html->addJS(URL.'core/com/Busca/busca.js');
		$html->addCSS(URL.'core/com/Busca/busca.css');
	}

	function escrever($v){
		echo '[';
		$p = true;
		if (is_array($v)){
			foreach ($v as $k => $l){
				if (!$p){
					echo ',';
				}else{
					$p = false;
				}
				echo '["';
				COM_Busca::_value($k);
				echo '","';
				COM_Busca::_value($l);
				echo '"]';
			}
		}elseif(is_a($v, 'MysqlResult')){
			while($v->next()){
				if (!$p){
					echo ',';
				}else{
					$p = false;
				}
				echo '["';
				COM_Busca::_value($v->Record->value);
				echo '","';
				COM_Busca::_value($v->Record->text);
				echo '"]';
			}
		}
		echo ']';
	}

	private function _value($v){
		echo str_replace(array("\r", "\n"), array('', '\n'), addslashes($v));
	}

}

?>