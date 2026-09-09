<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../../_config.php";

$dados = array(
	'qtd'	=> $_POST['qtd']
);

$id_embriao = $_GET['id_embriao'];
DBUpdate('embriao', $dados, "id  = '$id_embriao'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=embrioes'>";
?>
