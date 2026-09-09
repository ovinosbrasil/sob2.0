<?
set_time_limit(9999999999999);
include "../_config.php";

$animal = DBRead('animais', "WHERE status = '0' AND (data_de_saida != '' || causa_da_perda != '')");
foreach ($animal as $animal_) {
  echo "a";
  $id_animal = $animal_['id'];
  $dados = array(
    'status' => 1,
  );
  DBUpdate('animais', $dados, "id = '$id_animal'");
}
?>
