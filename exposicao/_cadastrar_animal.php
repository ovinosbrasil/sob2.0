<?php
include __DIR__ . "/../_config.php";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
$id_animal = $_GET['id_animal'];
$id_evento = $_GET['id_evento'];

$teste = DBRead('animais_evento', "WHERE id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
if($teste[0]['id']>0){
    echo "<script type=\"text/javascript\"> alert(\"Animal já cadastrado.Tente novamente\"); </script>
    <script language='javascript'>history.back()</script>";
}else{
  $dados = array(
    'id_animal'	=> $id_animal,
    'id_julgamento'	=> $id_evento,
    'leilao' => 0,
    'julgamento' => 0,
  );
  DBcreate('animais_evento', $dados);
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";
}


?>
