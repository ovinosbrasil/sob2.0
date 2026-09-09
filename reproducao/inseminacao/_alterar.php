<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../../_config.php";

$id_lote = $_GET['id_lote'];
$data = $_POST['data_inicial'];
include "../../funcoes_data/data.php";

$lote = $_POST['lote'];
$lote_ = DBRead('inseminacao', "WHERE codigo = '$lote' AND id != '$id_lote'");

// TESTE LOTE
if($lote_[0]['id'] > 0){
  echo "<script type=\"text/javascript\"> alert(\"Lote já existe.Tente novamente\"); </script>
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
	'data'	=> $data,
  'semen'	=> $_POST['semen'],
  'raca'  => $_POST['raca'],
  'notificacao'   => $_POST['notificacao'],
  'terceiro' => $terceiro,
  'id_macho' => $id_macho
);

DBUpdate('inseminacao', $dados, "id = '$id_lote'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=inseminacao&id_lote=$id_lote'>";
}}
?>
