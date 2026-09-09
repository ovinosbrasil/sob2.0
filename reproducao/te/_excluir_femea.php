<?
include "../../_config.php";

$id_controle = $_GET['id_controle'];
$id_lote = $_GET['id_lote'];

DBDelete('transplante_controle', "id = '$id_controle'");


//RANKING TE
$qtd=$ultrassom=$nascimento=0;
$ultrassom = DBRead('transplante_controle', "WHERE id_lote = '$id_lote' AND ultrassom = '1'");
$nascimento = DBRead('transplante_controle', "WHERE id_lote = '$id_lote' AND status_nascimento = '1'");
$dados = DBRead('transplante_controle', "WHERE id_lote = '$id_lote'");
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
DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_lote' AND tipo = '2'");
//RANKING TE FIM

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=te&id_lote=$id_lote'>";
?>
