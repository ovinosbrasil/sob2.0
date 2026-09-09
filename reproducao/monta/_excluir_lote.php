<?
include "../../_config.php";

$id_lote = $_GET['id_lote'];

DBDelete('monta_controle', "id_monta = '$id_lote'");
DBDelete('monta', "id = '$id_lote'");
DBDelete('lotes_reproducao', "id_lote = '$id_lote' AND tipo = '0'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=lista_monta'>";
?>
