<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../../_config.php";

$id_lote = $_GET['id_lote'];
$status = $_GET['status'];

$dados = array(
  'ultrassom' => $status
);

DBUpdate('monta_controle', $dados, "id = '$id_lote'");
$monta = DBRead('monta_controle', "WHERE id = '$id_lote'");
$id_lote = $monta[0]['id_monta'];

//RANKING MONTA
$qtd=$ultrassom=$nascimento=0;
$ultrassom = DBRead('monta_controle', "WHERE id_monta = '$id_lote' AND ultrassom = '1'");
$dados = DBRead('monta_controle', "WHERE id_monta = '$id_lote'");
$qtd = count($dados);
  if($ultrassom[0]['id'] > 0){
    $ultrassom = count($ultrassom);
    $ultrassom = ($ultrassom*100)/$qtd;
  }else{
    $ultrassom = 0;
  }
$dados = array(
  'ultrassom' => $ultrassom
);
DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_lote' AND tipo = '0'");
//RANKING MONTA FIM

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=monta&id_lote=$id_lote'>";
?>
