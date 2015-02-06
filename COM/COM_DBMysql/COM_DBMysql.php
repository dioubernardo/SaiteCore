<?php
require_once DIR_SAITE.'COM/COM_DB/COM_DB.php';
require_once DIR_SAITE.'COM/COM_DBMysql/MysqlResult.php';

class COM_DBMysql extends COM_DB{

	var $conexao;

	function COM_DBMysql($serv, $usuario, $senha, $base){
		$this->conexao = mysql_connect($serv, $usuario, $senha);
		if (!$this->conexao)
			COM_DB::erro(__CLASS__, 'Não foi possivel conectar, '.mysql_error());
		if (!mysql_select_db($base, $this->conexao))
			COM_DB::erro(__CLASS__, 'Não foi possivel selecionar a base de dados, '.mysql_error($this->conexao));
		if (defined('ENCODING_DB')){
			if (function_exists('mysql_set_charset'))
				mysql_set_charset(ENCODING_DB, $this->conexao);
			$this->exec('SET CHARACTER SET "'.ENCODING_DB.'"');
			$this->exec('SET NAMES "'.ENCODING_DB.'"');
		}
	}

	function obtemErro(){
		return mysql_error($this->conexao);
	}

	function exec($query, $marcacoes = null){
		if (is_array($marcacoes))
			foreach ($marcacoes as $marcacao => $valor)
				$query = str_replace('{'.$marcacao.'}', mysql_real_escape_string($valor), $query);
		$r = @mysql_query($query, $this->conexao);
		if (!$r)
			COM_DB::erro(__CLASS__, "Erro ao executar a querie:<br />{$query}<br /><br />".mysql_error($this->conexao));
		return $r;
	}

	function abreTransacao(){
		return $this->exec('START TRANSACTION');
	}

	function salvaTransacao(){
		return $this->exec('COMMIT');
	}

	function cancelaTransacao(){
		return $this->exec('ROLLBACK');
	}

	function &query($query, $marcacoes = null){
		$r = new MysqlResult($this->exec($query, $marcacoes));
		return $r;
	}

	function insert($tabela, $campos, $ignore = false){
		return $this->exec($this->SQLInsert($tabela, $campos, $ignore)) and $this->affectedRows();
	}

	function update($tabela, $valores, $condicao){
		return $this->exec($this->SQLUpdate($tabela, $valores, $condicao));
	}

	function delete($tabela, $condicao){
		return $this->exec($this->SQLDelete($tabela, $condicao));
	}

	function affectedRows(){
		return @mysql_affected_rows($this->conexao);
	}

	function lastInsertId(){
		return @mysql_insert_id($this->conexao);
	}

}
?>