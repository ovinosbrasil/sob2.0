<?
include "../_config.php";

$animal = $_POST['animal'];
$id_lote = $_GET['id_lote'];
$verifica_chip = DBRead('animais', "WHERE chip = '$animal'");
$animal = DBRead('animais', "WHERE nome = '$animal'");

if(($animal[0]['id'] <= 0) && ($verifica_chip[0]['id'] <= 0)){
  echo "<script type=\"text/javascript\"> alert(\"Animal não existe. Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}

if(($animal[0]['id'] >= 1) || ($verifica_chip[0]['id'] >= 1)){
$lote = DBRead('lote_vacina', "WHERE id = '$id_lote'");
$id_vacina = $lote[0]['id_vacina'];
$data = $lote[0]['data'];

if($verifica_chip[0]['id'] > 0){ $id_animal = $verifica_chip[0]['id']; }else{ $id_animal = $animal[0]['id']; }

$dados = array(
	'id_animal'	=> $id_animal,
	'id_vacina'	=> $id_vacina,
	'data'	=> $data,
  'id_lote' => $id_lote
);

DBcreate('vacinas', $dados);
}
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=vacina&id_lote=$id_lote'>";
?>
