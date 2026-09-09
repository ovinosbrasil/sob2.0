<?
include "../../_config.php";
$id_animal = $_GET['id_animal'];

$dados = array(
  'chip' => 0
);
DBUpdate('animais', $dados, "id = '$id_animal'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=cadastrar_chip'>";
?>
