<?php

class COM_Conversao{

	public static function inteiro($valor){
		return preg_replace('/[^0-9]/', '', $valor);
	}

	public static function data($valor){
		$valor = explode('/', $valor);
		return sprintf("%04d-%02d-%02d", $valor[2], $valor[1], $valor[0]);
	}

	public static function dataTela($valor){
		$valor = explode('-', $valor);
		return sprintf("%02d/%02d/%04d", $valor[2], $valor[1], $valor[0]);
	}

	public static function dataHoraMinuto($valor){
		if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}:\d{2})/', $valor, $partes))
			return "{$partes[3]}-{$partes[2]}-{$partes[1]} {$partes[4]}:00";
		return null;
	}

	public static function dataHoraMinutoTela($valor){
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}:\d{2})/', $valor, $partes))
			return "{$partes[3]}/{$partes[2]}/{$partes[1]} {$partes[4]}";
		return "";
	}	
	
	public static function decimal($valor, $decimal){
		return round(str_replace(",", ".", $valor), $decimal);
	}

	public static function decimalTela($valor, $decimal){
		if ($valor == 0)
		return '';
		return number_format($valor, $decimal, ',', '');
	}

	public static function strtolower($valor){
		return strtolower(strtr($valor,
			"ְֱֲֳִֵַָֹÊֻּֽ־ֿׁׂ׃װױײÙÚÛÜÝ",
			"אבגדהוחטיךכלםמןסעףפץצשתûü‎"
			));
	}

	public static function ucwords($valor){
		$valor = COM_Conversao::strToLower($valor);
		$valor = ucwords($valor);
		$valor = str_replace(
		array(" E ", " De ", " Da ", " Do ", " No ", " Na ", " Em ", " Ou ", " S/a ", " S.a. "),
		array(" e ", " de ", " da ", " do ", " no ", " na ", " em ", " ou ", " S/A ", " S.A. "),
		$valor
		);
		return $valor;
	}

	public static function strtoupper($valor){
		return strtoupper(strtr($valor,
			"אבגדהוחטיךכלםמןסעףפץצשתûü‎",
			"ְֱֲֳִֵַָֹÊֻּֽ־ֿׁׂ׃װױײÙÚÛÜÝ"
			));
	}

	public static function semAcentos($valor){
		return strtr($valor,
			"¥µְֱֲֳִֵֶַָֹÊֻּֽ־ֿ׀ׁׂ׃װױײ״ÙÚÛÜÝßאבגדהוזחטיךכלםמןנסעףפץצרשתûü‎ÿ",
			"SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy"
			);
	}

	public static function nomesArquivos($valor){
		return preg_replace("/[^A-Z0-9.-]/i", "_", COM_Conversao::semAcentos($valor));
	}

}

?>