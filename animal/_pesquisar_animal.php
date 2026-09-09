<?
include "../_config.php";

$chip = $_POST['animal'];
$animal = DBRead('animais', "WHERE chip = '$chip'");
$id_animal = $animal[0]['id'];

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=animal&id_animal=$id_animal'>";
?>
