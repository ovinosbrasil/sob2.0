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
  $morte = DBRead('animais', "WHERE mae = '$id_femea' AND terceiro_mae = '0' AND status = '1'");
  $qtd_mortes=0;

  foreach ($morte as $morte_) {
  $data = $morte_['data_de_nascimento'];
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
  $data_nascimento = $data;


  $data = $morte_['data_de_saida'];
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
  $data_morte = $data;

  $time_inicial = geraTimestamp($data_nascimento);
  $time_final = geraTimestamp($data_morte);
  $diferenca = $time_final - $time_inicial;
  $dias = (int)floor( $diferenca / (60 * 60 * 24));
  if($dias <= 90){
  $qtd_mortes++;
  }
  }
  $mortes = ($qtd_mortes*100)/$matriz_['qtd_crias'];
  $dados = array(
    'qtd_mortes' => $mortes
  );
  DBUpdate('matriz', $dados, "id_femea = '$id_femea'");
}
?>
