<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../_config.php";
$id_lote = $_GET['id_lote'];
$lote = $_POST['lote'];
$vacina = $_POST['vacina'];
$data = $_POST['data_inicial'];
include "../funcoes_data/data.php";


$dados = array(
  'nome'	=> $lote,
  'id_vacina' => $vacina,
  'data'  => $data
);


DBUpdate('lote_vacina', $dados, "id = '$id_lote'");

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=vacina&id_lote=$id_lote'>";
?>
