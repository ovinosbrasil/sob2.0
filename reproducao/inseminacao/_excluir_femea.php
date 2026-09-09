<?
include "../../_config.php";

$id_mae = $_GET['id_mae'];
$id_lote = $_GET['id_lote'];

DBDelete('inseminacao_controle', "id_femea = '$id_mae' AND id_lote = '$id_lote'");

//RANKING IA
$qtd=$ultrassom=$nascimento=0;
$ultrassom = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote' AND ultrassom = '1'");
$nascimento = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote' AND status_nascimento = '1'");
$dados = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote'");
$qtd = count($dados);
  if($ultrassom[0]['id'] > 0){
    $ultrassom = count($ultrassom);
    $ultrassom = ($ultrassom*100)/$qtd;
  }else{
    $ultrassom = 0;
  }
  if($nascimento[0]['id'] > 0){
    $nascimento = count($nascimento);
    $nascimento = ($nascimento*100)/$qtd;
  }else{
    $nascimento = 0;
  }
$dados = array(
  'ultrassom' => $ultrassom,
  'crias'   =>  $nascimento,
  'femeas' => $qtd
);
DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_lote' AND tipo = '1'");
//RANKING IA FIM
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=inseminacao&id_lote=$id_lote'>";
?>
