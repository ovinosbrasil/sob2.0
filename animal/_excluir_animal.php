<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

set_time_limit(9999999999999);
include "../_config.php";

$id_animal = $_GET['id_animal'];
$animal = DBRead('animais', "WHERE id = '$id_animal'");
DBDelete('animais', "id = '$id_animal'");
DBDelete('acasalamento', "id_animal = '$id_animal'");
if($animal[0]['sexo'] == "Fêmea"){
  DBDelete('acasalamento_controle', "id_femea = '$id_animal'");
  DBDelete('matriz', "id_femea = '$id_animal'");
}else{
  DBDelete('reprodutor', "id_macho = '$id_animal'");
}
DBDelete('animais_evento', "id_animal = '$id_animal'");
DBDelete('avaliacao', "id_animal = '$id_animal'");
DBDelete('compras', "id_animal = '$id_animal'");
DBDelete('doencas', "id_animal = '$id_animal'");

$id_pai = $animal[0]['pai'];
include "_ranking_reprodutor.php";
$id_mae = $animal[0]['mae'];
include "_ranking_matriz.php";

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php'>";
?>
