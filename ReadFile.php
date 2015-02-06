<?php

class Readfile{

	function read($c, $replaceURL){
		if (file_exists($c)){
			$ext = strtolower(pathinfo($c , PATHINFO_EXTENSION));
			switch ($ext){
				case 'js':
					header('Content-type: application/javascript');
					if ($replaceURL)
					die(str_replace('{URL}', URL, file_get_contents($c)));
					readfile($c);
					exit;
				case 'css':
					header('Content-type: text/css');
					readfile($c);
					exit;
				case 'swf':
					header('Content-type: application/x-shockwave-flash');
					readfile($c);
					exit;
				case 'html':
				case 'htm':
					readfile($c);
					exit;
				case 'png':
				case 'gif':
				case 'jpg':
				case 'jpeg':
					header('Content-type: image/'.$ext);
					readfile($c);
					exit;
			}
		}
	}

}

?>