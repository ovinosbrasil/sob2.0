<?
$data = $animal[0]['data_de_nascimento'];
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
$data_nascimento = $data;

$data = $animal[0]['data_de_entrada'];
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
$data_de_entrada = $data;


$data = $data_nascimento;
list($dia, $mes, $ano) = explode('/', $data);
// Descobre que dia é hoje e retorna a unix timestamp
$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
// Descobre a unix timestamp da data de nascimento do fulano
$nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
// Depois apenas fazemos o cálculo já citado :)
$anos = floor((((($hoje - $nascimento) / 60) / 60) / 24));
$idade_anos  = floor($anos /365);
$idade_meses = (($anos /365) - $idade_anos) * 12;
$idade_meses = (int)$idade_meses;
$idade_meses = round($idade_meses);
?>
<table class="table table-bordered" id="tabela_padrao">
  <tr>
    <th onclick="atualizar(1)" style="cursor:pointer;">Idade</th>
    <th onclick="atualizar(2)" style="cursor:pointer;">Crias</th>
    <th onclick="atualizar(3)" style="cursor:pointer;">Partos</th>
    <th onclick="atualizar(4)" style="cursor:pointer;">Intervalo de partos / Rebanho</th>
    <th onclick="atualizar(5)" style="cursor:pointer;">Prolificidade / Rebanho</th>
  </tr>
  <?
  $matriz = DBRead('matriz', "WHERE id_femea = '$id_animal'");

  $media = DBRead('matriz');
  foreach ($media as $media_){
    if($media_['nota'] > 0){
      $total_intervalo = $total_intervalo + $media_['intervalo']; $qtd_intervalo++;
    }
    if($media_['prolificidade'] > 0){
      $total_prolificidade = $total_prolificidade + $media_['prolificidade']; $qtd_prolificidade++;
    }
  }
  $media_intervalo = $total_intervalo/$qtd_intervalo;
  $media_prolificidade = $total_prolificidade/$qtd_prolificidade;



  $intervalo = $matriz[0]['intervalo'];
  $anos_intervalo = (int)floor($intervalo/365);
  $resto_ano_intervalo = $intervalo%365;
  $meses_intervalo = (int)floor($resto_ano_intervalo/30);
  $dias_fim_intervalo = $resto_ano_intervalo%30;

  $intervalo2 = $media_intervalo;
  $anos_intervalo2 = (int)floor($intervalo2/365);
  $resto_ano_intervalo2 = $intervalo2%365;
  $meses_intervalo2 = (int)floor($resto_ano_intervalo2/30);
  $dias_fim_intervalo2 = $resto_ano_intervalo2%30;
  ?>
  <tr>
    <td><?=$idade_anos?> Anos <?=$idade_meses?> Meses</td>
    <td><?=$matriz[0]['qtd_crias']?></td>
    <td><?=$matriz[0]['qtd_partos']?></td>
    <td>
      <? if($matriz[0]['intervalo'] < $media_intervalo){?> <span style="color:green;"> <? }else{ ?>  <span style="color:red;"> <? } ?>
      <?=$anos_intervalo,"A ", $meses_intervalo,"M ", $dias_fim_intervalo,"D";?> </span> / <?=$anos_intervalo2,"A ", $meses_intervalo2,"M ", $dias_fim_intervalo2,"D";?></td>
    <td>
      <? if($matriz[0]['prolificidade'] > $media_prolificidade){ ?> <span style="color:green;"> <? }else{ ?>  <span style="color:red;"> <? } ?>
      <?=number_format($matriz[0]['prolificidade'],2,",",".");?> </span> / <?=number_format($media_prolificidade,2,",",".");?>
    </td>
  </table>
