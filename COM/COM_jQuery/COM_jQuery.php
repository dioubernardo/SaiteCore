<?php

class COM_jQuery{

	public static function add(&$html){
		$html->addJS(URL.'core/com/jQuery/jquery.js');
	}

	public static function addUI(&$html){
		COM_jQuery::add($html);
		$html->addJS(URL.'core/com/jQuery/jquery-ui.js');
		$html->addCSS(URL.'core/com/jQuery/jquery-ui.css');
	}

	public static function addMaskedInput(&$html){
		COM_jQuery::add($html);
		$html->addJS(URL.'core/com/jQuery/jquery-maskedinput.js');
	}
}

?>