<?
include "../_config.php";
$animal = $_POST['animal'];
$data = $_POST['data'];
include "../funcoes_data/data.php";
$peso = $_POST['valor'];
$peso = str_replace("," , "" , $peso);

$animal = DBRead('animais', "WHERE nome = '$animal'");
if($animal[0]['id'] < 1){
  echo "<script type=\"text/javascript\">
   alert(\"Cuidado: Animal ($animal) não existe.\");
   </script>";
}else{
$id_animal = $animal[0]['id'];
$dados = array(
	'id_animal'	=> $id_animal,
	'data'	=> $data,
  'peso'	=> $peso,
);
DBcreate('pesagem', $dados);
}
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=pesagem&id_animal=$id_animal'>";
?>
