<?
include "../../_config.php";

$chip = $_POST['animal'];
$animal = DBRead('animais', "WHERE chip = '$chip'");
$id_animal = $animal[0]['id'];

$animal = DBRead('animais', "WHERE nome = '$chip'");
$id_animal2 = $animal[0]['id'];

if($id_animal2){
  $id_animal = $id_animal2;
}

if((!$id_animal) && (!$id_animal2)){
  echo "<script type=\"text/javascript\">
   alert(\"Animal não cadastrado.\");
   window.history.go(-1);
   </script>";
}else{
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal'>";
}
?>
