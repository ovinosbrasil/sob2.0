<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../_config.php";
$lote = $_POST['lote'];
$vacina = $_POST['vacina'];
$nova_vacina = $_POST['nova_vacina'];
$data = $_POST['data_inicial'];
include "../funcoes_data/data.php";


if($nova_vacina){
  $dados = array(
    'nome'	=> $nova_vacina
  );
  DBCreate('vacina', $dados);
  $vacina = DBRead('vacina',"WHERE nome = '$nova_vacina'");
  $vacina = $vacina[0]['id'];
}


$dados = array(
  'nome'	=> $lote,
  'id_vacina' => $vacina,
  'data'  => $data
);


DBcreate('lote_vacina', $dados);
$lote = DBRead('lote_vacina', "WHERE nome = '$lote'");
$id_lote = $lote[0]['id'];

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=vacina&id_lote=$id_lote'>";
?>
