<?php
include __DIR__ . "/../_config.php";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

$data = $_POST['data'];
include "../funcoes_data/data.php";
$data = $data;

$evento = $_POST['evento'];
$evento_ = DBRead('julgamento', "WHERE nome = '$evento'");

// TESTE EVENTO
if($evento_[0]['id'] > 0){
  echo "<script type=\"text/javascript\"> alert(\"Evento já existe.Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$dados = array(
	'nome'	=> $_POST['evento'],
	'cidade'	=> $_POST['cidade'],
  'local'	=> $_POST['local'],
  'data'  => $data
);

DBcreate('julgamento', $dados);
$evento = DBRead('julgamento', "WHERE nome = '$evento'");
$id_evento = $evento[0]['id'];

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";
}
?>
