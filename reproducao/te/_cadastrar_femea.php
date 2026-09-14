<?php
require __DIR__ . "/../../_config.php";
header('Content-Type: text/html; charset=UTF-8');
$id_lote = $_GET['id_lote'];
$receptora = $_POST['receptora'];
$teste = DBRead('transplante_controle', "WHERE receptora = '$receptora' AND id_lote = '$id_lote'");
if(!empty($teste[0]['id'])){
  echo "<script type=\"text/javascript\"> alert(\"Receptora já cadastrada.Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$dados = array(
	'receptora'	=> $_POST['receptora'],
	'id_lote'	=> $id_lote,
	'ultrassom' => 0,
	'status_nascimento' => 0,
	'n_embrioes' => ''
);

DBcreate('transplante_controle', $dados);

//RANKING TE
$qtd=$ultrassom=$nascimento=0;
$ultrassom = DBRead('transplante_controle', "WHERE id_lote = '$id_lote' AND ultrassom = '1'");
$nascimento = DBRead('transplante_controle', "WHERE id_lote = '$id_lote' AND status_nascimento = '1'");
$dados = DBRead('transplante_controle', "WHERE id_lote = '$id_lote'");
$qtd = count($dados);
  if(!empty($ultrassom[0]['id'])){
    $ultrassom = count($ultrassom);
    $ultrassom = ($ultrassom*100)/$qtd;
  }else{
    $ultrassom = 0;
  }
  if(!empty($nascimento[0]['id'])){
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
}
?>
