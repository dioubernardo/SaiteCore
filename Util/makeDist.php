<?php
$yuicompressor = dirname(__FILE__).'/yuicompressor.jar';

if (isset($DIR_DIST))
	$dirBase = realpath($DIR_DIST);
else
	$dirBase = realpath($_SERVER["argv"][1]);
$dirBase = str_replace('\\','/', $dirBase);

echo "Gerando Pacote de distribuição da pasta: " , $dirBase, "\n";

if (!is_dir($dirBase))
	die("Diretório inválido");

$dirDist = $dirBase.'/dist';

if (is_dir($dirDist))
	die('Remova o diretório da distribuição');

if (!mkdir($dirDist))
	die('Não foi possível criar o diretório de distribuição');

ler($dirBase, $dirDist);

@unlink($dirBase.'/tmpIn');
@unlink($dirBase.'/tmpOut');

function ler($dirBase, $dirDist){
	if ($dh = opendir($dirBase)){
		while (($file = readdir($dh)) !== false){
			if ($file[0] == '.' or $file == '.' or $file == '..')
				continue;
			if (is_dir($dirBase.'/'.$file)){
				if (($dirBase.'/'.$file) != $GLOBALS['dirDist'] and substr($file,0,1) != '_'){
					mkdir($dirDist.'/'.$file);
					ler($dirBase.'/'.$file, $dirDist.'/'.$file);
				}
			}else{
				$a = $dirBase.'/'.$file;
				if ($a == ($dirBase.'/tmpOut') or $a == ($dirBase.'/tmpIn'))
					continue;
				$ext = strtolower(pathinfo($a, PATHINFO_EXTENSION));
				echo '.';
				switch($ext){
					case 'phar':
						break;
					case 'js':
					case 'css':
						exec("java -jar \"{$GLOBALS['yuicompressor']}\" --line-break 5000 --type {$ext} -o \"{$dirDist}/{$file}\" \"{$a}\"");
						break;
					case 'html':
					case 'htm':
					case 'tpl':
						file_put_contents($dirDist.'/'.$file, HTMLmin(file_get_contents($a)));
						break;
					case 'php':
					case 'inc':
					default:
						@copy($a, $dirDist.'/'.$file);
				}
			}
		}
		closedir($dh);
	}
}

echo "\nFIM";

$marcacoes = array();
$tipo = '';

function HTMLmin($c){
	global $marcacoes, $tipo;
	$marcacoes = array();

	$c = trim($c);

	$tipo = 'js';
	$c = preg_replace_callback(
		'/\s*(<script\\b[^>]*?>)([\\s\\S]*?)<\\/script>\s*/i'
		, 'coisasProcessadasDepois' , $c);

		$tipo = 'css';
		$c = preg_replace_callback(
		'/\s*(<style\\b[^>]*?>)([\\s\\S]*?)<\\/style>\s*/i'
		, 'coisasProcessadasDepois' , $c);

		$tipo = 'comentario';
		$c = preg_replace_callback(
		'/\s*<!--([\\s\\S]*?)-->\s*/'
		, 'coisasProcessadasDepois' , $c);

		$tipo = 'normal';
		$c = preg_replace_callback(
		'/\s*<pre\\b[^>]*?>[\\s\\S]*?<\\/pre>\s*/i'
		, 'coisasProcessadasDepois' , $c);
		$c = preg_replace_callback(
		'/\s*<textarea\\b[^>]*?>[\\s\\S]*?<\\/textarea>\s*/i'
		, 'coisasProcessadasDepois' , $c);

		$c = preg_replace('/\s{2,}/', ' ', $c);
		//	$c = preg_replace('/>\s+</', '><', $c);

		if (is_array($marcacoes['normal']))
		foreach($marcacoes['normal'] as $m => $v)
		$c = str_replace($m, trim($v[0]), $c);

		if (is_array($marcacoes['js'])){
			foreach($marcacoes['js'] as $m => $v){
				$c = str_replace($m, $v[1].yuicompressor('js', $v[2]).'</script>', $c);
			}
		}

		if (is_array($marcacoes['css'])){
			foreach($marcacoes['css'] as $m => $v){
				$c = str_replace($m, $v[1].yuicompressor('css', $v[2]).'</style>', $c);
			}
		}
		if (is_array($marcacoes['comentario'])){
			foreach($marcacoes['comentario'] as $m => $v){
				if (preg_match('/^ ((importar|conteudo|bloco): |conteudo $)/', $v[1]))
				$c = str_replace($m, trim($v[0]), $c);
				else
				$c = str_replace($m, '', $c);
			}
		}

		$c = preg_replace('/^(<!DOCTYPE [^>]+>)\s*/', "\$1\n", $c);
		return wordwrap($c, 5000, " \n");
}

function coisasProcessadasDepois($m){
	static $c = 0;
	global $marcacoes, $tipo;
	$x = '<_#marcacoesProcessadosDepois#'.(++$c).'#_>';
	$marcacoes[$tipo][$x] = $m;
	return $x;
}

function removeCData($str){
	return preg_replace('/(^(\/\/\s*)?<\!\[CDATA\[|(\/\/\s*)?\]\]>$)/', '', trim($str));
}

function yuicompressor($type, $str){
	global $dirBase;
	file_put_contents($dirBase.'/tmpIn', $aux = removeCData($str));
	$r = _exec("java -jar \"{$GLOBALS['yuicompressor']}\" --line-break 5000 --type {$type} -o \"{$dirBase}/tmpOut\" \"{$dirBase}/tmpIn\"");
	if (!$r->status){
		echo "\n\nyuicompressor: ",$type," inline \n",$r->stdErr,"\n---\n",$aux,"\n---\n";
		return $aux;
	}
	return file_get_contents($dirBase.'/tmpOut');
}

function _exec($comando){
	$descriptorspec = array(
		2 => array('pipe', 'w') // stderr
	);
	$pipes = array();
	$ret = new stdClass();
	$ret->status = false;

	$process = proc_open($comando, $descriptorspec, $pipes);
	if (is_resource($process)){
		$ret->stdErr = stream_get_line($pipes[2], 1024);
		fclose($pipes[2]);
		$ret->status = (proc_close($process) == 0) ? true : false;
		$ret->stdErr = trim($ret->stdErr);
	}
	return $ret;
}

?>