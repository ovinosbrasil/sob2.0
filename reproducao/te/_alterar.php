<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../../_config.php";
$id_lote = $_GET['id_lote'];
$data = $_POST['data_inicial'];
include "../../funcoes_data/data.php";
$data_inicial = $data;
$data = $_POST['data_coleta'];
include "../../funcoes_data/data.php";
$data_coleta = $data;


$lote = $_POST['lote'];
$lote_ = DBRead('transplante', "WHERE codigo = '$lote' AND id != '$id_lote'");

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
  echo "<script type=\"text/javascript\"> alert(\"Macho não existe. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$femea = $_POST['femea'];
$verifica_femea = DBRead('animais', "WHERE nome = '$femea' AND sexo = 'Fêmea'");
$verifica_femea_terceiro = DBRead('terceiros', "WHERE nome = '$femea' AND sexo = 'Fêmea'");

//TESTE MACHO
if(($verifica_femea[0]['id'] <= 0) && ($verifica_femea_terceiro[0]['id'] <= 0)){
  echo "<script type=\"text/javascript\"> alert(\"Fêmea não existe. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

if($verifica_macho[0]['id'] > 0){ $id_macho = $verifica_macho[0]['id']; $terceiro_macho = 0; }
if($verifica_macho_terceiro[0]['id'] > 0){ $id_macho = $verifica_macho_terceiro[0]['id']; $terceiro_macho = 1; }
if($verifica_femea[0]['id'] > 0){ $id_femea = $verifica_femea[0]['id']; $terceiro_mae = 0; }
if($verifica_femea_terceiro[0]['id'] > 0){ $id_femea = $verifica_femea_terceiro[0]['id']; $terceiro_mae = 1; }

$dados = array(
	'codigo'	=> $_POST['lote'],
	'data'	=> $data_inicial,
  'terceiro_pai' => $terceiro_macho,
  'id_pai' => $id_macho,
  'terceiro_mae' => $terceiro_mae,
  'id_mae' => $id_femea,
  'qtd'   => $_POST['embrioes'],
  'congelados'  => $_POST['congelados'],
  'usados'  => $_POST['usados'],
  'data_coleta' => $data_coleta,
  'raca'    => $_POST['raca'],
  'tipo_semen'    => $_POST['tipo_semen']
);


DBUpdate('transplante', $dados, "id = '$id_lote'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=te&id_lote=$id_lote'>";
}}}
?>
