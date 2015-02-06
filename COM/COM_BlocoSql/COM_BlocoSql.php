<?php

class COM_BlocoSql{

	static function &parametros(){
		$r = new stdClass();
		$r->free = true;
		$r->raw = array();
		$r->callback = array();
		return $r;
	}

	static function monta(&$html, $nome, &$r, $p = null){
		if (is_null($p))
			$p = &COM_BlocoSql::parametros();
		$bloco = &$html->obtemBloco($nome);
		if (!is_null($bloco)){
			$itens = &$bloco->obtemBloco('itens');
			if (is_null($itens)){
				// bloco simples
				if ($r->rows())
					COM_BlocoSql::_escreve($nome, $r, $bloco, $p);
			}else{
				// bloco composto
				$vazio = &$bloco->obtemBloco('vazio');
				if ($r->rows()){
					COM_BlocoSql::_escreve($nome, $r, $itens, $p);
					$bloco->set($itens);
					$bloco->set($vazio);
				}else{
					$bloco->set($itens);
					if (!is_null($vazio))
						$vazio->parse();
					$bloco->set($vazio);
				}
				$bloco->parse();
			}
			$html->set($bloco);
		}
		$p->free && $r->free();
	}

	private function _escreve($nome, &$r, &$b, &$p){
		while ($r->next()){
			foreach ($r->Record as $chave => $valor){
				if (isset($p->callback[$chave]))
					$valor = call_user_func($p->callback[$chave], $valor, $r->Record);
				$b->set($nome.'.'.$chave, $valor, in_array($chave, $p->raw));
			}
			$b->parse();
		}
	}

}
?>