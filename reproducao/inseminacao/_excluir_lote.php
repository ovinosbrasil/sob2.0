<?
include "../../_config.php";

$id_lote = $_GET['id_lote'];

DBDelete('inseminacao_controle', "id_lote = '$id_lote'");
DBDelete('inseminacao', "id = '$id_lote'");
DBDelete('lotes_reproducao', "id_lote = '$id_lote' AND tipo = '1'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=lista_inseminacao'>";
?>
