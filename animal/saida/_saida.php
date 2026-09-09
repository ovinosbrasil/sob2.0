<?
include "../../_config.php";

$id_animal = $_GET['id_animal'];
$data = $_POST['data_saida'];
include "../../funcoes_data/data.php";


$dados = array(
	'data_de_saida'	=> $data,
	'status'	=> $_POST['tipo_saida'],
	'observacoes_de_saida'	=> str_replace("'", '"',$_POST['observacoes']),
	'causa_da_perda'	=> $_POST['causa']
);
DBUpdate('animais', $dados, "id = '$id_animal'");

//RANKING MORTALIDADE REPRODUTOR
$animal = DBRead('animais', "WHERE id = '$id_animal'");
$id_pai = $animal[0]['pai'];
$crias = DBRead('animais', "WHERE pai = '$id_pai' AND terceiro_pai = '0'");
$qtd_crias = count($crias);
$morte = DBRead('animais', "WHERE pai = '$id_pai' AND status = '1' AND causa_da_perda = 'Nascimento'");
$qtd_mortes = count($morte);
$qtd_mortes = ($qtd_mortes*100)/$qtd_crias;
$dados = array(
  'qtd_mortes' => $qtd_mortes
);
DBUpdate('reprodutor', $dados, "id_macho = '$id_pai'");
//FIM RANKING MORTALIDADE REPRODUTOR

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=saida'>";
?>
