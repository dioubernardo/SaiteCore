<?php

class COM_SWFUpload{

	public static function add(&$html){
		COM_jQuery::add($html);
		$html->addJS(URL.'core/com/SWFUpload/swfupload.js');
	}

}

?>