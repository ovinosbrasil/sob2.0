<?
include "../_config.php";
$id_animal = $_GET['id_animal'];
$id_evento = $_GET['id_evento'];
$tipo = $_GET['tipo'];

$dados = array(
  'julgamento'	=> 0
);
DBUpdate('animais_evento', $dados, "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
DBDelete('julgamento_controle', "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");

$animal = DBRead('animais', "WHERE id = '$id_animal'");
$id_pai = $animal[0]['pai'];
$id_mae = $animal[0]['mae'];
if($animal[0]['terceiro_pai'] == 0){
  $progenie_pai = DBRead('progene', "WHERE id_lote = '$id_evento' AND id_animal = '$id_pai'");
  if($progenie_pai[0]['id']>0){
    $qtd = $progenie_pai[0]['qtd']-1;
    $dados = array(
      'qtd'	=> $qtd
    );
    DBUpdate('progene', $dados, "id_lote = '$id_evento' AND id_animal = '$id_pai'");
}}

if($animal[0]['terceiro_mae'] == 0){
  $progenie_mae = DBRead('progene', "WHERE id_lote = '$id_evento' AND id_animal = '$id_mae'");
  if($progenie_mae[0]['id']>0){
    $qtd = $progenie_mae[0]['qtd']-1;
    $dados = array(
      'qtd'	=> $qtd
    );
    DBUpdate('progene', $dados, "id_lote = '$id_evento' AND id_animal = '$id_mae'");
}}

if($tipo){
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=julgamento&id_exposicao=$id_evento'>";
}else{
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";
}
?>
