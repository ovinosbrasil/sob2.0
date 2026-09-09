<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sistema Ovinos Brasil</title>
<!-- Principal -->

<style rel="stylesheet" type="text/css">
* {
filter:none !important;
-ms-filter:none !important;
}

@media print {
  #quebra {
    page-break-before: always;
  }
  a[href]:after {
  content: none !important;
   }
}


body {
margin:0;
padding:0;
line-height: 1.0em;
}

#tabela_padrao{
	border-collapse: collapse;
  border:1px solid lightgray;
}

#tabela_padrao td{
		border-collapse: collapse;
    border:1px solid lightgray;
}
</style>

</head>
<?
include "../_config.php";
$filtro = $_GET['filtro'];
if(($filtro == 'Todos') || ($filtro == 'Rebanho')){
  $animais = DBRead('animais', "WHERE status = '0'");
}

if($filtro == 'Sexo'){
  $sexo = $_GET['sexo'];
  $animais = DBRead('animais', "WHERE status = '0' AND sexo = '$sexo'");
}


$qtd = count($animais);
$x = $qtd/50;
while($x >= 0){
?>

<body style="width:100%; margin-left:0%; -webkit-print-color-adjust: exact; font-family: Arial, 'Helvetica Neue'; font-size: 14px; color:#5a5a5a;" onload="window.print()">
  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_tabelas">
      <div id="topo_logo" style="width:100%; text-align:center; margin-bottom:1%; margin-top:-1%;"> <img src="../img/logo.png" style="width:40%;"> </div>
      <table class="table table-bordered" id="tabela_padrao" style="font-size:12px; width:98%; text-align:center;">
        <tr>
          <th>Nº</th>
          <th>Animal</th>
          <th>Tatuagem</th>
          <th>Sexo</th>
          <th>Nascimento</th>
          <th>Idade</th>
          <th>tipo</th>
        </tr>
        <?
        $inicio = $pag*50;
        $fim = $fim+50;

        if(($filtro == 'Todos') || ($filtro == 'Rebanho')){
        $animais = DBRead('animais', "WHERE status = '0' ORDER BY tatuagem desc LIMIT $inicio,50");
        }

        if($filtro == 'Sexo'){
          $sexo = $_GET['sexo'];
          $animais = DBRead('animais', "WHERE status = '0' AND sexo = '$sexo' ORDER BY tatuagem desc LIMIT $inicio,50");
        }


        foreach ($animais as $animais_){
          $y++;
          $data_atual = $animais_['data_de_nascimento'];
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

          <tr>
          <td><?=$y?></td>
          <td onclick="abrir_animal(<?=$animais_['id']?>)" style="cursor:pointer;" ><?=$animais_['nome']?></td>
          <td><?=$animais_['tatuagem']?></td>
          <td><?=$animais_['sexo']?></td>
          <td><?=$data?></td>
          <td><?=$idade_anos?> Anos <?=$idade_meses?> Meses</td>
          <td><?=$animais_['tipo']?></td>
          </tr>
        <? } ?>
        </table>

    </div>
</div>
<div id="quebra" style="margin-top:0%; height:1%;"></div>
<? $x--;  $pag++;} ?>
</body>
</html>
