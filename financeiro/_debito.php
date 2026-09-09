<?
include "../_config.php";

$data = $_POST['data_debito'];
$valor = $_POST['valor_debito'];
$valor = str_replace("," , "" , $valor);
$descricao = $_POST['descricao_debito'];
$mes = date('m');
$ano = date('Y');
$parcelas = $_POST['parcelas_debito'];
$forma = $_POST['forma_debito'];
$categoria = $_POST['categoria_debito'];

include "../funcoes_data/data.php";

while($parcelas > 0){
	$dados = array(
	'titulo'			=> $descricao,
	'data'				=> $data,
	'valor'		=> $valor,
	'tipo'				=> 1,
	'categoria'	=> $categoria,
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

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=financeiro'>";

?>
