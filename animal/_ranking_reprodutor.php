<?php
$qtd_crias = 0;
//RANKING TIPOS
$crias = DBRead('animais', "WHERE pai = '$id_pai' AND terceiro_pai = '0'");
  if($crias){
  $tipo2=$tipo3=$tipo4=$tipo5=$qtd_avaliadas=0;
  $qtd_crias = count($crias);
  foreach (($crias ?: []) as $crias_){
    if($crias_['tipo'] > 0){
      $qtd_avaliadas++;
      if($crias_['tipo'] == 2){ $tipo2++;}
      if($crias_['tipo'] == 3){ $tipo3++;}
      if($crias_['tipo'] == 4){ $tipo4++;}
      if($crias_['tipo'] == 5){ $tipo5++;}
    }
  }

  if ($qtd_avaliadas) {
    $tipo = (($tipo5*10) + ($tipo4*7) + ($tipo3*4));
    $nota = $tipo ? (($tipo5*10) + ($tipo4*7) + ($tipo3*4))/$qtd_avaliadas : 0;
    $dados = array(
      'id_macho'	=> $id_pai,
      'qtd_crias' => $qtd_crias,
      'qtd_avaliadas'  => $qtd_avaliadas,
      'tipo2' => $tipo2,
      'tipo3' => $tipo3,
      'tipo4' => $tipo4,
      'tipo5' => $tipo5,
      'nota' => $nota
    );

    $teste = DBRead('reprodutor', "WHERE id_macho = '$id_pai'");
    if($teste){
      DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
    }else{
      DBCreate('reprodutor', array_merge(['qtd_vendas' => 0, 'qtd_mortes' => 0, 'venda_macho' => 0, 'venda_femea' => 0, 'venda_geral' => 0, 'pesagem' => 0, 'cabeca' => 0, 'pescoco' => 0, 'quarto_anterior' => 0, 'barril' => 0, 'quarto_posterior' => 0, 'comprimento' => 0, 'orgao' => 0, 'gordura' => 0, 'cobertura' => 0, 'cor' => 0, 'conformacao' => 0, 'gmd' => 0], $dados));
    }
  }
}

//FIM RANKING TIPOS
//RANKING AVALIACAO
$avaliacao = DBRead('avaliacao', "WHERE pai = '$id_pai'");
$pesagem=$cabeca=$pescoco=$quarto_anterior=$barril=$quarto_posterior=$comprimento=$orgao=$distribuicao=$cobertura=$cor=$conformacao=$qtd=0;
foreach (($avaliacao ?: []) as $avaliacao_) {

  $id_cria = $avaliacao_['id_animal'];
  $cria = DBRead('animais', "WHERE id = '$id_cria'");
  if($cria && !$cria[0]['terceiro_pai']){
  if($avaliacao_['avaliacao'] == 1){
    $id_animal_ = $avaliacao_['id_animal'];
    $teste = DBRead('avaliacao', "WHERE id_animal = '$id_animal_' AND avaliacao = '2'");
    if(!$teste){
      $pesagem = $pesagem+$avaliacao_['tamanho'];
      $cabeca = $cabeca+$avaliacao_['cabeca'];
      $pescoco = $pescoco+$avaliacao_['pescoco'];
      $quarto_anterior = $quarto_anterior+$avaliacao_['quarto_anterior'];
      $barril = $barril+$avaliacao_['barril'];
      $quarto_posterior = $quarto_posterior+$avaliacao_['quarto_posterior'];
      $comprimento = $comprimento+$avaliacao_['comprimento'];
      $orgao = $orgao+$avaliacao_['orgao'];
      $distribuicao = $distribuicao+$avaliacao_['distribuicao'];
      $cobertura = $cobertura+$avaliacao_['cobertura'];
      $cor = $cor+$avaliacao_['cor'];
      $conformacao = $conformacao+$avaliacao_['conformacao'];
      $qtd++;
    }}
    if($avaliacao_['avaliacao'] == 2){
      $pesagem = $pesagem+$avaliacao_['tamanho'];
      $cabeca = $cabeca+$avaliacao_['cabeca'];
      $pescoco = $pescoco+$avaliacao_['pescoco'];
      $quarto_anterior = $quarto_anterior+$avaliacao_['quarto_anterior'];
      $barril = $barril+$avaliacao_['barril'];
      $quarto_posterior = $quarto_posterior+$avaliacao_['quarto_posterior'];
      $comprimento = $comprimento+$avaliacao_['comprimento'];
      $orgao = $orgao+$avaliacao_['orgao'];
      $distribuicao = $distribuicao+$avaliacao_['distribuicao'];
      $cobertura = $cobertura+$avaliacao_['cobertura'];
      $cor = $cor+$avaliacao_['cor'];
      $conformacao = $conformacao+$avaliacao_['conformacao'];
      $qtd++;
    }
  }
}
if ($qtd > 0) {
  $dados2 = array(
  'pesagem' => $pesagem/$qtd,
  'cabeca' => $cabeca/$qtd,
  'pescoco' => $pescoco/$qtd,
  'quarto_anterior' => $quarto_anterior/$qtd,
  'barril' => $barril/$qtd,
  'quarto_posterior' => $quarto_posterior/$qtd,
  'comprimento' => $comprimento/$qtd,
  'orgao' => $orgao/$qtd,
  'gordura' => $distribuicao/$qtd,
  'cobertura' => $cobertura/$qtd,
  'cor' => $cor/$qtd,
  'conformacao' => $conformacao/$qtd
  );
  DBUpdate('reprodutor', $dados2, "id_macho = '$id_pai'");
}
//RANKING PESAGEM
$cria = DBRead('animais', "WHERE pai = '$id_pai' AND peso3 > '0' AND data3 > '2011-01-01' AND peso2 > '0' AND data2 > '2011-01-01' AND terceiro_pai = '0'");
$gmd=$qtd=0;
foreach (($cria ?: []) as $cria_) {
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
if($dias > 0 && $dias < 300){
  $gmd = $gmd + (($cria_['peso3']-$cria_['peso2'])/$dias);
  $qtd++;
}
}
$gmd = $qtd ? $gmd/$qtd : 0;
$dados = array(
  'gmd' => $gmd
);
DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
//FIM RANKING PESAGEM
//RANKING VENDAS
$vendas = DBRead('animais', "WHERE status = '2' AND pai = '$id_pai' AND terceiro_pai = '0'");
$qtd_macho=$qtd_femea=$total_macho=$total_femea=$valor_total=0;
  foreach (($vendas ?: []) as $vendas_) {
    $id_cria = $vendas_['id'];
    $valor = DBRead('vendas', "WHERE id_animal = '$id_cria' AND data > '2011-01-01'");
    if($valor){
      if($vendas_['sexo'] == 'Macho'){ $total_macho = $total_macho+$valor[0]['preco_de_venda']; $qtd_macho++;}
      if($vendas_['sexo'] == 'Fêmea'){ $total_femea = $total_femea+$valor[0]['preco_de_venda']; $qtd_femea++;}
      $valor_total = $valor_total+$valor[0]['preco_de_venda'];
    }
  }

  if ($vendas) {
  
    $media_macho = $qtd_macho ? $total_macho/$qtd_macho : 0;
    $media_femea = $qtd_femea ? $total_femea/$qtd_femea : 0;
    $media_total = $qtd_macho+$qtd_femea ? $valor_total/($qtd_macho+$qtd_femea) : 0;
    $qtd_total = $qtd_macho+$qtd_femea;
    $dados = array(
      'qtd_vendas'	=> $qtd_total,
      'venda_macho' => $media_macho,
      'venda_femea'  => $media_femea,
      'venda_geral' => $media_total
    );
    DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
  }

//RANKING MORTES
$data_final = "2016-01-01";
$morte = DBRead('animais', "WHERE pai = '$id_pai' AND status = '1' AND causa_da_perda = 'Nascimento' AND terceiro_pai = '0' AND data_de_nascimento >= '$data_final'");
$qtd_mortes = $morte ? count($morte) : 0;
if($morte){
  $qtd_mortes = $qtd_crias > 0 ? ($qtd_mortes*100)/$qtd_crias : 0;
}else{ $qtd_mortes=0; }
  $dados = array(
    'qtd_mortes' => $qtd_mortes
  );
DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
//FIM RANKING MORTES
?>
