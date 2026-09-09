<?
include "../_config.php";

$id_lote = $_GET['id_lote'];

DBDelete('vacinas', "id_lote = '$id_lote'");
DBDelete('lote_vacina', "id = '$id_lote'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=vacinas'>";
?>
