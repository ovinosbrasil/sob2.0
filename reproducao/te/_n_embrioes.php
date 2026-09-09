<?
include "../../_config.php";

$id_te = $_GET['id_te'];
$id_lote = $_GET['id_lote'];
$valor = $_GET['valor'];

$dados = array(
	'n_embrioes'	=> $valor,
);
DBUpdate('transplante_controle', $dados, "id = '$id_lote'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=te&id_lote=$id_te'>";
?>
