<?

//RANKING AVALIACAO
$crias = DBRead('animais', "WHERE mae = '$id_mae' AND terceiro_mae = '0'");
if($crias[0]['id'] > 0){
  $tipo2=$tipo3=$tipo4=$tipo5=$qtd_avaliadas=0;
  $qtd_crias = count($crias);
  foreach ($crias as $crias_){
    if($crias_['tipo'] > 0){
      $id_cria = $crias_['id'];
      $teste = DBRead('avaliacao', "WHERE id_animal = '$id_cria'");
      if($teste[0]['id'] > 0){
        $qtd_avaliadas++;
        if($crias_['tipo'] == 2){ $tipo2++;}
        if($crias_['tipo'] == 3){ $tipo3++;}
        if($crias_['tipo'] == 4){ $tipo4++;}
        if($crias_['tipo'] == 5){ $tipo5++;}
      }
    }
  }

  if ($qtd_avaliadas) {
    $nota = (($tipo5*10) + ($tipo4*7) + ($tipo3*4))/$qtd_avaliadas;
    $dados = array(
      'id_femea'  => $id_mae,
      'qtd_crias' => $qtd_crias,
      'qtd_avaliadas'  => $qtd_avaliadas,
      'tipo2' => $tipo2,
      'tipo3' => $tipo3,
      'tipo4' => $tipo4,
      'tipo5' => $tipo5,
      'nota'  => $nota
    );
    $teste = DBRead('matriz', "WHERE id_femea = '$id_mae'");
    if($teste[0]['id'] > 0){
      DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
    }else{
      DBCreate('matriz', $dados);
    }
  }
}
//FIM RANKING AVALIACAO


//RANKING INTERVALO
$cria = DBRead('animais', "WHERE mae = '$id_mae' AND tipo_reproducao != 'Embrionagem' AND terceiro_mae = '0' AND data_de_nascimento > '2011-01-01' ORDER BY data_de_nascimento asc");
$total=$qtd=$dias_total=$qtd_=$qtd_partos=$qtd_crias_prolificidade=$qtd_partos_prolificidade=0;
$qtd_crias = $cria ? count($cria) : 0;
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
  $mae = DBRead('animais', "WHERE id = '$id_mae'");
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

if ($cria) {
  $prolificidade = $qtd_crias_prolificidade/$qtd_partos_prolificidade;
  $media = $dias_total/$qtd_;
  $dados = array(
    'intervalo' => $media,
    'qtd_partos'  => $qtd_partos+1,
    'prolificidade' => $prolificidade
  );

  DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
  //FIM RANKING INTERVALO
}


//RANKING MORTE
$morte = DBRead('animais', "WHERE mae = '$id_mae' AND terceiro_mae = '0' AND status = '1'");
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

if ($matriz_['qtd_crias']) {
  $mortes = ($qtd_mortes*100)/$matriz_['qtd_crias'];
  $dados = array(
    'qtd_mortes' => $mortes
  );
  DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
  //FIM RANKING MORTE
}


//RANKING PESAGEM
$cria = DBRead('animais', "WHERE mae = '$id_mae' AND peso2 > '0' AND terceiro_mae = '0' AND tipo_reproducao != 'Embrionagem'");
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

if ($cria) {
  $gmd = ($gmd*90)/$qtd_partos;
  $dados = array(
    'peso_apartacao' => $gmd
  );
  DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
  //FIM RANKING PESAGEM
}


//RANKING VENDAS
$vendas = DBRead('animais', "WHERE status = '2' AND mae = '$id_mae' AND terceiro_mae = '0'  AND data_de_nascimento > '2011-01-01'");
$qtd_macho=$qtd_femea=$total_macho=$total_femea=$valor_total=0;
foreach ($vendas as $vendas_) {
  $id_cria = $vendas_['id'];
  $valor = DBRead('vendas', "WHERE id_animal = '$id_cria' AND data > '2011-01-01'");
  if($valor[0]['id'] > 0){
  if($vendas_['sexo'] == 'Macho'){ $total_macho = $total_macho+$valor[0]['preco_de_venda']; $qtd_macho++;}
  if($vendas_['sexo'] == 'Fêmea'){ $total_femea = $total_femea+$valor[0]['preco_de_venda']; $qtd_femea++;}
  $valor_total = $valor_total+$valor[0]['preco_de_venda'];
}}

if ($vendas) {
  $media_macho = $total_macho/$qtd_macho;
  $media_femea = $total_femea/$qtd_femea;
  $media_total = $valor_total/($qtd_macho+$qtd_femea);
  $qtd_total = $qtd_macho+$qtd_femea;
  $dados = array(
    'qtd_vendas'	=> $qtd_total,
    'venda_macho' => $media_macho,
    'venda_femea'  => $media_femea,
    'venda_geral' => $media_total
  );
  DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
//FIM RANKING VENDAS
}
  
?>
