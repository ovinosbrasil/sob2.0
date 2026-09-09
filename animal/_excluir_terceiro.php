<?
include "../_config.php";

$id_animal = $_GET['id_animal'];

DBDelete('terceiros', "id = '$id_animal'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=lista_terceiros'>";
?>
