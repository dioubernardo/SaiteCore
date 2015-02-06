<?php
function sub($c, $var){
	foreach ($var as $chave => $valor)
	$c = str_replace('{'.$chave.'}', $valor, $c);
	echo '<pre>'.$c.'</pre>';
	return $c;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Saite Core - Instalação</title>
<style type="text/css">
body {
	font-family: arial;
	font-size: 12px
}

h1 {
	text-align: center
}

fieldset {
	background-color: #ffc;
	border: 1px solid #ccc;
	padding: 5px 15px
}

legend {
	font-weight: bold;
	font-size: 13px;
	color: #333
}

label {
	font-weight: bold;
	color: green
}

b {
	font-weight: bold;
	color: red
}
</style>
</head>
<body>
<h1>Saite Core - Instalação</h1>

<fieldset><legend>Configurações</legend> <?php
$dir = dirname(__FILE__).'/';
$dir = str_replace('\\', '/', $dir);
$a = $dir.'config.php';
if (file_exists($a)){
	require $a;
	$OK = true;
	echo "<label>Arquivo de configurações lido com sucesso</label>";
}else{
	$OK = false;
	echo "<b>Não foi possivel ler o arquivo de configurações</b>";
}
?></fieldset>

<fieldset><legend>.htacess</legend> <?php
if ($OK){
	$dir = dirname(__FILE__).'/';
	$dir = str_replace('\\', '/', $dir);
	$c = sub('
# .htacess para as urls amigaveis do Saite
RewriteEngine On
# usar o cache
RewriteCond {DOCUMENT_ROOT}cache/%{REQUEST_URI} -f
RewriteRule . {URL}cache/%{REQUEST_URI} [L]
RewriteCond {DOCUMENT_ROOT}cache/%{REQUEST_URI}/index.html -f
RewriteRule . {URL}cache/%{REQUEST_URI}/index.html [L]
# se não existir o arquivo ou o diretorio, executar as classes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}/index.html !-f
RewriteRule . {URL}index.php
', array(
	'DOCUMENT_ROOT' => $dir,
	'URL' => URL
	));
	if (is_writable($dir)){
		$fp = fopen($dir.'/.htaccess', 'w');
		fwrite($fp, $c);
		fclose($fp);
		echo '<label>Arquivo criado com sucesso</label>';
	}else{
		echo '<b>Não é possivel escrever no diretório ['.$dir.']</b>';
	}
}else{
	echo '<b>É preciso ler o arquivo de configurações para está opção funcionar</b>';
}
?></fieldset>

<fieldset><legend>Diretório do cache</legend> <?php

function removerDiretorio($dir){
	if (is_dir($dir)){
		if (($handle = opendir($dir)) !== false){
			while (($item = readdir($handle)) !== false){
				if ($item != "." and $item != ".."){
					echo "Removendo $dir/$item<br>";
					if (is_dir("$dir/$item"))
					removerDiretorio("$dir/$item");
					else
					unlink("$dir/$item");
				}
			}
			closedir($handle);
			rmdir($dir);
		}
	}
}


if (is_writable($dir)){

	removerDiretorio($dir.'cache');

	clearstatcache();

	mkdir($dir.'cache');
	chmod($dir.'cache', 0770);

	if (is_dir($dir.'cache'))
	echo '<label>Diretório criado com sucesso</label>';
}else{
	echo '<b>Não é possivel escrever no diretório ['.$dir.']</b>';
}

?></fieldset>

<fieldset><legend>Cache dos templates</legend> <?php

function corrigirTemplates($dir){
	if (is_dir($dir)){
		if (($handle = opendir($dir)) !== false){
			while (($item = readdir($handle)) !== false){
				if ($item != "." and $item != ".."){
					if (is_dir("$dir/$item"))
					corrigirTemplates("$dir/$item");
					elseif (substr($item, -8) == '_montado'){
						echo "Removendo $dir/$item<br />";
						unlink("$dir/$item");
					}
				}
			}
			closedir($handle);
		}
	}
}

corrigirTemplates($dir);

?></fieldset>


</body>
</html>
