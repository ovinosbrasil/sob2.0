<?
include "../_config.php";
$id_animal = $_GET['id_animal'];
$animal = DBRead('animais', "WHERE id = '$id_animal'");

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

$admin = DBRead('admin');

$id_pai = $animal[0]['pai'];
if($animal[0]['terceiro_pai']){
  $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
}else{
  $pai = DBRead('animais', "WHERE id = '$id_pai'");
}

$id_mae = $animal[0]['mae'];
if($animal[0]['terceiro_mae']){
  $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
}else{
  $mae = DBRead('animais', "WHERE id = '$id_mae'");
}
$avo1 = $pai[0]['pai'];
if($pai[0]['terceiro_pai']){ $avo1 = DBRead('terceiros', "WHERE id = '$avo1'"); }else{ $avo1 = DBRead('animais', "WHERE id = '$avo1'"); }
$avo2= $pai[0]['mae'];
if($pai[0]['terceiro_mae']){ $avo2 = DBRead('terceiros', "WHERE id = '$avo2'"); }else{ $avo2 = DBRead('animais', "WHERE id = '$avo2'"); }
$avo3 = $mae[0]['pai'];
if($mae[0]['terceiro_pai']){ $avo3 = DBRead('terceiros', "WHERE id = '$avo3'"); }else{ $avo3 = DBRead('animais', "WHERE id = '$avo3'"); }
$avo4= $mae[0]['mae'];
if($mae[0]['terceiro_mae']){ $avo4 = DBRead('terceiros', "WHERE id = '$avo4'"); }else{ $avo4 = DBRead('animais', "WHERE id = '$avo4'"); }

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Relatório :: Sistema Ovinos Brasil</title>
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
}

body {
margin:0;
padding:0;
line-height: 1.4em;
}

#tabela_linha_padrao_impressao{
	height:35px;
	font-size:14px;
}

#tabela_padrao_impressao{
	border-collapse:collapse;
	border: #CCC;
  border-top: transparent;
  border-left: transparent;
}

#tabela_padrao tr th{
  background:#d9e5bc;
  text-align:center;
}

#tabela_padrao tr td{
  background:#fcfeff;
}
</style>

</head>

<body style="width:100%; margin-left:0%; -webkit-print-color-adjust: exact; font-family: Arial, 'Helvetica Neue'; font-size: 18px; color:#5a5a5a;" onload="window.print()">
  <div id="bg" style="background-image: url('../img/bg_impressao.png'); width: 100%; height: 100%; background-position: center; background-repeat: no-repeat; background-size: cover; margin:0; float:left;">
	<div id="palco_dados" style="width:90%; margin-left:5%; margin-top:4%;">
      <div id="titulo" style="width:100%;">
        <span style="font-weight:bold; Font-size:21px; color:#586439;">Dados gerais</span><br/>
      </div>

      <table class="table table-bordered" id="tabela_padrao" style="width:90%;">
        <tr>
          <td><span style="font-weight:bold;">Animal:</span> <?=$animal[0]['nome']?></td>
          <td><span style="font-weight:bold;">Fazenda:</span> <?=$admin[0]['fazenda']?></td>
        </tr>
        <tr>
          <td><span style="font-weight:bold;">Fbb:</span> <?=$animal[0]['fbb']?></td>
          <td><span style="font-weight:bold;">Responsável:</span> <?=$admin[0]['responsavel']?></td>
        </tr>
        <tr>
          <td><span style="font-weight:bold;">Sexo:</span> <?=$animal[0]['sexo']?></td>
          <td><span style="font-weight:bold;">E-mail:</span> <?=$admin[0]['email']?></td>
        </tr>
        <tr>
          <td><span style="font-weight:bold;">Nascimento:</span> <?=$data_nascimento?></td>
          <td><span style="font-weight:bold;">Celular:</span> <?=$admin[0]['celular']?></td>
        </tr>
        <tr>
          <td><span style="font-weight:bold;">Idade:</span> <?=$idade_anos?> Anos <?=$idade_meses?> Meses</td>
          <td><span style="font-weight:bold;">Cidade:</span> <?=$admin[0]['cidade']?></td>
        </tr>
        <tr>
          <td><span style="font-weight:bold;">Tipo:</span> <?=$animal[0]['tipo']?></td>
          <td><span style="font-weight:bold;">Estado:</span> <?=$admin[0]['estado']?></td>
        </tr>
      </table>

      <div id="titulo" style="float:left; width:100%; margin-top:3%;">
        <span style="font-weight:bold; Font-size:23px; color:#586439;">Pedigree</span><br/>
      </div>

      <table class="table table-bordered" id="tabela_padrao" style="width:100%;">
        <tr>
          <td><span style="font-weight:bold;">Pai:</span> <?=$pai[0]['nome']?></td>
          <td><span style="font-weight:bold;">Mãe:</span> <?=$mae[0]['nome']?></td>
        </tr>
        <tr>
          <td><span style="font-weight:bold;">Avô paterno:</span> <?=$avo1[0]['nome']?></td>
          <td><span style="font-weight:bold;">Avô materno:</span> <?=$avo2[0]['nome']?></td>
        </tr>
        <tr>
          <td><span style="font-weight:bold;">Avó paterna:</span> <?=$avo3[0]['nome']?></td>
          <td><span style="font-weight:bold;">Avó materna:</span> <?=$avo4[0]['nome']?></td>
        </tr>
      </table>

      <div id="titulo" style="float:left; width:100%; margin-top:3%;">
        <span style="font-weight:bold; Font-size:21px; color:#586439;">Lista de Vacinas</span><br/>
      </div>

      <table class="table table-bordered" id="tabela_padrao" style="width:100%; text-align:center;">
        <tr>
          <th>Vacina</td>
          <th>Data</td>
          <th>Observações</td>
        </tr>
        <?
        $vacina = DBRead('vacinas', "WHERE id_animal = '$id_animal' ORDER BY data asc");
        foreach ($vacina as $vacina_) {
          $y++;
          $id_vacina = $vacina_['id_vacina'];
          $nome_vacina = DBRead('vacina', "WHERE id = '$id_vacina'");
          $data = $vacina_['data'];
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
          $data_vacina = $data;
        ?>
        <tr style="background-color:#daecaa;">
            <td><?=$nome_vacina[0]['nome']?></td>
            <td><?=$data_vacina?></td>
            <td><?=$vacina_['obs']?></td>
          </tr>
      <? } ?>
      </table>

      <div id="titulo" style="float:left; width:100%; margin-top:3%;">
        <span style="font-weight:bold; Font-size:21px; color:#586439;">Lista de Doenças</span><br/>
      </div>

      <table class="table table-bordered" id="tabela_padrao" style="width:100%; text-align:center;">
        <tr>
          <th>Doença</td>
          <th>Data</td>
          <th>Observações</td>
        </tr>
        <?
        $doenca = DBRead('doencas', "WHERE id_animal = '$id_animal' ORDER BY data asc");
        foreach ($doenca as $doenca_) {
          $id_doenca = $doenca_['id_doenca'];
          $nome_doenca = DBRead('doenca', "WHERE id = '$id_doenca'");
          $data = $doenca_['data'];
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
          $data_doenca = $data;
        ?>
        <tr style="background-color:#daecaa;">
            <td><?=$nome_doenca[0]['nome']?></td>
            <td><?=$data_doenca?></td>
            <td><?=$doenca_['obs']?></td>
          </tr>
      <? } ?>
      </table>

      <div id="titulo" style="float:left; width:100%; margin-top:3%;">
        <span style="font-weight:bold; Font-size:21px; color:#586439;">Lista de Prêmios</span><br/>
      </div>

      <table class="table table-bordered" id="tabela_padrao" style="width:100%; text-align:center;">
        <tr>
          <th>Prêmio</td>
          <th>Exposição</td>
          <th>Data</td>
        </tr>
        <?
        $premio = DBRead('premio', "WHERE id_animal = '$id_animal'");
        foreach ($premio as $premio_) {
          $z++;
          $id_exposicao = $premio_['id_julgamento'];
          $exposicao = DBRead('julgamento', "WHERE id = '$id_exposicao'");
          $data = $exposicao[0]['data'];
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
          $data_exposicao = $data;
        ?>
        <tr style="background-color:#daecaa;">
            <td><?=$premio_['premio']?></td>
            <td><?=$exposicao[0]['nome']?> - <?=$exposicao[0]['cidade']?></td>
            <td><?=$data_exposicao?></td>
          </tr>
      <? } ?>
      </table>
</div>


<div id="quebra" style="margin-top:0%; height:1%;"></div>


<div id="bg" style="background-image: url('../img/bg_impressao.png'); width: 100%; height: 100%; background-position: center; background-repeat: no-repeat; background-size: cover; margin:0; float:left; margin-top:-1.4%;">
  <div id="palco_dados" style="width:90%; margin-left:5%; margin-top:4%;">
      <div id="titulo" style="width:100%;">
        <span style="font-weight:bold; Font-size:21px; color:#586439;">Avaliação do animal</span><br/>
      </div>
<?
$ava1 = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = 1");
$ava2 = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = 2");
if(!$ava1[0]['id']){ $ava1[0]['tipo'] = 0; }
if(!$ava2[0]['id']){ $ava2[0]['tipo'] = 0; }
?>
      <table class="table table-bordered" id="tabela_padrao" width="85%" style="text-align:center;">
        <tr>
          <th></th>
          <th>1ª Avaliação</th>
          <th>2ª Avaliação</th>
          <th>Evolução</th>
        </tr>
        <tr>
          <td>Pesagem</td>
          <td><?=$ava1[0]['tamanho']?></td>
          <td><?=$ava2[0]['tamanho']?></td>
          <td>
            <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] > $ava2[0]['tamanho'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] < $ava2[0]['tamanho'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] == $ava2[0]['tamanho'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Cabeça</td>
          <td><?=$ava1[0]['cabeca']?></td>
          <td><?=$ava2[0]['cabeca']?></td>
          <td>
            <? if(($ava1[0]['cabeca']) && ($ava2[0]['cabeca']) && ($ava1[0]['cabeca'] > $ava2[0]['cabeca'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['cabeca']) && ($ava2[0]['cabeca']) && ($ava1[0]['cabeca'] < $ava2[0]['cabeca'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['cabeca']) && ($ava2[0]['cabeca']) && ($ava1[0]['cabeca'] == $ava2[0]['cabeca'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Pescoço</td>
          <td><?=$ava1[0]['pescoco']?></td>
          <td><?=$ava2[0]['pescoco']?></td>
          <td>
            <? if(($ava1[0]['pescoco']) && ($ava2[0]['pescoco']) && ($ava1[0]['pescoco'] > $ava2[0]['pescoco'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['pescoco']) && ($ava2[0]['pescoco']) && ($ava1[0]['pescoco'] < $ava2[0]['pescoco'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['pescoco']) && ($ava2[0]['pescoco']) && ($ava1[0]['pescoco'] == $ava2[0]['pescoco'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Quarto Anterior</td>
          <td><?=$ava1[0]['quarto_anterior']?></td>
          <td><?=$ava2[0]['quarto_anterior']?></td>
          <td>
            <? if(($ava1[0]['quarto_anterior']) && ($ava2[0]['quarto_anterior']) && ($ava1[0]['quarto_anterior'] > $ava2[0]['quarto_anterior'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['quarto_anterior']) && ($ava2[0]['quarto_anterior']) && ($ava1[0]['quarto_anterior'] < $ava2[0]['quarto_anterior'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['quarto_anterior']) && ($ava2[0]['quarto_anterior']) && ($ava1[0]['quarto_anterior'] == $ava2[0]['quarto_anterior'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Barril</td>
          <td><?=$ava1[0]['barril']?></td>
          <td><?=$ava2[0]['barril']?></td>
          <td>
            <? if(($ava1[0]['barril']) && ($ava2[0]['barril']) && ($ava1[0]['barril'] > $ava2[0]['barril'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['barril']) && ($ava2[0]['barril']) && ($ava1[0]['barril'] < $ava2[0]['barril'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['barril']) && ($ava2[0]['barril']) && ($ava1[0]['barril'] == $ava2[0]['barril'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Quarto Posterior</td>
          <td><?=$ava1[0]['quarto_posterior']?></td>
          <td><?=$ava2[0]['quarto_posterior']?></td>
          <td>
            <? if(($ava1[0]['quarto_posterior']) && ($ava2[0]['quarto_posterior']) && ($ava1[0]['quarto_posterior'] > $ava2[0]['quarto_posterior'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['quarto_posterior']) && ($ava2[0]['quarto_posterior']) && ($ava1[0]['quarto_posterior'] < $ava2[0]['quarto_posterior'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['quarto_posterior']) && ($ava2[0]['quarto_posterior']) && ($ava1[0]['quarto_posterior'] == $ava2[0]['quarto_posterior'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Comprimento</td>
          <td><?=$ava1[0]['comprimento']?></td>
          <td><?=$ava2[0]['comprimento']?></td>
          <td>
            <? if(($ava1[0]['comprimento']) && ($ava2[0]['comprimento']) && ($ava1[0]['comprimento'] > $ava2[0]['comprimento'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['comprimento']) && ($ava2[0]['comprimento']) && ($ava1[0]['comprimento'] < $ava2[0]['comprimento'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['comprimento']) && ($ava2[0]['comprimento']) && ($ava1[0]['comprimento'] == $ava2[0]['comprimento'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Orgão Sexual</td>
          <td><?=$ava1[0]['orgao']?></td>
          <td><?=$ava2[0]['orgao']?></td>
          <td>
            <? if(($ava1[0]['orgao']) && ($ava2[0]['orgao']) && ($ava1[0]['orgao'] > $ava2[0]['orgao'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['orgao']) && ($ava2[0]['orgao']) && ($ava1[0]['orgao'] < $ava2[0]['orgao'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['orgao']) && ($ava2[0]['orgao']) && ($ava1[0]['orgao'] == $ava2[0]['orgao'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Conformação</td>
          <td><?=$ava1[0]['conformacao']?></td>
          <td><?=$ava2[0]['conformacao']?></td>
          <td>
            <? if(($ava1[0]['conformacao']) && ($ava2[0]['conformacao']) && ($ava1[0]['conformacao'] > $ava2[0]['conformacao'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['conformacao']) && ($ava2[0]['conformacao']) && ($ava1[0]['conformacao'] < $ava2[0]['conformacao'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['conformacao']) && ($ava2[0]['conformacao']) && ($ava1[0]['conformacao'] == $ava2[0]['conformacao'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Tamanho</td>
          <td><?=$ava1[0]['tamanho']?></td>
          <td><?=$ava2[0]['tamanho']?></td>
          <td>
            <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] > $ava2[0]['tamanho'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] < $ava2[0]['tamanho'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] == $ava2[0]['tamanho'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Distribuição</td>
          <td><?=$ava1[0]['distribuicao']?></td>
          <td><?=$ava2[0]['distribuicao']?></td>
          <td>
            <? if(($ava1[0]['distribuicao']) && ($ava2[0]['distribuicao']) && ($ava1[0]['distribuicao'] > $ava2[0]['distribuicao'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['distribuicao']) && ($ava2[0]['distribuicao']) && ($ava1[0]['distribuicao'] < $ava2[0]['distribuicao'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['distribuicao']) && ($ava2[0]['distribuicao']) && ($ava1[0]['distribuicao'] == $ava2[0]['distribuicao'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Cobertura</td>
          <td><?=$ava1[0]['cobertura']?></td>
          <td><?=$ava2[0]['cobertura']?></td>
          <td>
            <? if(($ava1[0]['cobertura']) && ($ava2[0]['cobertura']) && ($ava1[0]['cobertura'] > $ava2[0]['cobertura'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['cobertura']) && ($ava2[0]['cobertura']) && ($ava1[0]['cobertura'] < $ava2[0]['cobertura'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['cobertura']) && ($ava2[0]['cobertura']) && ($ava1[0]['cobertura'] == $ava2[0]['cobertura'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <td>Cor</td>
          <td><?=$ava1[0]['cor']?></td>
          <td><?=$ava2[0]['cor']?></td>
          <td>
            <? if(($ava1[0]['cor']) && ($ava2[0]['cor']) && ($ava1[0]['cor'] > $ava2[0]['cor'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['cor']) && ($ava2[0]['cor']) && ($ava1[0]['cor'] < $ava2[0]['cor'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['cor']) && ($ava2[0]['cor']) && ($ava1[0]['cor'] == $ava2[0]['cor'])){ ?> <span>Manteve</span> <? } ?>
          </td>
        </tr>
        <tr>
          <th>Resultado</th>
          <th><? if($ava1[0]['id']>0){ ?> Tipo <?=$ava1[0]['tipo']; } ?></th>
          <th><? if($ava2[0]['id']>0){ ?> Tipo <?=$ava2[0]['tipo']; } ?></th>
          <th><? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] > $ava2[0]['tipo'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
          <? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] < $ava2[0]['tipo'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
          <? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] == $ava2[0]['tipo'])){ ?> <span>Manteve</span> <? } ?></th>
        </tr>
        </table>

      <div id="titulo" style="float:left; width:100%; margin-top:3%;">
        <span style="font-weight:bold; Font-size:23px; color:#586439;">Crias do animal</span><br/>
      </div>

      <table class="table table-bordered" id="tabela_padrao" width="98%" style="text-align:center;">
        <tr>
          <th>Lote</th>
          <th>Animal</th>
          <th>Nascimento</th>
          <th>Sexo</th>
          <th>Tipo</th>
        </tr>

          <?
          $x=0;
          $cria = DBRead('animais', "WHERE mae = '$id_animal' AND terceiro_mae = '0' ORDER BY data_de_nascimento asc");
          $data_antiga = '';
          foreach ($cria as $crias){
            $x++;
            $id_cria = $crias['id'];
            $cria_ = DBRead('animais', "WHERE id = '$id_cria'");
            $data = $cria_[0]['data_de_nascimento'];
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
          ?>

          <tr>
          <td>
            <? if($cria_[0]['tipo_reproducao'] == 'Embrionagem'){?> T.E <? } ?>
            <? if($cria_[0]['tipo_reproducao'] == 'Monta Natural'){?> M.N <? } ?>
            <? if($cria_[0]['tipo_reproducao'] == 'Inseminação Artificial'){?> I.A <? } ?>
        </td>
          <td><?=$cria_[0]['nome']?></td>
          <td><?=$data_nascimento?></td>
          <td><?=$cria_[0]['sexo']?></td>
          <td><?=$cria_[0]['tipo']?></td>
        </tr>
        <? } ?>
        </table>
</div>
</div>

</body>
</html>
