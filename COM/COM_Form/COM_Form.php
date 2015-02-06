<?php

class COM_Form{

	public static function addFormsJs(&$html){
		COM_jQuery::addMaskedInput($html);
		$html->addJS(URL.'core/com/Form/forms.js');
	}

	public static function radio($valores, $sel, $sep, $att = ''){
		return COM_Form::inputs('radio', $valores, $sel, $sep, $att);
	}

	public static function check($valores, $sel, $sep, $att = ''){
		return COM_Form::inputs('checkbox', $valores, $sel, $sep, $att);
	}
	
	public static function options($valores, $sel){
		$r = '';
		if (is_array($valores)){
			foreach ($valores as $val => $des)
			$r .= '<option value="'.COM_Form::espace($val).'"'.(COM_Form::selecionado($val, $sel) ? ' selected="selected"' : '').'>'.COM_Form::espace($des).'</option>';
		}elseif(is_a($valores, 'MysqlResult')){
			while($valores->next())
			$r .= '<option value="'.COM_Form::espace($valores->Record->value).'"'.(COM_Form::selecionado($valores->Record->value, $sel) ? ' selected="selected"' : '').'>'.COM_Form::espace($valores->Record->text).'</option>';
		}
		return $r;
	}

	protected static function inputs($type, $valores, $sel, $sep, $att = ''){
		$r = '';
		if (!empty($att))
		$att .= ' ';
		if (is_array($valores)){
			foreach ($valores as $val => $des)
			$r .= '<input type="'.$type.'" value="'.COM_Form::espace($val).'" '.(COM_Form::selecionado($val, $sel) ? 'checked="checked" ' : '').$att.'/> '.COM_Form::espace($des).$sep;
		}elseif(is_a($valores, 'MysqlResult')){
			while($valores->next())
			$r .= '<input type="'.$type.'" value="'.COM_Form::espace($valores->Record->value).'" '.(COM_Form::selecionado($valores->Record->value, $sel) ? 'checked="checked" ' : '').$att.'/> '.COM_Form::espace($valores->Record->text).$sep;
		}
		if (empty($sep))
		return $r;
		return substr($r, 0, strlen($sep)*-1);
	}

	protected static function espace($txt){
		return htmlentities($txt, ENT_QUOTES, defined('ENCODING') ? ENCODING : 'ISO-8859-1');
	}

	protected static function selecionado($v1, $v2){
		if (is_array($v2))
		return in_array($v1, $v2);
		return (string)$v1 == (string)$v2;
	}

}

?>