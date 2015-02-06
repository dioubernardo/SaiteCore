<?php

class COM_Fancybox{

	public static function add(&$html){
		COM_jQuery::add($html);
		$html->addJS(URL.'core/com/Fancybox/jquery.fancybox.js');
		$html->addCSS(URL.'core/com/Fancybox/jquery.fancybox.css');
	}

}

?>