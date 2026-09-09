<?
include "../../_config.php";

$id = $_GET['id'];
$doenca = DBRead('doencas', "WHERE id = '$id'");
$id_animal = $doenca[0]['id_animal'];

DBDelete('doencas', "id = '$id'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=doenca'>";
?>
