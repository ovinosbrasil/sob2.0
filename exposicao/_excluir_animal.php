<?
include "../_config.php";
$id_animal = $_GET['id_animal'];
$id_evento = $_GET['id_evento'];


DBDelete('julgamento_controle', "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
DBDelete('animais_evento', "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";
?>
