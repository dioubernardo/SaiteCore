<?php

class COM_Imagem{

	static function geraImagemProporcional($imagem, $largura, $altura, $arquivo = null){
		$img_original = COM_Imagem::abreImg($imagem);
		$larg_ori = imagesx($img_original);
		$altu_ori = imagesy($img_original);

		if (!empty($largura) and !empty($altura) and ($larg_ori > $largura or $altu_ori > $altura)){
			/* por na proporcao */
			if (($larg_ori/$largura) > ($altu_ori/$altura)){
				$larg_des = $largura;
				$altu_des = $altu_ori / ($larg_ori/$largura);
			}else{
				$altu_des = $altura;
				$larg_des = $larg_ori / ($altu_ori/$altura);
			}
		}elseif (!empty($largura) and $larg_ori > $largura){
			/* reduzir largura */
			$larg_des = $largura;
			$altu_des = $altu_ori / ($larg_ori/$larg_des);
		}elseif (!empty($altura) and $altu_ori > $altura){
			/* reduzir altura */
			$altu_des = $altura;
			$larg_des = $larg_ori / ($altu_ori/$altu_des);
		}else{
			/* o arquivo esta bom apenas escrever */
			if (empty($arquivo)){
				header('Content-type: image/jpg');
				readfile($imagem);
			}else{
				copy($imagem, $arquivo);
			}
			return;
		}

		$imagem_destino = imagecreatetruecolor($larg_des, $altu_des);
		imagecopyresampled($imagem_destino, $img_original, 0, 0, 0, 0, $larg_des, $altu_des, $larg_ori,$altu_ori);
		if (empty($arquivo)){
			header('Content-type: image/jpeg');
			imagejpeg($imagem_destino);
		}else
			imagejpeg($imagem_destino, $arquivo);
		imagedestroy($imagem_destino);
		imagedestroy($img_original);
	}

	static function geraImagemRecorte($imagem, $largura, $altura, $arquivo = null){
		$img_original = COM_Imagem::abreImg($imagem);
		$larg_ori = imagesx($img_original);
		$altu_ori = imagesy($img_original);

		$altu_des = $altura;
		$larg_des = $larg_ori / ($altu_ori/$altu_des);

		if($larg_des < $largura){
			$larg_des = $largura;
			$altu_des = $altu_ori / ($larg_ori/$larg_des);
		}

		$margemTop = (($altu_des - $altura) / 2) * (-1);
		$margemLeft = (($larg_des - $largura) / 2) * (-1);

		$imagem_destino = imagecreatetruecolor($largura, $altura);
		imagecopyresampled($imagem_destino, $img_original, $margemLeft, $margemTop, 0, 0, $larg_des, $altu_des, $larg_ori, $altu_ori);
		if (empty($arquivo)){
			header('Content-type: image/jpeg');
			imagejpeg($imagem_destino);
		}else
		imagejpeg($imagem_destino, $arquivo);
		imagedestroy($imagem_destino);
		imagedestroy($img_original);
	}

	function abreImg($img){
		$extensao = strtolower(pathinfo($img, PATHINFO_EXTENSION));
		if ($extensao == "gif")
		$img_original = imagecreatefromgif($img);
		else if ($extensao == "jpg" or $extensao == "jpeg")
		$img_original = imagecreatefromjpeg($img);
		else if ($extensao == "png")
		$img_original = imagecreatefrompng($img);

		if (!$img_original)
		$img_original = imagecreatefromstring(file_get_contents($img));

		if (!$img_original){
			$img_original = imagecreate(100,100);
			imagecolorallocate ($img_original, 255, 255, 255);
			imagestring ($img_original, 4, 30, 42,  "ERRO!", imagecolorallocate ($img_original, 255, 0, 0));
			$preto = imagecolorallocate ($img_original, 0, 0, 0);
			imageline($img_original, 0, 0, 99, 0, $preto);
			imageline($img_original, 0, 0, 0, 99, $preto);
			imageline($img_original, 99, 0, 99, 99, $preto);
			imageline($img_original, 0, 99, 99, 99, $preto);
		}
		return $img_original;
	}

}
?>