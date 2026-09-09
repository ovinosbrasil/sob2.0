<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../../_config.php";
$id_lote = $_GET['id_lote'];
$receptora = $_POST['receptora'];
$teste = DBRead('transplante_controle', "WHERE receptora = '$receptora' AND id_lote = '$id_lote'");
if($teste[0]['id'] > 0){
  echo "<script type=\"text/javascript\"> alert(\"Receptora já cadastrada.Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$dados = array(
	'receptora'	=> $_POST['receptora'],
	'id_lote'	=> $id_lote
);

DBcreate('transplante_controle', $dados);

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
}
?>
