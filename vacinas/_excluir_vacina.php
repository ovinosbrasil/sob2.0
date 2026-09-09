<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../_config.php";

$id_vacina = $_GET['id_vacina'];
$id_lote = $_GET['id_lote'];

DBDelete('vacinas', "id = '$id_vacina'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=vacina&id_lote=$id_lote'>";
?>
