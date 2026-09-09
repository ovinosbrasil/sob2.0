<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}


set_time_limit(9999999999999);
include "../_config.php";

$matriz = DBRead('matriz');
foreach ($matriz as $matriz_){
  $id_femea = $matriz_['id_femea'];
  $cria = DBRead('animais', "WHERE mae = '$id_femea' AND peso2 > '0' AND terceiro_mae = '0' AND tipo_reproducao != 'Embrionagem'");
  $gmd=$qtd=$qtd_partos=0;
  foreach ($cria as $cria_) {
  $data_nova = $cria_['data_de_nascimento'];

  $data = $cria_['data_de_nascimento'];
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
  $data_adulto = $data;

  $time_inicial = geraTimestamp($data_apartacao);
  $time_final = geraTimestamp($data_adulto);
  $diferenca = $time_final - $time_inicial;
  $dias = (int)floor( $diferenca / (60 * 60 * 24));

  if(($qtd_partos > 0) && ($dias < 150)){
    if($data_anterior == $data_nova){
      $gmd = $gmd + ($cria_['peso2']/$dias);
    }else{
      $gmd = $gmd + ($cria_['peso2']/$dias);
      $qtd_partos++;
    }
  }

  if(($dias < 150) && ($qtd_partos == 0)){
    $gmd = $gmd + ($cria_['peso2']/$dias);
    $qtd_partos++;
  }
  $data_anterior = $cria_['data_de_nascimento'];
  }

  $gmd = ($gmd*90)/$qtd_partos;
  $dados = array(
    'peso_apartacao' => $gmd
  );
  DBUpdate('matriz', $dados, "id_femea = '$id_femea'");
}
?>
