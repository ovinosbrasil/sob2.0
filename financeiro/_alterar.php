<?
include "../_config.php";
$id_financeiro = $_GET['id_financeiro'];
$tipo = $_GET['tipo'];
$data_inicial = $_GET['data_inicial'];
$data_final = $_GET['data_final'];


$data = $_POST['data_alterar'];
$valor = $_POST['valor_alterar'];
$valor = str_replace("," , "" , $valor);
$id_cliente = $_POST['id_cliente_alterar'];
$descricao = $_POST['descricao_alterar'];
$mes = date('m');
$ano = date('Y');
$forma = $_POST['forma_alterar'];
$categoria = $_POST['categoria_alterar'];

include "../funcoes_data/data.php";

if($tipo == 0){
	$dados = array(
	'titulo'			=> $descricao,
	'data'				=> $data,
	'valor'		=> $valor,
  'forma_de_pagamento'    => $forma
);
}else{
  $dados = array(
  'titulo'			=> $descricao,
  'data'				=> $data,
  'valor'		=> $valor,
  'categoria'	=> $categoria,
  'forma_de_pagamento'    => $forma
);

}


DBUpdate('controle_financeiro', $dados, "id = '$id_financeiro'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=financeiro&data_inicial=$data_inicial&data_final=$data_final'>";

?>
