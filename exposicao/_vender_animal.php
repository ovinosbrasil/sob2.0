<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
list($id_animal, $id_evento) = validarAnimalExposicao(true);

$dataVenda = is_string($_POST['data'] ?? null) ? DateTimeImmutable::createFromFormat('!d/m/Y', $_POST['data']) : false;
if (empty($_POST['comprador']) || empty($_POST['data']) || !isset($_POST['valor']) || trim((string)$_POST['valor']) === '') {
    avisarExposicao('warning', 'Preencha os campos obrigatórios da venda.', $id_evento, 'vendas');
}
if (!$dataVenda || $dataVenda->format('d/m/Y') !== $_POST['data']) {
    avisarExposicao('warning', 'Informe uma data válida para a venda.', $id_evento, 'vendas');
}
$comprador = DBEscape($_POST['comprador']);
$comprador = DBRead('mercado', "WHERE nome = '$comprador'");
if (!$comprador) avisarExposicao('warning', 'Selecione um comprador cadastrado.', $id_evento, 'vendas');
$id_comprador = $comprador[0]['id'];
$data = $dataVenda->format('Y-m-d');
$parcelas = filter_var($_POST['parcelas'] ?? 1, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 24)));
$tipo = $_POST['tipo_venda'];
$forma = $_POST['forma'];
$valor = $_POST['valor'];
$valor = str_replace("," , "" , $valor);
$valor_venda = (float)$valor;
if (!$parcelas || !preg_match('/^\d+(?:\.\d{1,2})?$/D', $valor) || !is_finite($valor_venda) || $valor_venda <= 0 || round($valor_venda * 100) < $parcelas) {
    avisarExposicao('warning', 'Informe um valor total válido e de 1 a 24 parcelas, com pelo menos R$ 0,01 por parcela.', $id_evento, 'vendas');
}
$observacoes = str_replace("'", '"',$_POST['observacoes']);

$dados = array(
	'id_animal'	=> $id_animal,
	'data'	=> $data,
	'forma_de_pagamento'	=> $forma,
	'parcelas'	=> $parcelas,
	'tipo_venda'		=> $tipo,
	'comprador'	=> $id_comprador,
	'preco_de_venda'			=> $valor,
	'observacoes'			=> $observacoes
);

$teste = DBRead('vendas', "WHERE id_animal = '$id_animal'");
if(!empty($teste[0]['id'])){
    gravarExposicao('DBUpdate', 'vendas', $dados, "id_animal = '$id_animal'");
		gravarExposicao('DBDelete', 'controle_financeiro', "id_animal = '$id_animal'");
}else{
    gravarExposicao('DBCreate', 'vendas', $dados);
}
$dados = array(
  'status' => 2
);
gravarExposicao('DBUpdate', 'animais', $dados, "id = '$id_animal'");


//RANKING REPRODUTOR
$animal = DBRead('animais', "WHERE id = '$id_animal'");
$id_macho = $animal[0]['pai'];
if($animal[0]['terceiro_pai'] == 0){
$vendas = DBRead('animais', "WHERE status = '2' AND pai = '$id_macho' AND terceiro_pai = '0'");
$qtd_macho=$qtd_femea=$total_macho=$total_femea=$valor_total=0;
foreach ($vendas as $vendas_) {
  $id_cria = $vendas_['id'];
  $valor = DBRead('vendas', "WHERE id_animal = '$id_cria' AND data > '2011-01-01'");
	if($valor[0]['id'] > 0){
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
gravarExposicao('DBUpdate', 'reprodutor', $dados, "id_macho = '$id_macho'");
}
//RANKING FIM REPRODUTOR

//RANKING MATRIZ
$animal = DBRead('animais', "WHERE id = '$id_animal'");
$id_femea = $animal[0]['mae'];
if($animal[0]['terceiro_mae'] == 0){
$vendas = DBRead('animais', "WHERE status = '2' AND mae = '$id_femea' AND terceiro_mae = '0'");
$qtd_macho=$qtd_femea=$total_macho=$total_femea=$valor_total=0;
foreach ($vendas as $vendas_) {
  $id_cria = $vendas_['id'];
  $valor = DBRead('vendas', "WHERE id_animal = '$id_cria' AND data > '2011-01-01'");
	if($valor[0]['id'] > 0){
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
gravarExposicao('DBUpdate', 'matriz', $dados, "id_femea = '$id_femea'");
}
//RANKING FIM MATRIZ


//FINANCEIRO
$totalCentavosVenda = (int)round((float)$valor_venda * 100);
$valorBaseParcela = intdiv($totalCentavosVenda, $parcelas);
$restoCentavosVenda = $totalCentavosVenda % $parcelas;
$descricao = "Venda animal - ".$animal[0]['nome'];
$dataPrimeiraParcela = new DateTime($data);
for ($indiceParcela = 0; $indiceParcela < $parcelas; $indiceParcela++) {
    $mesParcela = (clone $dataPrimeiraParcela)->modify('first day of this month')->modify('+' . $indiceParcela . ' months');
    $diaParcela = min((int)$dataPrimeiraParcela->format('d'), (int)$mesParcela->format('t'));
    $data = $mesParcela->format('Y-m-') . sprintf('%02d', $diaParcela);
    $centavosParcela = $valorBaseParcela + ($indiceParcela < $restoCentavosVenda ? 1 : 0);
    $valor = intdiv($centavosParcela, 100) . '.' . sprintf('%02d', $centavosParcela % 100);
	$dados = array(
	'titulo'			=> $descricao . ' ' . ($indiceParcela + 1) . '/' . $parcelas,
	'data'				=> $data,
	'valor'		=> $valor,
	'id_animal'		=> $id_animal,
  'forma_de_pagamento' => $forma,
  'categoria' => '',
  'id_tipo' => 0,
  'obs' => $observacoes,
  'status' => 0,
  'tipo' => 0,
  'id_comprador' => $id_comprador,
  'id_embriao' => 0,
  'id_semen' => 0
);

gravarExposicao('DBCreate', 'controle_financeiro', $dados);

}
//FIM FINANCEIRO
avisarExposicao('success', 'Venda cadastrada com sucesso.', $id_evento, 'vendas');
?>
