<?php
include __DIR__ . "/../_config.php";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

$data = $_POST['data'];
include "../funcoes_data/data.php";
$data = $data;

$evento = $_POST['evento'];
$id_evento = $_GET['id_evento'];

$evento_ = DBRead('julgamento', "WHERE nome = '$evento' AND id !='$id_evento'");
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

DBUpdate('julgamento', $dados, "id = '$id_evento'");


$evento = DBRead('julgamento_controle', "WHERE id_julgamento ='$id_evento'");
require_once __DIR__ . "/../funcoes_data/categorias.php";
foreach ($evento as $evento_) {
  $id_animal = $evento_['id_animal'];
  $animal = DBRead('animais',"WHERE id = '$id_animal'");
  $idade = calcularIdadeMesesDias($animal[0]['data_de_nascimento'], $data);
  if (!$idade) {
    $meses = 0; $dias_final = 0; $categoria = 0;
  } else {
    $meses = $idade['meses'];
    $dias_final = $idade['dias'];
    $categoria = determinarCategoriaPorIdade($meses, $dias_final);
  }

  $dados = array(
      'categoria'	=> $categoria,
      'meses'  => $meses,
  );

  DBUpdate('julgamento_controle', $dados, "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
}

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";
}
?>
