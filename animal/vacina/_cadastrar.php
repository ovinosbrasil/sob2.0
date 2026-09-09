<?
include "../../_config.php";

$id_animal = $_GET['id_animal'];
$data = $_POST['data_vacina'];
include "../../funcoes_data/data.php";
$vacina = $_POST['vacina'];
$nova_vacina = $_POST['nova_vacina'];

if($nova_vacina){
  $dados = array(
    'nome'	=> $nova_vacina
  );
  DBCreate('vacina', $dados);
  $vacina = DBRead('vacina',"WHERE nome = '$nova_vacina'");
  $vacina = $vacina[0]['id'];
}


$dados = array(
	'id_animal'	=> $id_animal,
	'id_vacina'	=> $vacina,
	'obs'	=> str_replace("'", '"',$_POST['observacoes_vacina']),
	'data'	=> $data
);

DBcreate('vacinas', $dados);

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=vacina'>";
?>
