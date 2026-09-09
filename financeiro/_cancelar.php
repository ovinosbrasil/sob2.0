<?
include "../_config.php";

$id_financeiro = $_GET['id_financeiro'];
$data_inicial = $_GET['data_inicial'];
$data_final = $_GET['data_final'];

$dados = array(
  'status'	=> 0
);
DBUpdate('controle_financeiro', $dados, "id = '$id_financeiro'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=financeiro&data_inicial=$data_inicial&data_final=$data_final'>";
?>
