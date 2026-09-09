<?
include "../../_config.php";
$id_animal = $_GET['id_animal'];

$data = $_POST['data_apartacao'];
include "../../funcoes_data/data.php";

$dados = array(
	'peso_inicial'	=> $_POST['peso_inicial'],
	'peso2'	=> $_POST['peso_apartacao'],
	'data2'		=> $data

);
DBUpdate('animais', $dados, "id = '$id_animal'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=avaliacao&avaliacao=1'>";

?>
