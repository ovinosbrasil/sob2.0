<?
include "../_config.php";
$id_alerta = $_GET['id_alerta'];
DBDelete('alerta', "id = '$id_alerta'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php'>";
?>
