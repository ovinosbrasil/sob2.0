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
  $cria = DBRead('animais', "WHERE mae = '$id_femea' AND tipo_reproducao != 'Embrionagem' AND terceiro_mae = '0' AND data_de_nascimento > '2011-01-01' ORDER BY data_de_nascimento asc");
  $total=$qtd=$dias_total=$qtd_=$qtd_partos=$qtd_crias_prolificidade=$qtd_partos_prolificidade=0;
  $qtd_crias = count($cria);
  foreach ($cria as $cria_){
  $data_nova = $cria_['data_de_nascimento'];

    if($qtd != 0){
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
        $data_nova = $data;

        //ANTERIOR
        $data = $data_anterior;
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
        $data_anterior = $data;

        if($data_anterior != $data_nova){
          $time_inicial = geraTimestamp($data_anterior);
          $time_final = geraTimestamp($data_nova);
          $diferenca = $time_final - $time_inicial;
          $dias = (int)floor( $diferenca / (60 * 60 * 24));
          $dias_total = $dias_total+$dias;
          $qtd_++; $qtd_partos++;
        }
    }

    //TESTE 7 ANOS
    $mae = DBRead('animais', "WHERE id = '$id_femea'");
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
    $data_nova = $data;

    $data = $mae[0]['data_de_nascimento'];
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
    $data_mae = $data;

    $time_inicial = geraTimestamp($data_mae);
    $time_final = geraTimestamp($data_nova);
    $diferenca = $time_final - $time_inicial;
    $dias = (int)floor( $diferenca / (60 * 60 * 24));
    if($dias < 2555){
        if($data_anterior != $data_nova){  $qtd_partos_prolificidade++; }
        $qtd_crias_prolificidade++;
    }
    //FIM TESTE 7 ANOS
    $data_anterior = $cria_['data_de_nascimento'];
    $qtd++;
  }

  $prolificidade = $qtd_crias_prolificidade/$qtd_partos_prolificidade;
  $media = $dias_total/$qtd_;
  echo $prolificidade," - ";
  $dados = array(
    'intervalo' => $media,
    'qtd_partos'  => $qtd_partos+1,
    'prolificidade' => $prolificidade
  );
  DBUpdate('matriz', $dados, "id_femea = '$id_femea'");
}
?>
