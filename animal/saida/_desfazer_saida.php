<?
include "../../_config.php";

$id_animal = $_GET['id_animal'];
$dados = array(
  'data_de_saida'	=> NULL,
  'status'	=> 0,
  'observacoes_de_saida'	=> '',
  'causa_da_perda'	=> ''
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
