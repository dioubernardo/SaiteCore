<?php

class UID{

	function GET(){
		header('Content-type: application/x-shockwave-flash');
		die(str_replace('{CODE}', substr(crypt(CHAVE_ID),3), file_get_contents(dirname(__FILE__).'/gc.dat')));
	}

}

?>