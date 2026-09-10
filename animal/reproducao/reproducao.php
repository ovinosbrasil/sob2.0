<table class="table table-bordered" id="tabela_padrao" width="98%">
  <tr>
    <th>Lote</th>
    <th>Tipo</th>
    <th>Macho</th>
    <th>Receptora</th>
    <th>Data</th>
    <th>Previsão de parto</th>
    <th>Ultrassom/Status</th>
  </tr>
  <?
  if($animal[0]['sexo'] == 'Fêmea'){

  $monta = DBRead('monta_controle', "WHERE id_animal = '$id_animal' AND terceiro = '0'");
  foreach ($monta as $monta_) {
    $id_monta = $monta_['id_monta'];
    $lote = DBRead('monta', "WHERE id = '$id_monta'");
    $id_macho = $lote[0]['id_animal'];
    if($lote[0]['terceiro']){
      $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
    }else{
      $macho = DBRead('animais', "WHERE id = '$id_macho'");
    }

    $data = $lote[0]['data_inicio'];
    $data_atual = $data;
    $data = '0';
    $data['0'] = $data_atual['8'];
    $data['1'] = $data_atual['9'];
    $data['2'] = "/";
    $data['3'] = $data_atual['5'];
    $data['4'] = $data_atual['6'];
    $data['5'] = "/";
    $data['6'] = $data_atual['0'];
    $data['7'] = $data_atual['1'];
    $data['8'] = $data_atual['2'];
    $data['9'] = $data_atual['3'];
    $data_inicio = $data;

    $data = $lote[0]['data_fim'];
    $data_atual = $data;
    $data = '0';
    $data['0'] = $data_atual['8'];
    $data['1'] = $data_atual['9'];
    $data['2'] = "/";
    $data['3'] = $data_atual['5'];
    $data['4'] = $data_atual['6'];
    $data['5'] = "/";
    $data['6'] = $data_atual['0'];
    $data['7'] = $data_atual['1'];
    $data['8'] = $data_atual['2'];
    $data['9'] = $data_atual['3'];
    $data_fim = $data;


    $data = explode("/", $data_inicio);
    list($dia, $mes, $ano) = $data;
    $data = "$ano$mes$dia";
    $nextdate = addDayIntoDate($data,140);
    $data[0] = $nextdate[6];
  	$data[1] = $nextdate[7];
  	$data[2] = "/";
  	$data[3] = $nextdate[4];
  	$data[4] = $nextdate[5];
  	$data[5] = "/";
  	$data[6] = $nextdate[0];
  	$data[7] = $nextdate[1];
  	$data[8] = $nextdate[2];
  	$data[9] = $nextdate[3];
  	$data_previsao1 = $data;

    $data = explode("/", $data_fim);
    list($dia, $mes, $ano) = $data;
    $data = "$ano$mes$dia";
    $nextdate = addDayIntoDate($data,160);
    $data[0] = $nextdate[6];
  	$data[1] = $nextdate[7];
  	$data[2] = "/";
  	$data[3] = $nextdate[4];
  	$data[4] = $nextdate[5];
  	$data[5] = "/";
  	$data[6] = $nextdate[0];
  	$data[7] = $nextdate[1];
  	$data[8] = $nextdate[2];
  	$data[9] = $nextdate[3];
  	$data_previsao2 = $data;

  ?>
  <tr>
    <td>Lote <?=$lote[0]['codigo']?></td>
    <td>Monta natural</td>
    <? if($lote[0]['terceiro']){ ?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
    <? if(!$lote[0]['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
    <td>--</td>
    <td>Inicial: <?=$data_inicio?> Final: <?=$data_fim?></td>
    <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
    <td>
      <? if($monta_['ultrassom'] == 1){ ?> <span style="color:#093;">Positivo - </span> <? } ?>
      <? if($monta_['ultrassom'] == 2){ ?> <span style="color:#F00;">Negativo - </span>  <? } ?>
      <? if($monta_['status_nascimento'] == 1){ ?> <span style="color:#093;">Nasceu</span> <? }else{ ?> <span style="color:#F00;">Não Nasceu</span> <? } ?></td>
  </tr>
<? }


//INSEMINAÇÃO
$inseminacao = DBRead('inseminacao_controle', "WHERE id_femea = '$id_animal' AND terceiro = '0'");
foreach ($inseminacao as $inseminacao_) {
  $id_inseminacao = $inseminacao_['id_lote'];
  $lote = DBRead('inseminacao', "WHERE id = '$id_inseminacao'");
  $id_macho = $lote[0]['id_macho'];
  if($lote[0]['terceiro']){
    $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
  }else{
    $macho = DBRead('animais', "WHERE id = '$id_macho'");
  }

  $data = $lote[0]['data'];
  $data_atual = $data;
  $data = '0';
  $data['0'] = $data_atual['8'];
  $data['1'] = $data_atual['9'];
  $data['2'] = "/";
  $data['3'] = $data_atual['5'];
  $data['4'] = $data_atual['6'];
  $data['5'] = "/";
  $data['6'] = $data_atual['0'];
  $data['7'] = $data_atual['1'];
  $data['8'] = $data_atual['2'];
  $data['9'] = $data_atual['3'];
  $data_inseminacao = $data;


  $data = explode("/", $data_inseminacao);
  list($dia, $mes, $ano) = $data;
  $data = "$ano$mes$dia";
  $nextdate = addDayIntoDate($data,140);
  $data[0] = $nextdate[6];
  $data[1] = $nextdate[7];
  $data[2] = "/";
  $data[3] = $nextdate[4];
  $data[4] = $nextdate[5];
  $data[5] = "/";
  $data[6] = $nextdate[0];
  $data[7] = $nextdate[1];
  $data[8] = $nextdate[2];
  $data[9] = $nextdate[3];
  $data_previsao1 = $data;

  $data = explode("/", $data_inseminacao);
  list($dia, $mes, $ano) = $data;
  $data = "$ano$mes$dia";
  $nextdate = addDayIntoDate($data,160);
  $data[0] = $nextdate[6];
  $data[1] = $nextdate[7];
  $data[2] = "/";
  $data[3] = $nextdate[4];
  $data[4] = $nextdate[5];
  $data[5] = "/";
  $data[6] = $nextdate[0];
  $data[7] = $nextdate[1];
  $data[8] = $nextdate[2];
  $data[9] = $nextdate[3];
  $data_previsao2 = $data;

?>
<tr>
  <td>Lote <?=$lote[0]['codigo']?></td>
  <td>Inseminação artificial</td>
  <? if($lote[0]['terceiro']){ ?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
  <? if(!$lote[0]['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
  <td>--</td>
  <td><?=$data_inseminacao?></td>
  <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
  <td>
    <? if($inseminacao_['ultrassom'] == 1){ ?> <span style="color:#093;">Positivo - </span> <? } ?>
    <? if($inseminacao_['ultrassom'] == 2){ ?> <span style="color:#F00;">Negativo - </span>  <? } ?>
   <? if($inseminacao_['status_nascimento'] == 1){ ?> <span style="color:#093;">Nasceu</span> <? }else{ ?> <span style="color:#F00;">Não Nasceu</span> <? } ?></td>
</tr>
<? }

//TE
$te = DBRead('transplante', "WHERE id_mae = '$id_animal' AND terceiro_mae = '0'");
foreach ($te as $te_) {
  $id_te = $te_['id'];
  $id_macho = $te_['id_pai'];
  if($te_['terceiro_pai']){
    $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
  }else{
    $macho = DBRead('animais', "WHERE id = '$id_macho'");
  }

  $data = $te_['data'];
  $data_atual = $data;
  $data = '0';
  $data['0'] = $data_atual['8'];
  $data['1'] = $data_atual['9'];
  $data['2'] = "/";
  $data['3'] = $data_atual['5'];
  $data['4'] = $data_atual['6'];
  $data['5'] = "/";
  $data['6'] = $data_atual['0'];
  $data['7'] = $data_atual['1'];
  $data['8'] = $data_atual['2'];
  $data['9'] = $data_atual['3'];
  $data_te = $data;


  $data = explode("/", $data_te);
  list($dia, $mes, $ano) = $data;
  $data = "$ano$mes$dia";
  $nextdate = addDayIntoDate($data,140);
  $data[0] = $nextdate[6];
  $data[1] = $nextdate[7];
  $data[2] = "/";
  $data[3] = $nextdate[4];
  $data[4] = $nextdate[5];
  $data[5] = "/";
  $data[6] = $nextdate[0];
  $data[7] = $nextdate[1];
  $data[8] = $nextdate[2];
  $data[9] = $nextdate[3];
  $data_previsao1 = $data;

  $data = explode("/", $data_te);
  list($dia, $mes, $ano) = $data;
  $data = "$ano$mes$dia";
  $nextdate = addDayIntoDate($data,160);
  $data[0] = $nextdate[6];
  $data[1] = $nextdate[7];
  $data[2] = "/";
  $data[3] = $nextdate[4];
  $data[4] = $nextdate[5];
  $data[5] = "/";
  $data[6] = $nextdate[0];
  $data[7] = $nextdate[1];
  $data[8] = $nextdate[2];
  $data[9] = $nextdate[3];
  $data_previsao2 = $data;

  $te_controle = DBRead('transplante_controle', "WHERE id_lote = '$id_te'");
  foreach ($te_controle as $te_controle_) {
?>

<tr>
  <td>Lote <?=$te_['codigo']?></td>
  <td>Trans. de embriões</td>
  <? if($te_['terceiro']){ ?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
  <? if(!$te_['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
  <td><?=$te_controle_['receptora']?></td>
  <td><?=$data_te?></td>
  <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
  <td>
    <? if($te_controle_['ultrassom'] == 1){ ?> <span style="color:#093;">Positivo - </span> <? } ?>
    <? if($te_controle_['ultrassom'] == 2){ ?> <span style="color:#F00;">Negativo - </span>  <? } ?>
    <? if($te_controle_['status_nascimento'] == 1){ ?> <span style="color:#093;">Nasceu</span> <? }else{ ?> <span style="color:#F00;">Não Nasceu</span> <? } ?></td>
</tr>
<? } } ?>
<? } ?>
</table>
