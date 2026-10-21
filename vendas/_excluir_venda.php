<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/../includes/csrf.php';
exigirPostCsrfSob((($_POST['formato'] ?? '') === 'json'));
require_once __DIR__ . '/_alertas.php';
$id_animal = filter_var($_POST['id_animal'] ?? 0, FILTER_VALIDATE_INT);
$paginaRetorno = !empty($_POST['tipo']) ? 'relatorio_venda' : 'comprador';
$id_comprador = (int)($_POST['id_comprador'] ?? 0);
if (!$id_animal || $id_animal < 1 || !DBRead('vendas', "WHERE id_animal = '$id_animal'") || !DBRead('animais', "WHERE id = '$id_animal'")) {
    avisarVenda('warning', 'Venda não encontrada. Atualize a página e tente novamente.', $paginaRetorno, $id_comprador);
}
$dados = array(
  'status' => 0
);
gravarVenda('DBUpdate', 'animais', $dados, "id = '$id_animal'");

// RANKING REPRODUTOR
$animal = DBRead('animais', "WHERE id = '$id_animal'");
$id_macho = $animal[0]['pai'];
if($animal[0]['terceiro_pai'] == 0){
$vendas = DBRead('animais', "WHERE status = '2' AND pai = '$id_macho' AND terceiro_pai = '0'");
$qtd_macho=$qtd_femea=$total_macho=$total_femea=$valor_total=0;
foreach (($vendas ?: array()) as $vendas_) {
  $id_cria = $vendas_['id'];
  $valor = DBRead('vendas', "WHERE id_animal = '$id_cria' AND data > '2011-01-01'");
  if(!empty($valor[0]['id'])){
  if($vendas_['sexo'] == 'Macho'){ $total_macho = $total_macho+$valor[0]['preco_de_venda']; $qtd_macho++;}
  if($vendas_['sexo'] == 'Fêmea'){ $total_femea = $total_femea+$valor[0]['preco_de_venda']; $qtd_femea++;}
    $valor_total = $valor_total+$valor[0]['preco_de_venda'];
  }}
  $media_macho = $qtd_macho ? $total_macho/$qtd_macho : 0;
  $media_femea = $qtd_femea ? $total_femea/$qtd_femea : 0;
  $media_total = ($qtd_macho+$qtd_femea) ? $valor_total/($qtd_macho+$qtd_femea) : 0;
  $qtd_total = $qtd_macho+$qtd_femea;
  $dados = array(
    'qtd_vendas'	=> $qtd_total,
    'venda_macho' => $media_macho,
    'venda_femea'  => $media_femea,
    'venda_geral' => $media_total
  );
gravarVenda('DBUpdate', 'reprodutor', $dados, "id_macho = '$id_macho'");
}
//FIM RANKING REPRODUTOR

//RANKING MATRIZ
$animal = DBRead('animais', "WHERE id = '$id_animal'");
$id_femea = $animal[0]['mae'];
if($animal[0]['terceiro_mae'] == 0){
$vendas = DBRead('animais', "WHERE status = '2' AND mae = '$id_femea' AND terceiro_mae = '0'");
$qtd_macho=$qtd_femea=$total_macho=$total_femea=$valor_total=0;
foreach (($vendas ?: array()) as $vendas_) {
  $id_cria = $vendas_['id'];
  $valor = DBRead('vendas', "WHERE id_animal = '$id_cria' AND data > '2011-01-01'");
	if(!empty($valor[0]['id'])){
  if($vendas_['sexo'] == 'Macho'){ $total_macho = $total_macho+$valor[0]['preco_de_venda']; $qtd_macho++;}
  if($vendas_['sexo'] == 'Fêmea'){ $total_femea = $total_femea+$valor[0]['preco_de_venda']; $qtd_femea++;}
    $valor_total = $valor_total+$valor[0]['preco_de_venda'];
  }}
  $media_macho = $qtd_macho ? $total_macho/$qtd_macho : 0;
  $media_femea = $qtd_femea ? $total_femea/$qtd_femea : 0;
  $media_total = ($qtd_macho+$qtd_femea) ? $valor_total/($qtd_macho+$qtd_femea) : 0;
  $qtd_total = $qtd_macho+$qtd_femea;
  $dados = array(
    'qtd_vendas'	=> $qtd_total,
    'venda_macho' => $media_macho,
    'venda_femea'  => $media_femea,
    'venda_geral' => $media_total
  );
gravarVenda('DBUpdate', 'matriz', $dados, "id_femea = '$id_femea'");
}
//RANKING FIM MATRIZ


gravarVenda('DBDelete', 'vendas', "id_animal = '$id_animal'");
gravarVenda('DBDelete', 'controle_financeiro', "id_animal = '$id_animal'");
avisarVenda('success', 'Venda excluída com sucesso.', $paginaRetorno, $id_comprador);
