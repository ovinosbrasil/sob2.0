<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Relatório reprodução -  Sistema Ovinos Brasil</title>
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
include "../../_config.php";

$user = DBRead('admin');
$id_lote = $_GET['id_lote'];
$lote = DBRead('transplante', "WHERE id = $id_lote");
$id_macho = $lote[0]['id_pai'];
if(!$lote[0]['terceiro_pai']){
  $macho = DBRead('animais', "WHERE id = '$id_macho'");
}else{
  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
}

$id_mae = $lote[0]['id_mae'];
if(!$lote[0]['terceiro_mae']){
  $femea = DBRead('animais', "WHERE id = '$id_mae'");
}else{
  $femea = DBRead('terceiros', "WHERE id = '$id_mae'");
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
$data_inicial = $data;

$data = $lote[0]['data_coleta'];
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
$data_coleta = $data;


?>

<body style="width:100%; margin-left:0%; -webkit-print-color-adjust: exact; font-family: Arial, 'Helvetica Neue'; font-size: 18px; color:#5a5a5a;" onload="window.print()">
  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_topo" style="text-align:center; width:100%;">
      <div id="topo" style="height:115px;"><img src="../../img/topo_relatorio.png" /><br/></div>
      <div id="topo2" style="">RELATÓRIO DE COLHEITA E <br /></div>
      <div id="topo3"> TRANSFERÊNCIA DE EMBRIÕES OU FIV</div>
    </div>

    <div id="corpo_dados">
      <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
      	<li>FORMULÁRIO Nº:<span style="font-weight:100;"><?=$lote[0]['codigo']?></span></li>
        <li style="margin-bottom:10px;">Raça:<span style="font-weight:100;"><?=$lote[0]['raca']?></span></li>

        <div id="corpo_tabelas">
          <table class="table table-bordered" id="tabela_padrao" border="1" style="width:95%; margin-left:0%; float:left;  font-size:12px;">
            <tr>
              <th colspan="2"><span style="font-weight:bold;">Identificação</span></th>
            </tr>
            <tr>
              <th>Criador: <span style="font-weight:100;"><?=$user[0]['responsavel']?></span></th>
              <th>Código:<span style="font-weight:100;"><?=$user[0]['cod']?></span> </th>
            </tr>
          </table>

          <table class="table table-bordered" id="tabela_padrao" border="1" style="width:95%; margin-top:2%; float:left;  font-size:12px;">
            <tr>
              <th colspan="4"><span style="font-weight:bold;">RELATÓRIO DE TRANSFERÊNCIA DE EMBRIÕES</span></th>
            </tr>
            <tr>
              <th rowspan="2">Cobertura</span></th>
              <th>Monta Natural</th>
              <th>Inseminação Artificial</th>
              <th>Doses de Sêmen:</th>
            </tr>
            <tr>
              <th>Data:</th>
              <th>Data:<span style="font-weight:100;"><?=$data_inicial?></span> </th>
              <th><?=$lote[0]['tipo_semen']?></th>
            </tr>
        </table>

          <table class="table table-bordered" id="tabela_padrao" border="1" style="width:95%; margin-top:1%; float:left;  font-size:12px;">
            <tr>
              <th rowspan="2">Identificação</span></th>
              <th colspan="2" style="text-align:left;">Doadora: <span style="font-weight:100;"><?=$femea[0]['nome']?></span></th>
              <th>FBB:<span style="font-weight:100;"><?=$femea[0]['fbb']?></span></th>
            </tr>
            <tr>
              <th colspan="2" style="text-align:left;">Doador: <span style="font-weight:100;"><?=$macho[0]['nome']?></span></th>
              <th>FBB:<span style="font-weight:100;"><?=$macho[0]['fbb']?></span></th>
            </tr>
          </table>

          <table class="table table-bordered" id="tabela_padrao" border="1" style="width:95%; margin-top:1%; float:left;  font-size:12px;">
            <tr>
              <th>Embriões</th>
              <th>Coletados: <span style="font-weight:100;"><?=$lote[0]['qtd']?></span></th>
              <th>Congelados: <span style="font-weight:100;"><?=$lote[0]['congelados']?></span></th>
              <th>Usados: <span style="font-weight:100;"><?=$lote[0]['usados']?></span></th>
              <th>Data Colheita: <span style="font-weight:100;"><?=$data_coleta?></span></th>
            </tr>
          </table>


          <table class="table table-bordered" id="tabela_padrao" border="1" style="width:95%; margin-top:1%; margin-bottom:4%; float:left;  font-size:12px;">
            <tr>
              <th colspan="8">TRANSFERÊNCIA DE EMBRIÕES</th>
            </tr>
            <tr>
              <th colspan="8" style="text-align:left;">Data Transferência: <span style="font-weight:100;"><?=$data_coleta?></span></th>
            </tr>
            <tr>
              <th><span style="font-weight:bold;">Receptora</th>
              <th><span style="font-weight:bold;">Nº Embriões</th>
              <th><span style="font-weight:bold;">Receptora</th>
              <th><span style="font-weight:bold;">Nº. Embriões</th>
              <th><span style="font-weight:bold;">Receptora</th>
              <th><span style="font-weight:bold;">Nº. Embriões</th>
              <th><span style="font-weight:bold;">Receptora</th>
              <th><span style="font-weight:bold;">Nº. Embriões</th>
            </tr>
            <?
            $x=0;
            $receptoras = DBRead('transplante_controle', "WHERE id_lote = '$id_lote'");
            $linha = 1;
            while($linha < 9){ ?>
            <tr>
              <?
              $coluna = 1;
              while($coluna < 5){ ?>
                <th><span style="font-weight:100;"><? if($receptoras[$x]['id'] > 0){ echo $receptoras[$x]['receptora']; }else{?> &nbsp; <? } ?></th>
                <th><span style="font-weight:100;"><?=$receptoras[$x]['n_embrioes']?></th>
              <? $coluna++; $x++;} ?>
            </tr>
          <? $linha++; } ?>
          </table>
        </div>

        <li style="color:red; font-weight:100;">OBSERVAÇÃO</li>
        <li style="color:red; font-weight:100;">- Preencher um Formulário por colheita;</li>
        <li style="color:red; font-weight:100;">- A numeração deverá começar em 0001e seguir o modelo numeração/ano. Exemplos: 0001/2008; 0002/2008; 0003/2008</li>
        <li style="color:red; font-weight:100;">- Não aceitamos cópias, somente formulários originais e devidamente assinados pelo Médico Veterinário e Criador.</li>

      </ul>
    </div>


</div>


<div id="assinatura" style="text-align:center; margin-top:4%; float:left; width:40%; margin-left:5%;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px; text-align:center;">
    <li>__________________________________<br><?=$user[0]['responsavel']?> <br/><?=$user[0]['cod']?></li>
  </ul>
</div>

<div id="assinatura" style="text-align:center; margin-top:4%; float:left; width:40%; margin-left:5%;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px; text-align:center;">
    <li>__________________________________<br><?=$user[0]['tecnico']?> <br/> <?=$user[0]['cod_tecnico']?></li>
  </ul>
</div>

<div id="assinatura" style="text-align:center; margin-top:4%; float:left; width:90%;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px; text-align:center;">
      <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
      <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
  </ul>
</div>

</body>
</html>
