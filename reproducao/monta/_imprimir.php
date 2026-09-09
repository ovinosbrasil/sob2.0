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
$lote = DBRead('monta', "WHERE id = $id_lote");
$id_macho = $lote[0]['id_animal'];
if(!$lote[0]['terceiro']){
  $macho = DBRead('animais', "WHERE id = '$id_macho'");
}else{
  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
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
$data_inicial = $data;

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
$data_final = $data;

?>

<body style="width:100%; margin-left:0%; -webkit-print-color-adjust: exact; font-family: Arial, 'Helvetica Neue'; font-size: 18px; color:#5a5a5a;" onload="window.print()">
  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_topo" style="text-align:center; width:100%;">
      <div id="topo" style="height:115px;"><img src="../../img/topo_relatorio.png" /><br/></div>
      <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
      <div id="topo3" style=" font-size:24px;"> NOTIFICAÇÃO DE COBERTURA <samp style="color:#F06;">
      <? if($lote[0]['notificacao'] == 'PO'){ ?>
      PO( x ) PC(  )
      <? }else{ ?>
      PO( ) PC( x )
      <? } ?>
      </samp></div>
    </div>

    <div id="corpo_dados">
      <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
      	<li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$lote[0]['codigo']?></span></li>
          <li style="float:left; margin-right:45px;">CRIADOR: <span style="font-weight:100;"><?=$user[0]['responsavel']?></span></li>
          <li>COD: <span style="font-weight:100;"><?=$user[0]['cod']?></span></li>
          <li>END: <span style="font-weight:100;"><?=$user[0]['end']?> <?=$user[0]['num']?></span></li>
          <li style="float:left; margin-right:45px;">CIDADE: <span style="font-weight:100;"> <?=$user[0]['cidade']?> - <?=$user[0]['estado']?></span>
      </li><li>CEP: <span style="font-weight:100;"><?=$user[0]['cep']?> </span></li>
          <li>ESTABELECIMENTO: <span style="font-weight:100;"><?=$user[0]['fazenda']?> </span></li>
          <li style="float:left; margin-right:45px;">TELEFONE: <span style="font-weight:100;"><?=$user[0]['telefone']?></span></li>
          <li>CELULAR: <span style="font-weight:100;"><span style="font-weight:100;"><?=$user[0]['celular']?></span></li>
          <li style="float:left; margin-right:45px;">MUNICÍPIO: <span style="font-weight:100;"><?=$user[0]['municipio']?></span></li>
        <li style="margin-bottom:10px;">TÉCNICO: <span style="font-weight:100;"><?=$user[0]['tecnico']?></span></li>
          <li style="color:#F06;"> OBS.: É INDISPENSÁVEL À INDICAÇÃO DO N° DE REGISTRO (FBB) DO CARNEIRO E DAS OVELHAS.</li>
        <li style="margin-top:10px;font-weight: bold;">RAÇA: <span style="font-weight:100;"><?=$lote[0]['raca']?></span></li>
          <li style="float:left;">CARNEIRO PAI:<span style="font-weight:100;">
          <? $lote['macho'] = strtoupper($lote['macho']); echo $macho[0]['nome']?></span></li><br />
          <li>TAT: <span style="font-weight:100;"><?=$macho[0]['tatuagem']?></span></li>
          <li>REG: <span style="font-weight:100;"><?=$macho[0]['fbb']?></span></li>
        <li>PERÍODO/COB: <span style="font-weight:100;"><?=$data_inicial?> à <?=$data_final?></span></li>
        <li style="margin-top:10px; font-weight: bold;"> MONTA NATURAL</li>
      </ul>
    </div>

    <div id="corpo_tabelas">
      <table class="table table-bordered" id="tabela_padrao" border="1" style="width:40%; margin-left:5%; float:left; text-align:center;">
        <tr>
          <th colspan="3"><span style="font-weight:bold;">Relação das ovelhas</th>
        </tr>
        <tr>
          <th><span style="font-weight:bold;">Nome</th>
          <th><span style="font-weight:bold;">Tat</th>
          <th><span style="font-weight:bold;">FBB</th>
        </tr>
        <?
        $monta_controle = DBRead('monta_controle', "WHERE id_monta = '$id_lote'");
        $qtd = count($monta_controle);
        $x=1;
        foreach ($monta_controle as $monta_controle_) {
          $id_animal = $monta_controle_['id_animal'];
          if(!$monta_controle_['terceiro']){
            $femea = DBRead('animais', "WHERE id = '$id_animal'");
          }else{
            $femea = DBRead('terceiros', "WHERE id = '$id_animal'");
          }
        ?>
        <tr>
          <td><?=$femea[0]['nome']?></td>
          <td><?=$femea[0]['tatuagem']?></td>
          <td><?=$femea[0]['fbb']?></td>
        </tr>
      <? $x++; if($x > 15){ break;} } ?>
      </table>

      <table class="table table-bordered" id="tabela_padrao" border="1" style="width:40%; margin-left:5%; float:left;  text-align:center;">
        <tr>
          <th colspan="3"><span style="font-weight:bold;">Relação das ovelhas</th>
        </tr>
        <tr>
          <th><span style="font-weight:bold;">Nome</th>
          <th><span style="font-weight:bold;">Tat</th>
          <th><span style="font-weight:bold;">FBB</th>
        </tr>
        <?
        $x=1;
        foreach ($monta_controle as $monta_controle_) {
          if($x <= 15){

          }else{
          $id_animal = $monta_controle_['id_animal'];
          if(!$monta_controle_['terceiro']){
            $femea = DBRead('animais', "WHERE id = '$id_animal'");
          }else{
            $femea = DBRead('terceiros', "WHERE id = '$id_animal'");
          }
        ?>
        <tr>
          <td><?=$femea[0]['nome']?></td>
          <td><?=$femea[0]['tatuagem']?></td>
          <td><?=$femea[0]['fbb']?></td>
        </tr>
      <? } $x++;  if($x > 30){ break; } } ?>
      </table>
    </div>
</div>


<div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
    <li>DATA: <? echo date('d/m/Y');?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ASSINATURA: __________________________________</li>
    <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
    <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
  </ul>
</div>

<? if($qtd > 30){?>
<div id="quebra" style="margin-top:0%; height:1%;"></div>

<div id="corpo" style="margin-top:1%;">
  <div id="corpo_topo" style="text-align:center; width:100%;">
    <div id="topo" style="height:115px;"><img src="../../img/topo_relatorio.png" /><br/></div>
    <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
    <div id="topo3" style=" font-size:24px;"> NOTIFICAÇÃO DE COBERTURA <samp style="color:#F06;">
    <? if($lote[0]['notificacao'] == 'PO'){ ?>
    PO( x ) PC(  )
    <? }else{ ?>
    PO( ) PC( x )
    <? } ?>
    </samp></div>
  </div>

  <div id="corpo_dados">
    <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
      <li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$lote[0]['codigo']?>-2</span></li>
        <li style="float:left; margin-right:45px;">CRIADOR: <span style="font-weight:100;"><?=$user[0]['responsavel']?></span></li>
        <li>COD: <span style="font-weight:100;"><?=$user[0]['cod']?></span></li>
        <li>END: <span style="font-weight:100;"><?=$user[0]['end']?> <?=$user[0]['num']?></span></li>
        <li style="float:left; margin-right:45px;">CIDADE: <span style="font-weight:100;"> <?=$user[0]['cidade']?> - <?=$user[0]['estado']?></span>
    </li><li>CEP: <span style="font-weight:100;"><?=$user[0]['cep']?> </span></li>
        <li>ESTABELECIMENTO: <span style="font-weight:100;"><?=$user[0]['fazenda']?> </span></li>
        <li style="float:left; margin-right:45px;">TELEFONE: <span style="font-weight:100;"><?=$user[0]['telefone']?></span></li>
        <li>CELULAR: <span style="font-weight:100;"><span style="font-weight:100;"><?=$user[0]['celular']?></span></li>
        <li style="float:left; margin-right:45px;">MUNICÍPIO: <span style="font-weight:100;"><?=$user[0]['municipio']?></span></li>
      <li style="margin-bottom:10px;">TÉCNICO: <span style="font-weight:100;"><?=$user[0]['tecnico']?></span></li>
        <li style="color:#F06;"> OBS.: É INDISPENSÁVEL À INDICAÇÃO DO N° DE REGISTRO (FBB) DO CARNEIRO E DAS OVELHAS.</li>
      <li style="margin-top:10px;font-weight: bold;">RAÇA: <span style="font-weight:100;"><?=$lote[0]['raca']?></span></li>
        <li style="float:left;">CARNEIRO PAI:<span style="font-weight:100;">
        <? $lote['macho'] = strtoupper($lote['macho']); echo $macho[0]['nome']?></span></li><br />
        <li>TAT: <span style="font-weight:100;"><?=$macho[0]['tatuagem']?></span></li>
        <li>REG: <span style="font-weight:100;"><?=$macho[0]['fbb']?></span></li>
      <li>PERÍODO/COB: <span style="font-weight:100;"><?=$data_inicial?> à <?=$data_final?></span></li>
      <li style="margin-top:10px; font-weight: bold;"> MONTA NATURAL</li>
    </ul>
  </div>

  <div id="corpo_tabelas">
    <table class="table table-bordered" id="tabela_padrao" border="1" style="width:40%; margin-left:5%; float:left; text-align:center;">
      <tr>
        <th colspan="3"><span style="font-weight:bold;">Relação das ovelhas</th>
      </tr>
      <tr>
        <th><span style="font-weight:bold;">Nome</th>
        <th><span style="font-weight:bold;">Tat</th>
        <th><span style="font-weight:bold;">FBB</th>
      </tr>
      <?
      $x=1;
      foreach ($monta_controle as $monta_controle_) {
        if($x <= 30){

        }else{
        $id_animal = $monta_controle_['id_animal'];
        if(!$monta_controle_['terceiro']){
          $femea = DBRead('animais', "WHERE id = '$id_animal'");
        }else{
          $femea = DBRead('terceiros', "WHERE id = '$id_animal'");
        }
      ?>
      <tr>
        <td><?=$femea[0]['nome']?></td>
        <td><?=$femea[0]['tatuagem']?></td>
        <td><?=$femea[0]['fbb']?></td>
      </tr>
    <? } $x++; if($x > 45){ break;} } ?>
    </table>

    <table class="table table-bordered" id="tabela_padrao" border="1" style="width:40%; margin-left:5%; float:left; text-align:center;">
      <tr>
        <th colspan="3"><span style="font-weight:bold;">Relação das ovelhas</th>
      </tr>
      <tr>
        <th><span style="font-weight:bold;">Nome</th>
        <th><span style="font-weight:bold;">Tat</th>
        <th><span style="font-weight:bold;">FBB</th>
      </tr>
      <?
      $x=1;
      foreach ($monta_controle as $monta_controle_) {
        if($x <= 45){

        }else{
        $id_animal = $monta_controle_['id_animal'];
        if(!$monta_controle_['terceiro']){
          $femea = DBRead('animais', "WHERE id = '$id_animal'");
        }else{
          $femea = DBRead('terceiros', "WHERE id = '$id_animal'");
        }
      ?>
      <tr>
        <td><?=$femea[0]['nome']?></td>
        <td><?=$femea[0]['tatuagem']?></td>
        <td><?=$femea[0]['fbb']?></td>
      </tr>
    <? } $x++; } ?>
    </table>
  </div>
</div>

<div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%;">
<ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
  <li>DATA: <? echo date('d/m/Y');?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ASSINATURA: __________________________________</li>
  <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
  <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
</ul>
</div>

<? } ?>


<? if($qtd > 60){?>
<div id="quebra" style="margin-top:0%; height:1%;"></div>

<div id="corpo" style="margin-top:1%;">
  <div id="corpo_topo" style="text-align:center; width:100%;">
    <div id="topo" style="height:115px;"><img src="../../img/topo_relatorio.png" /><br/></div>
    <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
    <div id="topo3" style=" font-size:24px;"> NOTIFICAÇÃO DE COBERTURA <samp style="color:#F06;">
    <? if($lote[0]['notificacao'] == 'PO'){ ?>
    PO( x ) PC(  )
    <? }else{ ?>
    PO( ) PC( x )
    <? } ?>
    </samp></div>
  </div>

  <div id="corpo_dados">
    <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
      <li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$lote[0]['codigo']?>-3</span></li>
        <li style="float:left; margin-right:45px;">CRIADOR: <span style="font-weight:100;"><?=$user[0]['responsavel']?></span></li>
        <li>COD: <span style="font-weight:100;"><?=$user[0]['cod']?></span></li>
        <li>END: <span style="font-weight:100;"><?=$user[0]['end']?> <?=$user[0]['num']?></span></li>
        <li style="float:left; margin-right:45px;">CIDADE: <span style="font-weight:100;"> <?=$user[0]['cidade']?> - <?=$user[0]['estado']?></span>
    </li><li>CEP: <span style="font-weight:100;"><?=$user[0]['cep']?> </span></li>
        <li>ESTABELECIMENTO: <span style="font-weight:100;"><?=$user[0]['fazenda']?> </span></li>
        <li style="float:left; margin-right:45px;">TELEFONE: <span style="font-weight:100;"><?=$user[0]['telefone']?></span></li>
        <li>CELULAR: <span style="font-weight:100;"><span style="font-weight:100;"><?=$user[0]['celular']?></span></li>
        <li style="float:left; margin-right:45px;">MUNICÍPIO: <span style="font-weight:100;"><?=$user[0]['municipio']?></span></li>
      <li style="margin-bottom:10px;">TÉCNICO: <span style="font-weight:100;"><?=$user[0]['tecnico']?></span></li>
        <li style="color:#F06;"> OBS.: É INDISPENSÁVEL À INDICAÇÃO DO N° DE REGISTRO (FBB) DO CARNEIRO E DAS OVELHAS.</li>
      <li style="margin-top:10px;font-weight: bold;">RAÇA: <span style="font-weight:100;"><?=$lote[0]['raca']?></span></li>
        <li style="float:left;">CARNEIRO PAI:<span style="font-weight:100;">
        <? $lote['macho'] = strtoupper($lote['macho']); echo $macho[0]['nome']?></span></li><br />
        <li>TAT: <span style="font-weight:100;"><?=$macho[0]['tatuagem']?></span></li>
        <li>REG: <span style="font-weight:100;"><?=$macho[0]['fbb']?></span></li>
      <li>PERÍODO/COB: <span style="font-weight:100;"><?=$data_inicial?> à <?=$data_final?></span></li>
      <li style="margin-top:10px; font-weight: bold;"> MONTA NATURAL</li>
    </ul>
  </div>

  <div id="corpo_tabelas">
    <table class="table table-bordered" id="tabela_padrao" border="1" style="width:40%; margin-left:5%; float:left; text-align:center;">
      <tr>
        <th colspan="3"><span style="font-weight:bold;">Relação das ovelhas</th>
      </tr>
      <tr>
        <th><span style="font-weight:bold;">Nome</th>
        <th><span style="font-weight:bold;">Tat</th>
        <th><span style="font-weight:bold;">FBB</th>
      </tr>
      <?
      $x=1;
      foreach ($monta_controle as $monta_controle_) {
        if($x <= 60){

        }else{
        $id_animal = $monta_controle_['id_animal'];
        if(!$monta_controle_['terceiro']){
          $femea = DBRead('animais', "WHERE id = '$id_animal'");
        }else{
          $femea = DBRead('terceiros', "WHERE id = '$id_animal'");
        }
      ?>
      <tr>
        <td><?=$femea[0]['nome']?></td>
        <td><?=$femea[0]['tatuagem']?></td>
        <td><?=$femea[0]['fbb']?></td>
      </tr>
    <? } $x++; if($x > 75){ break;} } ?>
    </table>

    <table class="table table-bordered" id="tabela_padrao" border="1" style="width:40%; margin-left:5%; float:left; text-align:center;">
      <tr>
        <th colspan="3"><span style="font-weight:bold;">Relação das ovelhas</th>
      </tr>
      <tr>
        <th><span style="font-weight:bold;">Nome</th>
        <th><span style="font-weight:bold;">Tat</th>
        <th><span style="font-weight:bold;">FBB</th>
      </tr>
      <?
      $x=1;
      foreach ($monta_controle as $monta_controle_) {
        if($x <= 75){

        }else{
        $id_animal = $monta_controle_['id_animal'];
        if(!$monta_controle_['terceiro']){
          $femea = DBRead('animais', "WHERE id = '$id_animal'");
        }else{
          $femea = DBRead('terceiros', "WHERE id = '$id_animal'");
        }
      ?>
      <tr>
        <td><?=$femea[0]['nome']?></td>
        <td><?=$femea[0]['tatuagem']?></td>
        <td><?=$femea[0]['fbb']?></td>
      </tr>
    <? } $x++; } ?>
    </table>
  </div>
</div>

<div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%;">
<ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
  <li>DATA: <? echo date('d/m/Y');?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ASSINATURA: __________________________________</li>
  <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
  <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
</ul>
</div>

<? } ?>
</body>
</html>
