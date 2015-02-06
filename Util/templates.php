<?php
$dir = realpath($_SERVER["argv"][1]);
$dir = str_replace('\\','/', $dir);

echo "Atualizando arquivo da pasta: " , $dir, "\n";

if (!is_dir($dir))
die("Diretório inválido");

atualizar($dir);

function atualizar($dir){
	if ($dh = opendir($dir)){
		while (($file = readdir($dh)) !== false){
			if ($file == '.' or $file == '..')
			continue;
			if (is_dir($dir.'/'.$file))
			atualizar($dir.'/'.$file);
			else{
				$ext = pathinfo($dir.'/'.$file, PATHINFO_EXTENSION);
				if ($ext != 'html' and $ext != 'htm' and $ext != 'tpl')
				continue;

				$conteudo = @file_get_contents($dir.'/'.$file);
				if ($conteudo === false){
					echo 'Não foi possivel ler ',$dir,'/',$file."\n";
					continue;
				}
				if (preg_match('/<!--\s+InstanceBegin\s+template="([^"]+)"[^>]+-->/', $conteudo, $deft)){
					$template = $deft[1];
					echo 'O arquivo ',$dir,'/'.$file,' usa o template ',$template,"\n";
					$conteudoTemplate = file_get_contents($dir.'/'.$template);
					if ($conteudoTemplate === false){
						echo "Template inválido\n";
						continue;
					}
					if (preg_match_all('/<!-- TemplateBeginEditable name="([^"]+)" -->/', $conteudoTemplate, $marcacoes)){
						foreach ($marcacoes[1] as $marc){
							$abre = '<!-- TemplateBeginEditable name="'.$marc.'" -->';
							$contAr = getConteudo($conteudo, $abre, '<!-- TemplateEndEditable -->');
							if (!empty($contAr)){
								$conteudoTemplate = str_replace(
								getConteudo($conteudoTemplate, $abre, '<!-- TemplateEndEditable -->'),
								$contAr,
								$conteudoTemplate
								);
							}
						}
					}
					$conteudoTemplate = $conteudoTemplate."\n".$deft[0];
					file_put_contents($dir.'/'.$file, $conteudoTemplate);
				}
			}
		}
		closedir($dh);
	}
}

function getConteudo($c, $ini, $fim){
	$pi = strpos($c, $ini);
	$pf = strpos($c, $fim, $pi);
	if ($pi !== false and $pf !== false){
		return substr($c, $pi, $pf-$pi+strlen($fim));
	}
	return '';
}

?>