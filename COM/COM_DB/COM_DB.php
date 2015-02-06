<?php

class COM_DB{

	/*
	 Com base nos dados definidos no arquivo de configuração obtem uma instancia para acesso ao banco de dados
	 */
	static function &obtem(){
		static $o;
		if (!is_null($o))
			return $o;
		switch (SGBD){
			default:
				require_once DIR_SAITE.'COM/COM_DBMysql/COM_DBMysql.php';
				$o = new COM_DBMysql(SERVIDOR, USUARIO, SENHA, BASE_DE_DADOS);
				return $o;
		}
	}

	function SQLInsert($tabela, $campos, $ignore = false){
		$str_campos = '';
		$str_valores = '';
		if (is_array($campos)){
			foreach ($campos as $campo => $valor){
				$str_campos .= $campo.',';
				$str_valores .= $this->trataValor($valor).',';
			}
			$campos = substr($str_campos, 0, -1);
			$valores = substr($str_valores, 0, -1);
		}
		return 'INSERT '.($ignore ? 'IGNORE ' : '') . 'INTO ' . $tabela . ' (' . $campos . ') VALUES (' . $valores . ')';
	}

	function SQLUpdate($tabela, $campos, $condicao){
		return 'UPDATE ' . $tabela . ' SET ' . $this->montaString($campos, ',') . $this->montaCondicao($condicao);
	}

	function SQLDelete($tabela, $condicao){
		return 'DELETE FROM ' . $tabela . $this->montaCondicao($condicao);
	}

	function montaCondicao($c){
		$c = $this->montaString($c, ' AND');
		return empty($c) ? '' : (' WHERE '.$c);
	}

	function montaString($s, $sep){
		if (is_array($s)){
			$a = '';
			foreach ($s as $campo => $valor)
			$a .= $campo.'='.$this->trataValor($valor).$sep;
			$s = substr($a, 0, strlen($sep)*-1);
		}
		return $s;
	}

	function trataValor($valor){
		if (is_null($valor))
		return 'NULL';
		return '\''.mysql_real_escape_string($valor).'\'';
	}

	function erro($class, $msg){
		echo '<h3>Erro no banco de dados ',$class,'<h3><pre>',$msg,'</pre>';
	}

}
?>