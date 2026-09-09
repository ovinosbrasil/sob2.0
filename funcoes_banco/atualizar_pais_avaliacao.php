<?



$avaliacao = DBRead('avaliacao');
foreach ($avaliacao as $avaliacao_){
  $id_avaliacao = $avaliacao_['id'];
  $id_animal = $avaliacao_['id_animal'];
  $animal = DBRead('animais', "WHERE id = '$id_animal'");
  if($animal[0]['terceiro_pai'] == 0){ $id_pai = $animal[0]['pai']; }
  if($animal[0]['terceiro_mae'] == 0){ $id_mae = $animal[0]['mae']; }
  $dados = array(
    'pai' => $id_pai,
    'mae' => $id_mae
  );
    DBUpdate('avaliacao', $dados, "id = '$id_avaliacao'");
  }


  $reprodutor = DBRead('reprodutor');
  foreach ($reprodutor as $reprodutor_){
    $id_animal = $reprodutor_['id_macho'];
    $avaliacao = DBRead('avaliacao', "WHERE pai = '$id_animal'");
    $pesagem=$cabeca=$pescoco=$quarto_anterior=$barril=$quarto_posterior=$comprimento=$orgao=$distribuicao=$cobertura=$cor=$conformacao=$qtd=0;
    foreach ($avaliacao as $avaliacao_) {
      $id_cria = $avaliacao_['id_animal'];
      $cria = DBRead('animais', "WHERE id = '$id_cria'");
      if(!$cria[0]['terceiro_pai']){
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
    $dados = array(
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
    DBUpdate('reprodutor', $dados, "id_macho = '$id_animal'");
  }


  $matriz = DBRead('matriz');
  foreach ($matriz as $matriz_){
    $id_animal = $matriz_['id_femea'];
    $avaliacao = DBRead('avaliacao', "WHERE mae = '$id_animal'");
    $pesagem=$cabeca=$pescoco=$quarto_anterior=$barril=$quarto_posterior=$comprimento=$orgao=$distribuicao=$cobertura=$cor=$conformacao=$qtd=0;
    foreach ($avaliacao as $avaliacao_) {
      $id_cria = $avaliacao_['id_animal'];
      $cria = DBRead('animais', "WHERE id = '$id_cria'");
      if(!$cria[0]['terceiro_mae']){
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
    $dados = array(
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
    DBUpdate('matriz', $dados, "id_femea = '$id_animal'");
  }
?>
