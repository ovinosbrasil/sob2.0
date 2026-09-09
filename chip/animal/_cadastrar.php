<?
include "../../_config.php";
$chip = $_POST['chip'];
$id_animal = $_GET['id_animal'];

$dados = array(
  'chip' => ''
);
DBUpdate('animais', $dados, "chip = '$chip'");
$dados = array(
  'chip' => $chip
);
DBUpdate('animais', $dados, "id = '$id_animal'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=cadastrar_chip'>";
?>
