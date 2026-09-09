<?
include "../_config.php";
$id_animal = $_GET['id_animal'];
$id_evento = $_GET['id_evento'];

$comprador = $_POST['comprador'];
$comprador = DBRead('mercado', "WHERE nome = '$comprador'");
$id_comprador = $comprador[0]['id'];
$data = $_POST['data'];
include "../funcoes_data/data.php";
$parcelas = $_POST['parcelas'];
$tipo = $_POST['tipo_venda'];
$forma = $_POST['forma'];
$valor = $_POST['valor'];
$valor = str_replace("," , "" , $valor);
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
if($teste[0]['id'] > 0){
    DBUpdate('vendas', $dados, "id_animal = '$id_animal'");
		DBDelete('controle_financeiro', "id_animal = '$id_animal'");
}else{
    DBCreate('vendas', $dados);
}
$dados = array(
  'status' => 2
);
DBUpdate('animais', $dados, "id = '$id_animal'");


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
DBUpdate('reprodutor', $dados, "id_macho = '$id_macho'");
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
DBUpdate('matriz', $dados, "id_femea = '$id_femea'");
}
//RANKING FIM MATRIZ


//FINANCEIRO
$valor = $valor_compra/$parcelas;
$descricao = "Venda animal - ".$animal[0]['nome'];
while($parcelas > 0){
	$dados = array(
	'titulo'			=> $descricao,
	'data'				=> $data,
	'valor'		=> $valor,
	'id_animal'		=> $id_animal,
	'comprador'	=> $id_comprador,
  'forma_de_pagamento'    => $forma
);

DBCreate('controle_financeiro', $dados);

list($ano, $mes, $dias) = explode('-', $data);
$mes = $mes+1;
if($mes == 13){ $mes = 1; $ano = $ano+1; }
if(($mes == 2) && (($dias == 29) || ($dias == 30) || ($dias == 31)) ){ $dia = 28; }
if($dias == 31){ $dias = 30;}

$data = $ano.'-'.$mes.'-'.$dias;
$parcelas--;
$status = 0;
}
//FIM FINANCEIRO
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";
?>
