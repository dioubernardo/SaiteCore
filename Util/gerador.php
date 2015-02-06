<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Gerador</title>
</head>
<body>
<form action="gerador.php" method="post">Servidor <?php input("servidor")?><br />
Usuário <?php input("usuario")?><br />
Senha <?php input("senha")?><br />
Banco <?php input("banco")?><br />
Tabela <?php input("tabela")?><br />
URL <?php input("url")?><br />
Titulo <?php input("titulo")?><br />
<input type="submit" value="ver" /> <input type="hidden" name="anterior"
	value="<?php echo $_POST['tabela']?>" />

<table border="1">
	<tr>
		<td>Campo</td>
		<td>Li</td>
		<td>In</td>
		<td>Al</td>
		<td>Ob</td>
		<td>Fi</td>
		<td>Tamanho</td>
	</tr>
	<?php

	$link = mysql_connect($_POST['servidor'], $_POST['usuario'], $_POST['senha']);
	if (!$link)
	die('Could not connect: ' . mysql_error());

	$db_selected = mysql_select_db($_POST['banco'], $link);
	if (!$db_selected)
	die ('Can\'t use foo : ' . mysql_error());

	$re = mysql_query('show fields from '.$_POST['tabela']);

	if ($_POST['anterior'] != $_POST['tabela'])
	unset($_POST);

	while($li = mysql_fetch_object($re)){
		echo '<tr><td>';

		input('campos_'.$li->Field, $li->Field);
		echo '</td><td>';
		check('listagem_'.$li->Field, '');
		echo '</td><td>';
		check('insercao_'.$li->Field, '');
		echo '</td><td>';
		check('alteracao_'.$li->Field, '');
		echo '</td><td>';
		check('obrigatorio_'.$li->Field, '');
		echo '</td><td>';
		check('filtro_'.$li->Field, '');
		echo '</td><td>';
		input('tamanho_'.$li->Field, getTamanho($li->Type));
		echo '</td><td>';

		echo '</tr>';
	}

	?>
</table>
</form>
	<?php
	echo $_POST['url'],'.php<br />';

	$listagem = lista('listagem', "'@#'", ", ", 0);
	$listagemSQL = lista('listagem', "@#", ",\n", 4);
	$setNovo = lista('insercao', "\$html->set('@#', '');", "\n", 2);
	$setEditar = lista('alteracao', "\$html->set('@#', \$co->Record->@#);", "\n", 2);
	$editarSQL = lista('alteracao', "@#", ",\n", 4);
	$vetorNovo = lista('insercao', "'@#' => \$_POST['@#']", ",\n", 3);
	$vetorEditar = lista('alteracao', "'@#' => \$_POST['@#']", ",\n", 3);
	$obrigatorios = lista('obrigatorio', "empty(\$_POST['@#']) && COM_AjaxPostForm::alert('Você deve definir o @#',true);", "\n", 2);
	$filtro = lista('filtro', "\t\tif (!empty(\$_POST['@#']))\n\t\t\t\$sql .= ' and @# like \"%'.addslashes(\$_POST['@#']).'%\"';", "\n", 0);


	echo '<textarea style="width:100%;height:200px">';
	$html = <<<EOT
<?php
require_once DIR_BASE.'libs/class.CRUD.php';

class {$_POST['url']} extends cmsCRUD{

	var \$tplListagem = '{$_POST['url']}/listagem';
	var \$camposListagem = array(
	{$listagem}
	);

	function __construct(){
		parent::__construct(__CLASS__);
	}

	protected function obtemSQLListagem(){
		\$sql = '
			select
			{$listagemSQL},
				id
			from
			{$_POST['tabela']}
			where
				1
		';
		{$filtro}
		return \$sql;
	}

	protected function montaOperacoes(\$re){
		return '<a class="ico i1" href="'.URL.'{$_POST['url']}/editar/'.\$re->id.'"></a><a class="ico i2 ajax" href="'.URL.'{$_POST['url']}/delete/'.\$re->id.'"></a>';
	}

	function adicionar(){
		\$html = &cmsCMS::getHTML('{$_POST['url']}/formulario');
		COM_AjaxPostForm::add(\$html);

		\$html->set('form.operacao', 'Inserção');
		\$html->set('form.funcaoOperacao', 'insert');

		{$setNovo}

		\$html->monta();
	}

	function editar(\$p){
		\$html = &cmsCMS::getHTML('{$_POST['url']}/formulario');
		COM_AjaxPostForm::add(\$html);

		\$id = (int)\$p[0];
		\$html->set('form.operacao', 'Alteração');
		\$html->set('form.funcaoOperacao', 'update/'.\$id);

		\$db = &COM_DB::obtem();
		\$co = &\$db->query("
			select
			{$editarSQL}
			from
			{$_POST['tabela']}
			where
				id = '{\$id}'
		");
		\$co->next();

		{$setEditar}

		\$html->monta();
	}

	function POST_delete(\$p){
		\$db = &COM_DB::obtem();
		\$db->abreTransacao();
		\$id = (int)\$p[0];
		\$db->delete('{$_POST['tabela']}', "id ={\$id}");
		if (\$db->affectedRows() == 0)
			COM_AjaxPostForm::alert('Não foi possível remover', true);
		\$db->salvaTransacao();
		COM_AjaxPostForm::exec('Lst.carregar();');
		COM_AjaxPostForm::alert('Registro removido com sucesso');
	}

	protected function testeObrigatorios(){
	{$obrigatorios}
	}

	function POST_insert(){
		\$this->testeObrigatorios();
		
		\$db = &COM_DB::obtem();
		\$db->abreTransacao();

		\$r = \$db->insert('{$_POST['tabela']}', array(
		{$vetorNovo}
		));
		if (!\$r)
			COM_AjaxPostForm::alert("Não foi possível inserir.\\n".\$db->obtemErro(), true);
		
		\$id = \$db->lastInsertId();

		\$db->salvaTransacao();
		COM_AjaxPostForm::alert('Dados inseridos com sucesso');
		COM_AjaxPostForm::location(URL.'{$_POST['url']}');
	}

	function POST_update(\$p){
		\$this->testeObrigatorios();

		\$db = &COM_DB::obtem();
		\$db->abreTransacao();
		\$id = (int)\$p[0];

		\$r = \$db->update('{$_POST['tabela']}', array(
		{$vetorEditar}
		), "id = '{\$id}'");
		if (!\$r)
			COM_AjaxPostForm::alert("Não foi possível alterar.\\n".\$db->obtemErro(), true);
				
		\$db->salvaTransacao();
		COM_AjaxPostForm::alert('Dados alterados com sucesso');
		COM_AjaxPostForm::location(URL.'{$_POST['url']}');
	}

}
?>
EOT;
		echo htmlentities($html);
		echo '</textarea>';

		$filtro = lista('filtro', "<div>\n\t\t<label>@#</label><input type=\"text\" name=\"@#\"/>\n\t</div>", "\n", 1);
		$tdListagem = lista('listagem', "<td><a href=\"#\">@#</a></td>", "\n", 3);

		echo 'listagem.html<br /><textarea style="width:100%;height:200px">';
		$html = <<<EOT
<!-- importar: ../interno.html -->

<!-- conteudo: cms.titulo -->
{$_POST['titulo']}
<!-- conteudo -->

<!-- conteudo: cms.corpo -->
<form method="post" action="#" class="cfiltro">
{$filtro}
	<div>
		<input type="button" value="filtrar" class="bt" />
	</div>
	<br clear="all" />
</form>

<a href="{URL}{$_POST['url']}/adicionar"><i class="ico i3"></i> Adicionar</a><br />
<table class="cgrid">
	<thead>
		<tr>
		{$tdListagem}
			<td>&nbsp;</td>
		</tr>
	</thead>
	<tbody><tr><td>&nbsp;</td></tr></tbody>
</table>
<div class="cpag"></div>
<script type="text/javascript">
	Lst.init('{URL}{$_POST['url']}/listagem');
</script>
<!-- conteudo -->
EOT;
		echo htmlentities($html);
		echo '</textarea>';

		$jafoi = array();

		$campos = '';
		foreach ($_POST as $ind => $valor){
			if ($valor == 'S' and preg_match('/^(insercao|alteracao)_(.+)/', $ind, $dados)){
				if (in_array($dados[2], $jafoi))
				continue;

				$campos .= "\t\t<div class=\"l\">\n";
				$campos .= "\t\t\t<label>{$dados[2]}";
				if ($_POST['obrigatorio_'.$dados[2]])
				$campos .= " *";
				$campos .= "</label>\n";
				$campos .= "\t\t\t<input name=\"{$dados[2]}\" class=\"g\" value=\"{{$dados[2]}}\" type=\"text\"";
				if (!empty($_POST['tamanho_'.$dados[2]]))
				$campos .= " maxlength=\"{$_POST['tamanho_'.$dados[2]]}\"";
				$campos .= " />\n";
				$campos .= "\t\t</div>\n";

				$jafoi[] = $dados[2];
			}
		}
		$campos = rtrim($campos);


		echo 'formulario.html<br /><textarea style="width:100%;height:200px">';
		$html = <<<EOT
<!-- importar: ../interno.html -->

<!-- conteudo: cms.titulo -->
{$_POST['titulo']} - {form.operacao}
<!-- conteudo -->

<!-- conteudo: cms.corpo -->
<form method="post" action="{URL}{$_POST['url']}/{form.funcaoOperacao}" class="cform">
	<p>Preencha os campos abaixo:</p>
	<fieldset>
		<legend>Informações</legend>
		{$campos}
	</fieldset>
	<div class="actions">
		<a class="bt bt-voltar" title="voltar" href="{URL}{$_POST['url']}">voltar</a>
		<input class="bt bt-salvar APF" type="submit" value="salvar" />
	</div>	
</form>
<!-- conteudo -->
EOT;
		echo htmlentities($html);
		echo '</textarea>';

		?>

</body>
</html>
<?php

function input($nome, $valor = ''){
	echo "<input type='text' style='width:150px' name='{$nome}' value='".(isset($_POST) ? $_POST[$nome] : $valor)."' />";
}

function check($nome, $valor = ''){
	echo
"<input type='checkbox' name='{$nome}' value='S' ",
	(
	((isset($_POST) and $_POST[$nome] == 'S') or (!isset($_POST) and $valor == 'S')) ?
		"checked='checked'"
		:
		''
		),"/>";
}

function getTamanho($tp){
	switch($tp){
		case 'date':
			return 10;
	}
	if (preg_match('/\(([0-9]+)\)/', $tp, $dados))
	return $dados[1];
	if (preg_match('/\(([0-9]+),([0-9]+)\)/', $tp, $dados))
	return $dados[1]+1;
	return '';
}

function lista($nome, $tpl, $fl, $ident){
	$nome .= '_';
	$l = strlen($nome);
	$ret = '';
	foreach ($_POST as $ind => $valor){
		if ($valor == 'S' and substr($ind,0, $l) == $nome){
			$ret .= str_repeat("\t", $ident).str_replace('@#', substr($ind,$l), $tpl).$fl;
		}
	}
	$ret = substr($ret, 0, strlen($fl) * -1);
	return $ret;
}
?>