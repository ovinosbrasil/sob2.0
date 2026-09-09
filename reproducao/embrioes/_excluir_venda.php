<?
include "../../_config.php";

$id_venda = $_GET['id_venda'];

DBDelete('venda_embriao', "id = '$id_venda'");
DBDelete('controle_financeiro', "id_embriao = '$id_venda'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=vendas_embriao'>";
?>
