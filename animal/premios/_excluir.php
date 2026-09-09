<?
include "../../_config.php";

$id = $_GET['id'];
$premio = DBRead('premio', "WHERE id = '$id'");
$id_animal = $premio[0]['id_animal'];

DBDelete('premio', "id = '$id'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=premios'>";
?>
