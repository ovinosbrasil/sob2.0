<?php
require_once __DIR__ . "/../../_config.php";
header("Content-Type: text/html; charset=UTF-8");

$data = $_POST['data'];
include "../../funcoes_data/data.php";
$macho = DBEscape($_POST['macho'] ?? '');
$femea = DBEscape($_POST['femea'] ?? '');

// TESTE MACHO
$verifica_macho = DBRead('animais', "WHERE nome = '$macho' AND sexo = 'Macho'");
$verifica_macho_terceiro = DBRead('terceiros', "WHERE nome = '$macho' AND sexo = 'Macho'");

//TESTE MACHO
if((($verifica_macho[0]['id'] ?? 0) <= 0) && (($verifica_macho_terceiro[0]['id'] ?? 0) <= 0)){
  echo "<script type=\"text/javascript\"> alert(\"Macho não existe. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$verifica_femea = DBRead('animais', "WHERE nome = '$femea' AND sexo = 'Fêmea'");
$verifica_femea_terceiro = DBRead('terceiros', "WHERE nome = '$femea' AND sexo = 'Fêmea'");

//TESTE MACHO
if((($verifica_femea[0]['id'] ?? 0) <= 0) && (($verifica_femea_terceiro[0]['id'] ?? 0) <= 0)){
  echo "<script type=\"text/javascript\"> alert(\"Fêmea não existe. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

if(($verifica_macho[0]['id'] ?? 0) > 0){ $id_macho = $verifica_macho[0]['id']; $terceiro_macho = 0; }
if(($verifica_macho_terceiro[0]['id'] ?? 0) > 0){ $id_macho = $verifica_macho_terceiro[0]['id']; $terceiro_macho = 1; }
if(($verifica_femea[0]['id'] ?? 0) > 0){ $id_femea = $verifica_femea[0]['id']; $terceiro_mae = 0; }
if(($verifica_femea_terceiro[0]['id'] ?? 0) > 0){ $id_femea = $verifica_femea_terceiro[0]['id']; $terceiro_mae = 1; }

$dados = array(
	'data'	=> $data,
	'pai'	=> $id_macho,
  'mae'	=> $id_femea,
  'terceiro'  => $terceiro_macho,
  'terceiro_mae'  => $terceiro_mae,
  'qtd'  => $_POST['qtd'],
  'botijao' => $_POST['botijao'],
  'palheta' => $_POST['palheta'],
  'qualidade' => $_POST['qualidade']
);

DBcreate('embriao', $dados);

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=embrioes'>";
}}
?>
