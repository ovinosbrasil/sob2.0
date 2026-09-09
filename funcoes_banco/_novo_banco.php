<?
set_time_limit(9999999999999);
include "../_config.php";
$x = 1;
$animal = DBRead('animais');
foreach ($animal as $animal_) {
  $id_animal = $animal_['id'];
  $nome = 'SOB '.$x;
  $fbb = "O0".$x;
  $dados = array(
    'nome' => $nome,
    'tatuagem' => $x,
    'fbb' => $fbb
  );
  DBUpdate('animais', $dados, "id = '$id_animal'");
  $x++;
}
?>
