<?php

class COM_JsCom{

	function lista(&$html){
		COM_jQuery::addUI($html);
		$html->addJS(URL.'core/com/JsCom/coms.js');
		$html->addCSS(URL.'core/com/JsCom/css.css');
		$html->addScript('JsCom.lista.init()');
	}

}

?>