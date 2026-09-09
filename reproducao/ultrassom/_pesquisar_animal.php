<?
include "../../_config.php";

$chip = $_POST['mae'];
$animal = DBRead('animais', "WHERE chip = '$chip'");
$id_animal = $animal[0]['id'];

$animal = DBRead('animais', "WHERE nome = '$chip'");
$id_animal2 = $animal[0]['id'];

$animal = DBRead('terceiros', "WHERE nome = '$chip'");
$id_animal3 = $animal[0]['id'];
$terceiro =0;

if($id_animal2){
  $id_animal = $id_animal2;
}
if($id_animal3){
  $id_animal = $id_animal3;
  $terceiro=1;
}

if((!$id_animal) && (!$id_animal2) && (!$id_animal3)){
  echo "<script type=\"text/javascript\">
   alert(\"Animal não cadastrado.\");
   window.history.go(-1);
   </script>";
}else{
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=lista_ultrassom&id_animal=$id_animal&tipo=0&terceiro=$terceiro'>";
}
?>
