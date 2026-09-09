<?
include "../../_config.php";

$id_animal = $_GET['id_animal'];
$premio = $_POST['posicao'].' - '.$_POST['categoria'];

$dados = array(
	'id_animal'	=> $id_animal,
	'premio'	=> $premio,
	'id_julgamento'	=> $_POST['exposicao'],
);

DBcreate('premio', $dados);

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=premios'>";
?>
