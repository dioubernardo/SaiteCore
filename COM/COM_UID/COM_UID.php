<?php

class COM_UID{

	function create(&$html, $id, $ev){
		COM_jQuery::add($html);
		COM_SWFObject::add($html);
		$html->addJS(URL.'core/com/UID/uid.js');
		$html->addScript($id.'=new uid("'.$id.'"'.(empty($ev) ? '' : ','.$ev).')');
	}

	function validarPOST(){
		$a = '$1$'.urldecode($_POST['IDC']);
		if (crypt(CHAVE_ID, $a) != $a){
			COM_UID::retorno('Chave inválida');
			exit;
		}
	}

	function retorno($m){
		echo 's='.urlencode(utf8_encode(addslashes($m)));
	}

}

?>