<?
include "../../_config.php";

$id_venda = $_GET['id_venda'];

DBDelete('venda_semen', "id = '$id_venda'");
DBDelete('controle_financeiro', "id_semen = '$id_venda'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=vendas_semen'>";
?>
