<?php
require_once DIR_SAITE.'ReadFile.php';

class Cache{

	function executar($base, $uri){
		$a = $base.'/cache/'.$uri;
		Readfile::read($a, false);
		Readfile::read($a.'/index.html', false);
	}

}

?>