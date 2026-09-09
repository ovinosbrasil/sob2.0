<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../../_config.php";

$data = $_POST['data_inicial'];
include "../../funcoes_data/data.php";
$data_inicial = $data;
$data = $_POST['data_final'];
include "../../funcoes_data/data.php";
$data_final = $data;
// Calcula a diferença em segundos entre as datas
$diferenca = strtotime($data_final) - strtotime($data_inicial);
//Calcula a diferença em dias
$dias = floor($diferenca / (60 * 60 * 24));
$lote = $_POST['lote'];
$lote_ = DBRead('monta', "WHERE codigo = '$lote'");

// TESTE LOTE
if($lote_[0]['id'] > 0){
  echo "<script type=\"text/javascript\"> alert(\"Lote já existe.Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{


//TESTE DIFERENÇA DE DIAS
if($dias > 90){
  echo "<script type=\"text/javascript\"> alert(\"Diferença entre datas é maior que 90 dias. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$macho = $_POST['macho'];
$verifica_macho = DBRead('animais', "WHERE nome = '$macho' AND sexo = 'Macho'");
$verifica_macho_terceiro = DBRead('terceiros', "WHERE nome = '$macho' AND sexo = 'Macho'");

//TESTE MACHO
if(($verifica_macho[0]['id'] <= 0) && ($verifica_macho_terceiro[0]['id'] <= 0)){
  echo "<script type=\"text/javascript\"> alert(\"Animal não existe. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

if($verifica_macho[0]['id'] > 0){ $id_macho = $verifica_macho[0]['id']; $terceiro = 0; }
if($verifica_macho_terceiro[0]['id'] > 0){ $id_macho = $verifica_macho_terceiro[0]['id']; $terceiro = 1; }

$dados = array(
	'codigo'	=> $_POST['lote'],
	'data_inicio'	=> $data_inicial,
  'data_fim'	=> $data_final,
  'raca'  => $_POST['raca'],
  'notificacao'   => $_POST['notificacao'],
  'terceiro' => $terceiro,
  'id_animal' => $id_macho
);

DBcreate('monta', $dados);
$lote = DBRead('monta', "WHERE codigo = '$lote'");
$id_lote = $lote[0]['id'];


$dados = array(
	'id_lote'	=> $id_lote,
  'tipo'    => 0
);
DBcreate('lotes_reproducao', $dados);

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=monta&id_lote=$id_lote'>";
}}}
?>
