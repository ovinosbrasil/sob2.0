<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}


include "../../_config.php";
$id_animal = $_GET['id_animal'];

$data = $_POST['data_adulto'];
include "../../funcoes_data/data.php";

$dados = array(
	'peso_inicial'	=> $_POST['peso_inicial'],
	'peso3'	=> $_POST['peso_adulto'],
	'data3'		=> $data

);
DBUpdate('animais', $dados, "id = '$id_animal'");


$animal = DBRead('animais', "WHERE id = '$id_animal'");
$id_pai = $animal[0]['pai'];
//RANKIN GMD
$reprodutor = DBRead('reprodutor', "WHERE id_macho = '$id_pai'");
  $cria = DBRead('animais', "WHERE pai = '$id_pai' AND peso3 > '0' AND data3 > '2011-01-01' AND peso2 > '0' AND data2 > '2011-01-01'");
  $gmd=$qtd=0;
  foreach ($cria as $cria_){
  $data = $cria_['data2'];
  $data_atual = $data;
  $data = '0';
  $data['0'] = $data_atual['8'];
  $data['1'] = $data_atual['9'];
  $data['2'] = "/";
  $data['3'] = $data_atual['5'];
  $data['4'] = $data_atual['6'];
  $data['5'] = "/";
  $data['6'] = $data_atual['0'];
  $data['7'] = $data_atual['1'];
  $data['8'] = $data_atual['2'];
  $data['9'] = $data_atual['3'];
  $data_apartacao = $data;

  //GMD
  $data = $cria_['data3'];
  $data_atual = $data;
  $data = '0';
  $data['0'] = $data_atual['8'];
  $data['1'] = $data_atual['9'];
  $data['2'] = "/";
  $data['3'] = $data_atual['5'];
  $data['4'] = $data_atual['6'];
  $data['5'] = "/";
  $data['6'] = $data_atual['0'];
  $data['7'] = $data_atual['1'];
  $data['8'] = $data_atual['2'];
  $data['9'] = $data_atual['3'];
  $data_adulto = $data;

  $time_inicial = geraTimestamp($data_apartacao);
  $time_final = geraTimestamp($data_adulto);
  $diferenca = $time_final - $time_inicial;
  $dias = (int)floor( $diferenca / (60 * 60 * 24));

  if($dias < 300){
    $gmd = $gmd + (($cria_['peso3']-$cria_['peso2'])/$dias);
    $qtd++;
  }
  }
  $gmd = $gmd/$qtd;
  $dados = array(
    'gmd' => $gmd
  );
  DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=avaliacao&avaliacao=2'>";

?>
