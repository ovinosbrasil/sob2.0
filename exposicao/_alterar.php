<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../_config.php";

$data = $_POST['data'];
include "../funcoes_data/data.php";
$data = $data;

$evento = $_POST['evento'];
$id_evento = $_GET['id_evento'];

$evento_ = DBRead('julgamento', "WHERE nome = '$evento' AND id !='$id_evento'");
// TESTE EVENTO
if($evento_[0]['id'] > 0){
  echo "<script type=\"text/javascript\"> alert(\"Evento já existe.Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$dados = array(
	'nome'	=> $_POST['evento'],
	'cidade'	=> $_POST['cidade'],
  'local'	=> $_POST['local'],
  'data'  => $data
);

DBUpdate('julgamento', $dados, "id = '$id_evento'");


$evento = DBRead('julgamento_controle', "WHERE id_julgamento ='$id_evento'");
foreach ($evento as $evento_) {
  $id_animal = $evento_['id_animal'];
  $animal = DBRead('animais',"WHERE id = '$id_animal'");
  list($ano, $mes, $dias) = explode('-', $animal[0]['data_de_nascimento']);
  list($ano_, $mes_, $dias_) = explode('-', $data);

  $ano_final = $ano_ - $ano;
  $mes_final = $mes_ - $mes;
  $dias_final = $dias_ - $dias;


  if($dias_final < 0){
    $mes_final = $mes_final-1;
    $dias_final = $dias_final+31;
  }

$meses = ($ano_final*12)+$mes_final;
$categoria = 0;


if(($meses >= 4) && ($meses < 5)){
  if(($meses==4) && ($dias_final == 0)){$categoria = 1; }else{ $categoria = 1; }
  $categoria = 1;
}

if(($meses >= 5) && ($meses < 6)){
  if(($meses==5) && ($dias_final == 0)){$categoria = 2; }else{ $categoria = 2; }
  $categoria = 2;
}

if(($meses >= 6) && ($meses < 7)){
  if(($meses==6) && ($dias_final == 0)){$categoria = 3; }else{ $categoria = 3; }
  $categoria = 3;
}

if(($meses >= 7) && ($meses < 8)){
  if(($meses==7) && ($dias_final == 0)){$categoria = 4; }else{ $categoria = 4; }
}

if(($meses >= 8) && ($meses < 9)){
  if(($meses==8) && ($dias_final == 0)){$categoria = 5; }else{ $categoria = 5; }
}

if(($meses >= 9) && ($meses < 10)){
  if(($meses==9) && ($dias_final == 0)){$categoria = 6; }else{ $categoria = 6; }
}

if(($meses >= 10) && ($meses < 11)){
  if(($meses==10) && ($dias_final == 0)){ $categoria = 7; }else{ $categoria = 7; }
  $categoria = 7;
}

if(($meses >= 11) && ($meses < 12)){
  if(($meses==11) && ($dias_final == 0)){$categoria = 8; }else{ $categoria = 8; }
}

if(($meses >= 12) && ($meses < 15)){
  if(($meses==12) && ($dias_final == 0)){$categoria = 9; }else{ $categoria = 9; }
}

if(($meses >= 15) && ($meses < 18)){
  if(($meses==15) && ($dias_final == 0)){$categoria = 10; }else{ $categoria = 10; }
}

if(($meses >= 18) && ($meses < 21)){
  if(($meses==18) && ($dias_final == 0)){ $categoria = 11; }else{ $categoria = 11; }
}

if(($meses >= 21) && ($meses < 24)){
  if(($meses==21) && ($dias_final == 0)){$categoria = 12; }else{ $categoria = 12; }
}

if(($meses >= 24) && ($meses < 30)){
  if(($meses==24) && ($dias_final == 0)){$categoria = 13; }else{ $categoria = 13; }
}

if(($meses >= 30) && ($meses < 36)){
  if(($meses==30) && ($dias_final == 0)){$categoria = 14; }else{ $categoria = 14; }
}

  $dados = array(
      'categoria'	=> $categoria,
      'meses'  => $meses,
  );

  DBUpdate('julgamento_controle', $dados, "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
}

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";
}
?>
