<?php

$DIR_DIST = realpath(dirname(__FILE__).'/../'); 
require_once 'makeDist.php';

$dirSaite = realpath(dirname(__FILE__).'/../').'/dist/';
$versao = file_get_contents($dirSaite.'../Version.txt');

echo "Criando phar apartir de ",$dirSaite,"\n";
$phar = new Phar($dirSaite."../SaiteCore-{$versao}.phar", 0);
$phar->compress(Phar::NONE);
$ret = $phar->buildFromDirectory($dirSaite);

echo "Arquivos incluidos:\n";
foreach($ret as $a){
	echo " ",$a,"\n";
}

rrmdir($dirSaite);

function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}
?>