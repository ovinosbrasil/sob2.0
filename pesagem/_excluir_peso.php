<?
include "../_config.php";
$id_peso = $_GET['id_peso'];
$id_animal = $_GET['id_animal'];
DBDelete('pesagem', "id = '$id_peso'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=pesagem&id_animal=$id_animal'>";
?>
