<?php
require_once DIR_SAITE.'COM/COM_jQuery/COM_jQuery.php';

class COM_AjaxPostForm{

	static function add(&$html){
		COM_jQuery::add($html);
		$html->addJS(URL.'core/com/AjaxPostForm/APF.js');
	}

	static function exec($script, $die = false){
		echo $script;
		$die && exit;
	}

	static function alert($msg, $die = false){
		COM_AjaxPostForm::exec('alert("'.str_replace("\n", '\n', addslashes($msg)).'");', $die);
	}

	static function location($url, $die = false){
		COM_AjaxPostForm::exec('document.location="'.addslashes($url).'";', $die);
	}

	static function escape($txt){
		return addslashes(htmlentities($txt, ENT_NOQUOTES));
	}

}

?>