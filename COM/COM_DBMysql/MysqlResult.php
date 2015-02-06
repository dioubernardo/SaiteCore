<?php

class MysqlResult{

	var $resultado;
	var $Record;

	function MysqlResult($r){
		$this->resultado = $r;
	}

	function next($typeNumeric = false){
		if (!$this->resultado)
			return false;
		if ($typeNumeric)
			return $this->Record = @mysql_fetch_row($this->resultado);
		return $this->Record = @mysql_fetch_object($this->resultado);
	}

	function seek($p){
		if (!$this->resultado)
			return false;
		return @mysql_data_seek($this->resultado, $p);
	}

	function rows(){
		return @mysql_num_rows($this->resultado);
	}

	function free(){
		@mysql_free_result($this->resultado);
		$this->resultado = null;
	}

}

?>