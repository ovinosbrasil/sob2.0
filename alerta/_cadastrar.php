<?php
require __DIR__ . "/../_config.php";
$titulo = str_replace("'", '"', $_POST['titulo']);
$data = $_POST['data'];
include "../funcoes_data/data.php";
$prioridade = $_POST['prioridade'];

$dados = array(
	'titulo'	=> $titulo,
	'data'		=> $data,
	'status'	=> $prioridade
);

DBCreate('alerta', $dados);

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php'>";

?>
