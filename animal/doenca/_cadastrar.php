<?
include "../../_config.php";

$id_animal = $_GET['id_animal'];
$data = $_POST['data_doenca'];
include "../../funcoes_data/data.php";
$doenca = $_POST['doenca'];
$nova_doenca = $_POST['nova_doenca'];

if($nova_doenca){
  $dados = array(
    'nome'	=> $nova_doenca
  );
  DBCreate('doenca', $dados);
  $doenca = DBRead('doenca',"WHERE nome = '$nova_doenca'");
  $doenca = $doenca[0]['id'];
}


$dados = array(
	'id_animal'	=> $id_animal,
	'id_doenca'	=> $doenca,
	'obs'	=> preg_replace("'", '"',$_POST['observacoes_doenca']),
	'data'	=> $data
);

DBcreate('doencas', $dados);

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=doenca'>";
?>
