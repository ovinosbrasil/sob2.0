<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../../_config.php";

$id_lote = $_GET['id_lote'];
$status = $_GET['status'];
$id_lote2 = $_GET['id_lote2'];
$reproducao = $_GET['reproducao'];
$tipo = $_GET['tipo'];
$id_animal = $_GET['id_animal'];
$terceiro = $_GET['terceiro'];

$dados = array(
  'ultrassom' => $status
);
if($reproducao == 0){
  DBUpdate('monta_controle', $dados, "id = '$id_lote'");
}
if($reproducao == 1){
  DBUpdate('inseminacao_controle', $dados, "id = '$id_lote'");
  echo "a";
}
if($reproducao == 2){
  DBUpdate('transplante_controle', $dados, "id = '$id_lote'");
}


//RANKING MONTA
if($reproducao == 0){
$qtd=$ultrassom=$nascimento=0;
$ultrassom = DBRead('monta_controle', "WHERE id_monta = '$id_lote2' AND ultrassom = '1'");
$dados = DBRead('monta_controle', "WHERE id_monta = '$id_lote2'");
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
DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_lote2' AND tipo = '0'");
}
//RANKING MONTA FIM

//RANKING IA
if($reproducao == 1){
$qtd=$ultrassom=$nascimento=0;
$ultrassom = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote2' AND ultrassom = '1'");
$dados = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote2'");
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
DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_lote2' AND tipo = '1'");
}
//RANKING IA FIM

//RANKING TE
if($reproducao == 2){
$qtd=$ultrassom=$nascimento=0;
$ultrassom = DBRead('transplante_controle', "WHERE id_lote = '$id_lote2' AND ultrassom = '1'");
$dados = DBRead('transplante_controle', "WHERE id_lote = '$id_lote2'");
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
DBUpdate('lotes_reproducao', $dados, "id_lote = '$id_lote2' AND tipo = '2'");
}
//RANKING TE FIM

if($tipo){
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=lista_ultrassom&id_lote=$id_lote2&reproducao=$reproducao+&tipo=1'>";
}else{
  echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=lista_ultrassom&id_animal=$id_animal&tipo=0&terceiro=$terceiro'>";
}

?>
