<?php
require __DIR__ . "/../../_config.php";

$id_animal = $_GET['id_animal'];
$comprador = $_POST['comprador'];
$comprador = DBRead('mercado', "WHERE nome = '$comprador'");
$id_comprador = $comprador[0]['id'];
$data = $_POST['data_venda'];
include __DIR__ . "/../../funcoes_data/data.php";
$parcelas = $_POST['parcelas'];
$tipo = $_POST['tipo_venda'];
$forma = $_POST['forma'];
$valor = $_POST['valor'];
$valor = str_replace("," , "" , $valor);
$valor_compra = $valor;
$observacoes = str_replace("'", '"',$_POST['observacoes']);
if(!$parcelas){ $parcelas = 1; }
$parcelas = filter_var($parcelas, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 24)));
if (!$parcelas || !is_numeric($valor_compra) || !is_finite((float)$valor_compra) || (float)$valor_compra <= 0 || round((float)$valor_compra * 100) < $parcelas) {
    http_response_code(400);
    exit('Informe um valor total válido e de 1 a 24 parcelas, com pelo menos R$ 0,01 por parcela.');
}
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
    DBUpdate('vendas', $dados, "id_animal = '$id_animal'");
		DBDelete('controle_financeiro', "id_animal = '$id_animal'");
}else{
    DBCreate('vendas', $dados);
}
$dados = array(
  'status' => 2
);
DBUpdate('animais', $dados, "id = '$id_animal'");


// Os relatórios calculam as médias diretamente das vendas.
$animal = DBRead('animais', "WHERE id = '$id_animal'");

//FINANCEIRO
$totalCentavosVenda = (int)round((float)$valor_compra * 100);
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

DBCreate('controle_financeiro', $dados);

}
//FIM FINANCEIRO
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=vender'>";
?>
