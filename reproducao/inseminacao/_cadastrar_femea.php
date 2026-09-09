<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../../_config.php";

$mae = $_POST['mae'];
$id_lote = $_GET['id_lote'];
$verifica_mae = DBRead('animais', "WHERE nome = '$mae' AND sexo = 'Fêmea'");
$verifica_mae_terceiro = DBRead('terceiros', "WHERE nome = '$mae' AND sexo = 'Fêmea'");
$verifica_chip = DBRead('animais', "WHERE chip = '$mae' AND sexo = 'Fêmea'");


//TESTE MAE
if(($verifica_mae[0]['id'] <= 0) && ($verifica_mae_terceiro[0]['id'] <= 0) && ($verifica_chip[0]['id'] <= 0)){
  echo "<script type=\"text/javascript\"> alert(\"Animal não existe. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}

if(($verifica_mae[0]['id'] >= 1) || ($verifica_mae_terceiro[0]['id'] >= 1) || ($verifica_chip[0]['id'] >= 1)){

if($verifica_mae[0]['id'] > 0){ $id_mae = $verifica_mae[0]['id']; $terceiro = 0; }
if($verifica_mae_terceiro[0]['id'] > 0){ $id_mae = $verifica_mae_terceiro[0]['id']; $terceiro = 1; }
if($verifica_chip[0]['id'] > 0){ $id_mae = $verifica_chip[0]['id']; $terceiro = 0; }

$teste = DBRead('inseminacao_controle', "WHERE id_femea = '$id_mae' AND id_lote = '$id_lote'");
if($teste[0]['id'] > 0){
  echo "<script type=\"text/javascript\"> alert(\"Animal já cadastrado no lote. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{
$dados = array(
	'id_lote'	=> $_GET['id_lote'],
	'id_femea'	=> $id_mae,
  'terceiro' => $terceiro,
);

DBcreate('inseminacao_controle', $dados);

//RANKING IA
$qtd=$ultrassom=$nascimento=0;
$ultrassom = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote' AND ultrassom = '1'");
$nascimento = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote' AND status_nascimento = '1'");
$dados = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote'");
$qtd = count($dados);
  if($ultrassom[0]['id'] > 0){
    $ultrassom = count($ultrassom);
    $ultrassom = ($ultrassom*100)/$qtd;
  }else{
    $ultrassom = 0;
  }
  if($nascimento[0]['id'] > 0){
    $nascimento = count($nascimento);
    $nascimento = ($nascimento*100)/$qtd;
  }else{
    $nascimento = 0;
  }
$dados = array(
  'ultrassom' => $ultrassom,
  'crias'   =>  $nascimento,
  'femeas' => $qtd
);
DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_lote' AND tipo = '1'");
//RANKING IA FIM


echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=inseminacao&id_lote=$id_lote'>";
}}
?>
