<?
include "../../_config.php";

$id_lote = $_GET['id_lote'];

DBDelete('transplante', "id = '$id_lote'");
DBDelete('transplante_controle', "id_lote = '$id_lote'");
DBDelete('lotes_reproducao', "id_lote = '$id_lote' AND tipo = '2'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=lista_te'>";
?>
