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

<fieldset><legend>Diretório do cache</legend> <?php

function removerDiretorio($dir){
	if (is_dir($dir)){
		if (($handle = opendir($dir)) !== false){
			while (($item = readdir($handle)) !== false){
				if ($item != "." and $item != ".."){
					echo "Removendo $dir/$item<br>";
					if (is_dir("$dir/$item")){
						removerDiretorio("$dir/$item");
						rmdir("$dir/$item");
					}else
					unlink("$dir/$item");
				}
			}
			closedir($handle);
		}
	}
}

removerDiretorio($dir.'cache');

if (is_dir($dir.'cache'))
echo '<label>Diretório criado com sucesso</label>';
?></fieldset>

</body>
</html>
