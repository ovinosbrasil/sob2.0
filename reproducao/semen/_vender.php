<?
include "../../_config.php";

$id_embriao = $_GET['id_embriao'];
$comprador = $_POST['comprador'];
$comprador = DBRead('mercado', "WHERE nome = '$comprador'");
$id_comprador = $comprador[0]['id'];
$data = $_POST['data'];
include "../../funcoes_data/data.php";
$parcelas = $_POST['parcelas'];
$tipo = $_POST['tipo_venda'];
$forma = $_POST['forma'];
$valor = $_POST['valor'];
$valor = str_replace("," , "" , $valor);
$valor_compra = $valor;
$observacoes = str_replace("'", '"',$_POST['observacoes']);
$doses = $_POST['qtd'];

$embriao = DBRead('semen', "WHERE id = '$id_embriao'");
$qtd = $embriao[0]['qtd'];
$id_macho = $embriao[0]['id_animal'];
if($embriao[0]['terceiro']){
  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
}else{
  $macho = DBRead('animais', "WHERE id = '$id_macho'");
}

if($doses > $qtd){
  echo "<script type=\"text/javascript\"> alert(\"Quantidade de doses inexistente. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{


if(!$parcelas){ $parcelas = 1; }
$dados = array(
	'id_semen'	=> $id_embriao,
  'data'	=> $data,
	'doses'	=> $doses,
	'forma_de_pagamento'	=> $forma,
	'parcelas'	=> $parcelas,
	'tipo_venda'		=> $tipo,
	'comprador'	=> $id_comprador,
	'valor'			=> $valor,
	'obs'			=> $observacoes
);

DBCreate('venda_semen', $dados);
$qtd_final = $qtd-$doses;
$dados = array(
	'qtd'	=> $qtd_final
);
DBUpdate('semen', $dados, "id = $id_embriao");

$id_venda = DBRead('venda_semen', "ORDER BY id desc");
$id_venda = $id_venda[0]['id'];


//FINANCEIRO
$valor = $valor_compra/$parcelas;
$descricao = "Sêmen: ".$macho[0]['nome'];
while($parcelas > 0){
	$dados = array(
	'titulo'			=> $descricao,
	'data'				=> $data,
	'valor'		=> $valor,
	'id_animal'		=> $id_animal,
  'forma_de_pagamento'    => $forma,
  'id_comprador' => $id_comprador,
  'id_semen'  => $id_venda
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
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=semen'>";
}
?>
