<?
include "../../_config.php";

$id = $_GET['id'];
$vacina = DBRead('vacinas', "WHERE id = '$id'");
$id_animal = $vacina[0]['id_animal'];

DBDelete('vacinas', "id = '$id'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=vacina'>";
?>
