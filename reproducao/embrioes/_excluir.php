<?
include "../../_config.php";

$id_embriao = $_GET['id_embriao'];

DBDelete('embriao', "id= '$id_embriao'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=embrioes'>";
?>
