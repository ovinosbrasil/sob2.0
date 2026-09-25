<?php
require_once __DIR__ . "/../../_config.php";

$id_animal = filter_var($_GET['id_animal'] ?? '', FILTER_VALIDATE_INT);
$avaliacao = filter_var($_GET['avaliacao'] ?? '', FILTER_VALIDATE_INT);
if (!$id_animal || $id_animal < 1 || !in_array($avaliacao, array(1, 2), true)) {
  exit('Animal ou avaliação inválidos.');
}
foreach (array('tamanho', 'cabeca', 'pescoco', 'quarto_anterior', 'barril', 'quarto_posterior', 'comprimento', 'orgao', 'distribuicao', 'cobertura', 'cor') as $campo) {
  $notaInformada = filter_var($_POST[$campo] ?? '', FILTER_VALIDATE_INT);
  if (!in_array($notaInformada, array(2, 3, 4, 5), true)) {
    exit('Preencha todos os critérios da avaliação com notas de 2 a 5.');
  }
  $_POST[$campo] = $notaInformada;
}

$animal = DBRead('animais', "WHERE id = '$id_animal'");
if (!$animal) {
  exit('Animal não encontrado.');
}
// Estes vínculos são usados apenas no ranking de animais do rebanho.
$id_pai = empty($animal[0]['terceiro_pai']) ? (int)($animal[0]['pai'] ?? 0) : 0;
$id_mae = empty($animal[0]['terceiro_mae']) ? (int)($animal[0]['mae'] ?? 0) : 0;


$dados = array(
  'id_animal' => $id_animal,
  'tipo' => 0,
  'conformacao' => 0,
  'sexo' => $animal[0]['sexo'] === 'Macho' ? 0 : 1,
	'tamanho'	=> $_POST['tamanho'],
	'cabeca'	=> $_POST['cabeca'],
	'pescoco'	=> $_POST['pescoco'],
  'quarto_anterior'	=> $_POST['quarto_anterior'],
  'barril'	=> $_POST['barril'],
  'quarto_posterior'	=> $_POST['quarto_posterior'],
  'comprimento'	=> $_POST['comprimento'],
  'orgao'	=> $_POST['orgao'],
  'distribuicao'	=> $_POST['distribuicao'],
  'cobertura'	=> $_POST['cobertura'],
  'cor'	=> $_POST['cor'],
  'avaliacao' => $avaliacao,
  'pai' => $id_pai,
  'mae' => $id_mae
);


$teste = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = '$avaliacao'");
if(($teste[0]['id'] ?? 0) > 0){
  DBUpdate('avaliacao', $dados, "id_animal = '$id_animal' AND avaliacao = '$avaliacao'");
}else{
  DBCreate('avaliacao', $dados);
}

include "_setar_tipo.php";


//RANKING REPRODUTOR
if($id_pai > 0){
  $id_pai = $animal[0]['pai'];
  $crias = DBRead('animais', "WHERE pai = '$id_pai' AND terceiro_pai = '0'");
  if($crias){
  $tipo2=$tipo3=$tipo4=$tipo5=$qtd_avaliadas=0;
  $qtd_crias = count($crias);
  foreach ($crias as $crias_){
    if($crias_['tipo'] > 0){
      $qtd_avaliadas++;
      if($crias_['tipo'] == 2){ $tipo2++;}
      if($crias_['tipo'] == 3){ $tipo3++;}
      if($crias_['tipo'] == 4){ $tipo4++;}
      if($crias_['tipo'] == 5){ $tipo5++;}
    }
  }
  $nota = $qtd_avaliadas > 0 ? (($tipo5*10) + ($tipo4*7) + ($tipo3*4))/$qtd_avaliadas : 0;
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
  DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
}}
//FIM RANKING REPRODUTOR

//RANKING MATRIZ
if($id_mae > 0){
  $id_mae = $animal[0]['mae'];
  $crias = DBRead('animais', "WHERE mae = '$id_mae' AND terceiro_mae = '0'");
  if($crias){
  $tipo2=$tipo3=$tipo4=$tipo5=$qtd_avaliadas=0;
  $qtd_crias = count($crias);
  foreach ($crias as $crias_){
    if($crias_['tipo'] > 0){
      $qtd_avaliadas++;
      if($crias_['tipo'] == 2){ $tipo2++;}
      if($crias_['tipo'] == 3){ $tipo3++;}
      if($crias_['tipo'] == 4){ $tipo4++;}
      if($crias_['tipo'] == 5){ $tipo5++;}
    }
  }
  $nota = $qtd_avaliadas > 0 ? (($tipo5*10) + ($tipo4*7) + ($tipo3*4))/$qtd_avaliadas : 0;
  $dados = array(
    'id_femea'	=> $id_mae,
    'qtd_crias' => $qtd_crias,
    'qtd_avaliadas'  => $qtd_avaliadas,
    'tipo2' => $tipo2,
    'tipo3' => $tipo3,
    'tipo4' => $tipo4,
    'tipo5' => $tipo5,
    'nota' => $nota
  );
  DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
}}
//FIM RANKING MATRIZ

//RANKING TIPIFICACAO REPRODUTOR
if ($id_pai > 0) {
  $avaliacao = DBRead('avaliacao', "WHERE pai = '$id_pai'");
  $pesagem=$cabeca=$pescoco=$quarto_anterior=$barril=$quarto_posterior=$comprimento=$orgao=$distribuicao=$cobertura=$cor=$conformacao=$qtd=0;
  foreach (($avaliacao ?: array()) as $avaliacao_) {
    $id_cria = $avaliacao_['id_animal'];
    $cria = DBRead('animais', "WHERE id = '$id_cria'");
    if($cria && !$cria[0]['terceiro_pai']){
    if($avaliacao_['avaliacao'] == 1){
      $id_animal_ = $avaliacao_['id_animal'];
      $teste = DBRead('avaliacao', "WHERE id_animal = '$id_animal_' AND avaliacao = '2'");
      if(empty($teste[0]['id'])){
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
    }}
  if ($qtd > 0) {
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
  DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
}
}
//FIM RANKING



//RANKING TIPIFICACAO MATRIZ
if ($id_mae > 0) {
  $avaliacao = DBRead('avaliacao', "WHERE mae = '$id_mae'");
  $pesagem=$cabeca=$pescoco=$quarto_anterior=$barril=$quarto_posterior=$comprimento=$orgao=$distribuicao=$cobertura=$cor=$conformacao=$qtd=0;
  foreach (($avaliacao ?: array()) as $avaliacao_) {
    $id_cria = $avaliacao_['id_animal'];
    $cria = DBRead('animais', "WHERE id = '$id_cria'");
    if($cria && !$cria[0]['terceiro_mae']){
    if($avaliacao_['avaliacao'] == 1){
      $id_animal_ = $avaliacao_['id_animal'];
      $teste = DBRead('avaliacao', "WHERE id_animal = '$id_animal_' AND avaliacao = '2'");
      if(empty($teste[0]['id'])){
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
    }}
  if ($qtd > 0) {
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
  DBUpdate('matriz', $dados, "id_femea = '$id_mae'");
}
}
//FIM RANKING

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=avaliacao'>";


?>
