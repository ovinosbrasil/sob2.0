<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
$campos = array();
foreach ($_POST as $campo => $valorCampo) {
    if (is_string($valorCampo)) $campos[$campo] = trim($valorCampo);
}
$_SESSION['campos_venda'] = $campos;
if (empty($campos['comprador']) || empty($campos['data']) || empty($campos['tipo_venda'])) {
    avisarVenda('warning', 'Preencha os campos obrigatórios: Comprador, Data da venda e Tipo de venda.');
}
$dataVenda = DateTimeImmutable::createFromFormat('!d/m/Y', $campos['data']);
if (!$dataVenda || $dataVenda->format('d/m/Y') !== $campos['data']) avisarVenda('warning', 'Informe uma data válida no formato dd/mm/aaaa.');
if (!in_array($campos['tipo_venda'], array('Fazenda', 'Leilão', 'Exposição', 'Virtual'), true)) avisarVenda('warning', 'Selecione um tipo de venda válido.');
$parcelas = filter_var($campos['parcelas'] ?? 1, FILTER_VALIDATE_INT);
if (!$parcelas || $parcelas < 1 || $parcelas > 24) avisarVenda('warning', 'Informe de 1 a 24 parcelas.');
$nomeComprador = DBEscape($campos['comprador']);
$comprador = DBRead('mercado', "WHERE nome = '$nomeComprador'");
if (!$comprador) avisarVenda('warning', 'Selecione um comprador cadastrado.');
$id_comprador = (int)$comprador[0]['id'];
$animaisVenda = array(); $idsVenda = array(); $totalVenda = 0;
// Valida todos os animais antes de iniciar qualquer gravação ou lançamento financeiro.
for ($x = 1; $x <= 6; $x++) {
    $nome = $campos['animal_' . $x] ?? '';
    $preco = $campos['preco_conjunto_' . $x] ?? '';
    if ($nome === '' && $preco === '') continue;
    if ($nome === '' || $preco === '') avisarVenda('warning', 'Preencha o animal e o valor em todas as linhas da venda.');
    $valorLinha = str_replace(',', '', $preco);
    if (!is_numeric($valorLinha) || !is_finite((float)$valorLinha) || (float)$valorLinha <= 0) avisarVenda('warning', 'Informe um valor maior que zero para o animal ' . $nome . '.');
    $idSelecionado = filter_var($campos['animal_' . $x . '_id'] ?? 0, FILTER_VALIDATE_INT);
    $origem = $campos['animal_' . $x . '_origem'] ?? '';
    if (!$idSelecionado || $idSelecionado < 1 || $origem !== 'rebanho') avisarVenda('warning', 'Selecione o animal ' . $nome . ' na lista de pesquisa.');
    $registros = DBRead('animais', "WHERE id = '$idSelecionado'");
    if (!$registros) avisarVenda('warning', 'Animal não encontrado: ' . $nome . '.');
    $animalVenda = $registros[0];
    if ((int)$animalVenda['status'] > 0) avisarVenda('warning', 'O animal ' . $nome . ' já saiu do rebanho.');
    $idAnimal = (int)$animalVenda['id'];
    if (isset($idsVenda[$idAnimal])) avisarVenda('warning', 'O animal ' . $nome . ' foi informado mais de uma vez.');
    $idsVenda[$idAnimal] = true;
    $animaisVenda[] = array('id' => $idAnimal, 'valor' => (float)$valorLinha);
    $totalVenda += (float)$valorLinha;
}
if (!$animaisVenda) avisarVenda('warning', 'Adicione pelo menos um animal à venda.');
$totalCentavosPacote = (int)round($totalVenda * 100);
if ($totalCentavosPacote < $parcelas) avisarVenda('warning', 'O total deve permitir pelo menos R$ 0,01 por parcela.');
$tipo = $campos['tipo_venda'];
$forma = $campos['forma'] ?? '';
$observacoes = str_replace("'", '"', $campos['observacoes'] ?? '');
foreach ($animaisVenda as $itemVenda) {
    $id_animal = $itemVenda['id'];
    $valor = $itemVenda['valor'];
    $data = $dataVenda->format('Y-m-d');
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
    gravarVenda('DBUpdate', 'vendas', $dados, "id_animal = '$id_animal'");
		gravarVenda('DBDelete', 'controle_financeiro', "id_animal = '$id_animal'");
}else{
    gravarVenda('DBCreate', 'vendas', $dados);
}
$dados = array(
  'status' => 2
);
gravarVenda('DBUpdate', 'animais', $dados, "id = '$id_animal'");


//RANKING REPRODUTOR
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
  $media_macho = $media_femea = $media_total = $qtd_total = 0;
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
//RANKING FIM REPRODUTOR

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
  $media_macho = $media_femea = $media_total = $qtd_total = 0;
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

}
// O total é calculado no servidor a partir dos animais validados.
$valorBasePacote = intdiv($totalCentavosPacote, $parcelas);
$restoPacote = $totalCentavosPacote % $parcelas;
$descricaoPacote = count($animaisVenda) > 1 ? 'Pacote de vendas' : 'Venda animal - ' . ($campos['animal_1'] ?? '');
for ($parcela = 0; $parcela < $parcelas; $parcela++) {
    $centavosPacote = $valorBasePacote + ($parcela < $restoPacote ? 1 : 0);
    $valor = intdiv($centavosPacote, 100) . '.' . sprintf('%02d', $centavosPacote % 100);
    $mesParcela = $dataVenda->modify('first day of this month')->modify('+' . $parcela . ' months');
    $diaParcela = min((int)$dataVenda->format('d'), (int)$mesParcela->format('t'));
    $dataParcela = $mesParcela->setDate((int)$mesParcela->format('Y'), (int)$mesParcela->format('m'), $diaParcela);
    gravarVenda('DBCreate', 'controle_financeiro', array(
        'titulo' => $descricaoPacote . ' ' . ($parcela + 1) . '/' . $parcelas, 'data' => $dataParcela->format('Y-m-d'),
        'valor' => $valor, 'forma_de_pagamento' => $forma, 'id_comprador' => $id_comprador,
        'categoria' => '', 'id_tipo' => 0, 'obs' => $observacoes, 'status' => 0, 'tipo' => 0,
        'id_animal' => 0, 'id_embriao' => 0, 'id_semen' => 0
    ));
}
unset($_SESSION['campos_venda']);
$quantidade = count($animaisVenda);
avisarVenda('success', $quantidade === 1 ? 'Venda cadastrada com sucesso.' : 'Venda de ' . $quantidade . ' animais cadastrada com sucesso.');
