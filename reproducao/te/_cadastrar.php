<?php
require __DIR__ . "/../../_config.php";
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/_alertas_te.php';
validarCamposLoteTe(false);
require __DIR__ . "/_validar_macho_complementar.php";

$data = $_POST['data_inicial'];
include "../../funcoes_data/data.php";
$data_inicial = $data;


$lote = DBEscape($_POST['lote']);
$lote_ = DBRead('transplante', "WHERE codigo = '$lote'");

// TESTE LOTE
if(!empty($lote_[0]['id'])){
  voltarFluxoTe('Lote já existe.Tente novamente');
}else{

$macho = $_POST['macho'];
$machoEscapado = DBEscape($macho);
$verifica_macho = DBRead('animais', "WHERE nome = '$machoEscapado' AND sexo = 'Macho'");
$verifica_macho_terceiro = DBRead('terceiros', "WHERE ativo = 1 AND nome = '$machoEscapado' AND sexo = 'Macho'");

//TESTE MACHO
if(empty($verifica_macho[0]['id']) && empty($verifica_macho_terceiro[0]['id'])){
  voltarFluxoTe('Macho não existe. Tente novamente');
}else{

$femea = $_POST['femea'];
$femeaEscapado = DBEscape($femea);
$verifica_femea = DBRead('animais', "WHERE nome = '$femeaEscapado' AND sexo = 'Fêmea'");
$verifica_femea_terceiro = DBRead('terceiros', "WHERE ativo = 1 AND nome = '$femeaEscapado' AND sexo = 'Fêmea'");

//TESTE MACHO
if(empty($verifica_femea[0]['id']) && empty($verifica_femea_terceiro[0]['id'])){
  voltarFluxoTe('Fêmea não existe. Tente novamente');
}else{

if(!empty($verifica_macho[0]['id'])){ $id_macho = $verifica_macho[0]['id']; $terceiro_macho = 0; }
if(!empty($verifica_macho_terceiro[0]['id'])){ $id_macho = $verifica_macho_terceiro[0]['id']; $terceiro_macho = 1; }
if(!empty($verifica_femea[0]['id'])){ $id_femea = $verifica_femea[0]['id']; $terceiro_mae = 0; }
if(!empty($verifica_femea_terceiro[0]['id'])){ $id_femea = $verifica_femea_terceiro[0]['id']; $terceiro_mae = 1; }

$dados = array(
	'codigo'	=> $_POST['lote'],
	'data'	=> $data_inicial,
  'pai' => $macho,
  'mae' => $femea,
  'terceiro_pai' => $terceiro_macho,
  'id_pai' => $id_macho,
  'id_pai_2' => $id_pai_2,
  'terceiro_pai_2' => $terceiro_pai_2,
  'terceiro_mae' => $terceiro_mae,
  'id_mae' => $id_femea,
  'qtd'   => $_POST['embrioes'],
  // Campo legado obrigatório; a quantidade por receptora fica em transplante_controle.n_embrioes.
  'n_embrioes' => '',
  'usados' => 0,
  'congelados' => 0,
  'data_coleta' => null,
  'raca' => '',
  'tipo_semen' => ''
);

$id_lote = DBcreate('transplante', $dados, true);


//RANKING TE
$dados = array(
	'id_lote'	=> $id_lote,
  'tipo'    => 2,
  'ultrassom' => 0,
  'femeas' => 0,
  'crias' => 0,
  'mortes' => 0,
  'vivos' => 0
);
DBcreate('lotes_reproducao', $dados);
//RANKING TE FIM


$_SESSION['alerta_te'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Lote de transplante cadastrado com sucesso.');
unset($_SESSION['campos_te']);
header('Location: ../../geral.php?pg=te&id_lote=' . (int)$id_lote);
}}}
?>
