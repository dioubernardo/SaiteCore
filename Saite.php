<?php

function __autoload($className){
	if (substr($className,0, 4) == 'COM_')
		require_once DIR_SAITE.'COM/'.$className.'/'.$className.'.php';
}

if (defined('ENCODING'))
	ini_set('default_charset', ENCODING);

?>