<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?
include "../_config.php";
$id_animal = $_GET['id_animal'];
$id_evento = $_GET['id_evento'];

$teste = DBRead('julgamento_controle', "WHERE id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
if($teste[0]['id'] > 0){
    echo "<script type=\"text/javascript\"> alert(\"Animal já cadastrado.Tente novamente\"); </script>
    <script language='javascript'>history.back()</script>";
}else{
    $animal = DBRead('animais', "WHERE id = '$id_animal'");
    $id_pai = $animal[0]['pai'];
    $id_mae = $animal[0]['mae'];

    $evento = DBRead('julgamento', "WHERE id = '$id_evento'");
    $data = $evento[0]['data'];

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
    	'id_animal'	=> $id_animal,
    	'id_julgamento'	=> $id_evento,
      'categoria'	=> $categoria,
      'meses'  => $meses,
      'pai'  => $id_pai,
      'mae'  => $id_mae
    );

    DBcreate('julgamento_controle', $dados);
    $dados = array(
    	'julgamento'	=> 1
    );
    DBUpdate('animais_evento', $dados, "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");

    if($animal[0]['terceiro_pai'] == 0){
      $progenie_pai = DBRead('progene', "WHERE id_lote = '$id_evento' AND id_animal = '$id_pai'");
      if($progenie_pai[0]['id']>0){
        $qtd = $progenie_pai[0]['qtd']+1;
        $dados = array(
        	'qtd'	=> $qtd
        );
        DBUpdate('progene', $dados, "id_lote = '$id_evento' AND id_animal = '$id_pai'");
      }else{
        $dados = array(
        	'qtd'	=> $qtd,
          'id_animal' => $id_pai,
          'sexo'  => "M",
          'id_lote' => $id_evento
        );
        DBcreate('progene', $dados);
      }
    }

    if($animal[0]['terceiro_mae'] == 0){
      $progenie_mae = DBRead('progene', "WHERE id_lote = '$id_evento' AND id_animal = '$id_mae'");
      if($progenie_mae[0]['id']>0){
        $qtd = $progenie_mae[0]['qtd']+1;
        $dados = array(
        	'qtd'	=> $qtd
        );
        DBUpdate('progene', $dados, "id_lote = '$id_evento' AND id_animal = '$id_mae'");
      }else{
        $dados = array(
        	'qtd'	=> $qtd,
          'id_animal' => $id_mae,
          'sexo'  => "F",
          'id_lote' => $id_evento
        );
        DBcreate('progene', $dados);
      }
    }


    echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=exposicao&id_exposicao=$id_evento'>";
}
?>
