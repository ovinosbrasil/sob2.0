<?
include "../../_config.php";

$user = DBRead('admin');
$id_lote = $_GET['id_lote'];
$lote = DBRead('inseminacao', "WHERE id = $id_lote");
$id_macho = $lote[0]['id_macho'];
if(!$lote[0]['terceiro']){
  $macho = DBRead('animais', "WHERE id = '$id_macho'");
}else{
  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
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
$data = $data;

?>
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

<body style="width:100%; margin-left:0%; -webkit-print-color-adjust: exact; font-family: Arial, 'Helvetica Neue'; font-size: 18px; color:#5a5a5a;" onload="window.print()">
<div id="corpo" style="margin-top:1%;">
<div id="corpo_impressao" style="text-align:center;">

	<div id="logo_arco"><img src="../../img/topo_relatorio.png"/></div>
  <div id="relatorio_ia">RELATÓRIO DE INSEMINAÇÃO ARTIFICIAL</div>
  <div id="numero_formulario">
  <table border="0">
  <tr>
    <td><strong>FORMULÁRIO N°:</strong>&nbsp;<?=$lote[0]['codigo']?></td>
    <td><strong>RAÇA:</strong>&nbsp;<?=$lote[0]['raca']?></td>
    </tr>
</table>
  <!-- FIM DO NUMERO FORMULARIO --></div>
    <div id="identificacao">
    	<table id="tabela_padrao" width="794" border="1">
  <tr>
    <td colspan="2" align="center" style="font-size:15px; font-weight:bold;">IDENTIFICAÇÃO</td>
    </tr>
  <tr>
    <td width="586"><strong>Criador:</strong>&nbsp;<?=$user[0]['responsavel']?></td>
    <td width="192"><strong>Código:</strong>&nbsp;<?=$user[0]['cod']?></td>
    </tr>
</table>
    <!--FIM DA IDENTIFICAOCA--></div>

	<div id="tecnica">
    	<table id="tabela_padrao" width="794" border="1">
  <tr>
    <td colspan="3" align="center"><strong>TÉCNICA DE INSEMINAÇÃO</strong></td>
    </tr>
  <tr>
    <td width="245"><input type="checkbox" name="checkbox" id="checkbox" />&nbsp;Inseminação Vaginal</td>
    <td width="267"><input type="checkbox" name="checkbox2" id="checkbox2" />&nbsp;Inseminação Cervical</td>
    <td width="260"><input type="checkbox" name="checkbox3" id="checkbox3" />Inseminação Por Laparoscopia***</td>
  </tr>
</table>
    <!-- FIM DA TECNICA--></div>

  <div id="carneiro">
  <table id="tabela_padrao" width="794" border="1">
  <tr>
    <td colspan="2" align="center" style="font-size:15px; font-weight:bold;">INFORMAÇÕES DO CARNEIRO DOADOR</td>
    </tr>
  <tr>
    <td width="586"><strong>*Carneiro:</strong>&nbsp;<?=$macho[0]['nome']?></td>
    <td width="192"><strong>FBB:</strong>&nbsp;<?=$macho[0]['fbb']?></td>
    </tr>
</table>
    <!-- FIM DO CARNEIRO--></div>

    <div id="tabela_femeas">
    <table id="tabela_padrao" width="794" border="1">
  <tr>
    <td width="47" rowspan="2" align="center">** TIPO Sêmen</td>
    <td width="306" rowspan="2" align="center">Ovelhas</td>
    <td width="67" rowspan="2" align="center">FBB</td>
    <td colspan="2" valign="top">****</br>( )Ia Realizada em um único dia.
    </br>( )Ia Realizada em um determinado período.</td>
    <td width="74" rowspan="2" align="center">Quantidade de doses Sêmen</td>
  </tr>
  <tr>
    <td width="139" align="center">Início</td>
    <td width="121" align="center">Fim</td>
  </tr>

  <?
  $x=1;
  $linha = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote'");
  $qtd = count($linha);
  foreach ($linha as $linha_){
    $id_femea = $linha_['id_femea'];
    if(!$linha_['terceiro']){
      $femea = DBRead('animais', "WHERE id = '$id_femea'");
    }else{
      $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
    }
  ?>
  <tr>
  <? if($lote[0]['semen'] == 'A fresco'){ ?> <td align="center">F</td> <? } ?>
  <? if($lote[0]['semen'] == 'Congelado'){?> <td align="center">C</td> <? } ?>
  <? if($lote[0]['semen'] == 'Refrigerado'){?> <td align="center">R</td> <? } ?>
    <td align="center"><?=$femea[0]['nome']?></td>
    <td align="center"><?=$femea[0]['fbb']?></td>
    <td align="center"><?=$data?></td>
    <td align="center"><?=$data?></td>
    <td align="center">1</td>
  </tr>
  <? $x++; if($x > 20){ break;} } ?>
    </table>
    <!-- FIM DA TEBELA FEMEAS--></div>


    <div id="assinaturas" style="margin-top:4%; margin-bottom:1%;">
    <table width="796" border="0">
  <tr>
    <td width="392" align="center">__________________________________</br><?=$user[0]['responsavel']?></td>
    <td width="394" align="center">__________________________________</br><?=$user[0]['tecnico']?> ***</td>
  </tr>
  <tr>
    <td align="center">Cod.:___________________</td>
    <td align="center">CRM:____________________</td>
  </tr>
    </table>
    <!-- FIM DAS ASSINATURAS--></div>

    <div id="end">
      <table width="98%" border="0">
  <tr>
    <td align="center"> Avenida 7 de Setembro, 1159 – CX Postal, 145 – Bagé /RS – Cep 96400-970 </br>
Fone: (53) 3242.8422 – Fax: (53) 3242.9522 – E-mail: registro@arcoovinos.com.br </td>
    </tr>
</table>
  <!--FIM DO END--></div>
<!-- FIM DO CORPO--></div>
</div>

<? if($qtd > 20){?>
<div id="quebra" style="margin-top:0%; height:1%;"></div>

<div id="corpo" style="margin-top:1%;">

<div id="corpo_impressao" style="text-align:center;">
  <div id="logo_arco"><img src="../../img/topo_relatorio.png"/></div>
  <div id="relatorio_ia">RELATÓRIO DE INSEMINAÇÃO ARTIFICIAL</div>
  <div id="numero_formulario">
  <table border="0">
  <tr>
    <td><strong>FORMULÁRIO N°:</strong>&nbsp;<?=$lote[0]['codigo']?>-2</td>
    <td><strong>RAÇA:</strong>&nbsp;<?=$lote[0]['raca']?></td>
    </tr>
  </table>
  <div id="identificacao">
    <table id="tabela_padrao" width="794" border="1">
<tr>
  <td colspan="2" align="center" style="font-size:15px; font-weight:bold;">IDENTIFICAÇÃO</td>
  </tr>
<tr>
  <td width="586"><strong>Criador:</strong>&nbsp;<?=$user[0]['responsavel']?></td>
  <td width="192"><strong>Código:</strong>&nbsp;<?=$user[0]['cod']?></td>
  </tr>
</table>
  <!--FIM DA IDENTIFICAOCA--></div>

<div id="tecnica">
    <table id="tabela_padrao" width="794" border="1">
<tr>
  <td colspan="3" align="center"><strong>TÉCNICA DE INSEMINAÇÃO</strong></td>
  </tr>
<tr>
  <td width="245"><input type="checkbox" name="checkbox" id="checkbox" />&nbsp;Inseminação Vaginal</td>
  <td width="267"><input type="checkbox" name="checkbox2" id="checkbox2" />&nbsp;Inseminação Cervical</td>
  <td width="260"><input type="checkbox" name="checkbox3" id="checkbox3" />Inseminação Por Laparoscopia***</td>
</tr>
</table>
  <!-- FIM DA TECNICA--></div>

<div id="carneiro">
    <table id="tabela_padrao" width="794" border="1">
<tr>
  <td colspan="2" align="center" style="font-size:15px; font-weight:bold;">INFORMAÇÕES DO CARNEIRO DOADOR</td>
  </tr>
<tr>
  <td width="586"><strong>*Carneiro:</strong>&nbsp;<?=$macho[0]['nome']?></td>
  <td width="192"><strong>FBB:</strong>&nbsp;<?=$macho[0]['fbb']?></td>
  </tr>
</table>
  <!-- FIM DO CARNEIRO--></div>

  <div id="tabela_femeas">
  <table id="tabela_padrao" width="794" border="1">
<tr>
  <td width="47" rowspan="2" align="center">** TIPO Sêmen</td>
  <td width="306" rowspan="2" align="center">Ovelhas</td>
  <td width="67" rowspan="2" align="center">FBB</td>
  <td colspan="2" valign="top">****</br>( )Ia Realizada em um único dia.
  </br>( )Ia Realizada em um determinado período.</td>
  <td width="74" rowspan="2" align="center">Quantidade de doses Sêmen</td>
</tr>
<tr>
  <td width="139" align="center">Início</td>
  <td width="121" align="center">Fim</td>
</tr>

<?
$x=1;
$linha = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote'");
foreach ($linha as $linha_){
  if($x <= 20){

  }else{
  $id_femea = $linha_['id_femea'];
  if(!$linha_['terceiro']){
    $femea = DBRead('animais', "WHERE id = '$id_femea'");
  }else{
    $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
  }
?>
<tr>
<? if($lote[0]['semen'] == 'A fresco'){?> <td align="center">F</td> <? } ?>
<? if($lote[0]['semen'] == 'Congelado'){?> <td align="center">C</td> <? } ?>
<? if($lote[0]['semen'] == 'Refrigerado'){?> <td align="center">R</td> <? } ?>
  <td align="center"><?=$femea[0]['nome']?></td>
  <td align="center"><?=$femea[0]['fbb']?></td>
  <td align="center"><?=$data?></td>
  <td align="center"><?=$data?></td>
  <td align="center">1</td>
</tr>
<? } $x++; if($x > 40){ break;} } ?>
  </table>
  <!-- FIM DA TEBELA FEMEAS--></div>


  <div id="assinaturas" style="margin-top:4%;  margin-bottom:1%;">
  <table width="796" border="0">
<tr>
  <td width="392" align="center">__________________________________</br><?=$user[0]['responsavel']?></td>
  <td width="394" align="center">__________________________________</br><?=$user[0]['tecnico']?> ***</td>
</tr>
<tr>
  <td align="center">Cod.:___________________</td>
  <td align="center">CRM:____________________</td>
</tr>
  </table>
  <!-- FIM DAS ASSINATURAS--></div>

  <div id="end">
    <table width="98%" border="0">
<tr>
  <td align="center"> Avenida 7 de Setembro, 1159 – CX Postal, 145 – Bagé /RS – Cep 96400-970 </br>
Fone: (53) 3242.8422 – Fax: (53) 3242.9522 – E-mail: registro@arcoovinos.com.br </td>
  </tr>
</table>
<!--FIM DO END--></div>
<!-- FIM DO CORPO--></div>
</div>
<? } ?>


<? if($qtd > 40){?>
<div id="quebra" style="margin-top:0%; height:1%;"></div>

<div id="corpo" style="margin-top:1%;">

<div id="corpo_impressao" style="text-align:center;">
  <div id="logo_arco"><img src="../../img/topo_relatorio.png"/></div>
  <div id="relatorio_ia">RELATÓRIO DE INSEMINAÇÃO ARTIFICIAL</div>
  <div id="numero_formulario">
  <table border="0">
  <tr>
    <td><strong>FORMULÁRIO N°:</strong>&nbsp;<?=$lote[0]['codigo']?>-3</td>
    <td><strong>RAÇA:</strong>&nbsp;<?=$lote[0]['raca']?></td>
    </tr>
  </table>
  <div id="identificacao">
    <table id="tabela_padrao" width="794" border="1">
<tr>
  <td colspan="2" align="center" style="font-size:15px; font-weight:bold;">IDENTIFICAÇÃO</td>
  </tr>
<tr>
  <td width="586"><strong>Criador:</strong>&nbsp;<?=$user[0]['responsavel']?></td>
  <td width="192"><strong>Código:</strong>&nbsp;<?=$user[0]['cod']?></td>
  </tr>
</table>
  <!--FIM DA IDENTIFICAOCA--></div>

<div id="tecnica">
    <table id="tabela_padrao" width="794" border="1">
<tr>
  <td colspan="3" align="center"><strong>TÉCNICA DE INSEMINAÇÃO</strong></td>
  </tr>
<tr>
  <td width="245"><input type="checkbox" name="checkbox" id="checkbox" />&nbsp;Inseminação Vaginal</td>
  <td width="267"><input type="checkbox" name="checkbox2" id="checkbox2" />&nbsp;Inseminação Cervical</td>
  <td width="260"><input type="checkbox" name="checkbox3" id="checkbox3" />Inseminação Por Laparoscopia***</td>
</tr>
</table>
  <!-- FIM DA TECNICA--></div>

<div id="carneiro">
    <table id="tabela_padrao" width="794" border="1">
<tr>
  <td colspan="2" align="center" style="font-size:15px; font-weight:bold;">INFORMAÇÕES DO CARNEIRO DOADOR</td>
  </tr>
<tr>
  <td width="586"><strong>*Carneiro:</strong>&nbsp;<?=$macho[0]['nome']?></td>
  <td width="192"><strong>FBB:</strong>&nbsp;<?=$macho[0]['fbb']?></td>
  </tr>
</table>
  <!-- FIM DO CARNEIRO--></div>

  <div id="tabela_femeas">
  <table id="tabela_padrao" width="794" border="1">
<tr>
  <td width="47" rowspan="2" align="center">** TIPO Sêmen</td>
  <td width="306" rowspan="2" align="center">Ovelhas</td>
  <td width="67" rowspan="2" align="center">FBB</td>
  <td colspan="2" valign="top">****</br>( )Ia Realizada em um único dia.
  </br>( )Ia Realizada em um determinado período.</td>
  <td width="74" rowspan="2" align="center">Quantidade de doses Sêmen</td>
</tr>
<tr>
  <td width="139" align="center">Início</td>
  <td width="121" align="center">Fim</td>
</tr>

<?
$x=1;
$linha = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote'");
foreach ($linha as $linha_){
  if($x <= 40){

  }else{
  $id_femea = $linha_['id_femea'];
  if(!$linha_['terceiro']){
    $femea = DBRead('animais', "WHERE id = '$id_femea'");
  }else{
    $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
  }
?>
<tr>
<? if($lote[0]['semen'] == 'A fresco'){?> <td align="center">F</td> <? } ?>
<? if($lote[0]['semen'] == 'Congelado'){?> <td align="center">C</td> <? } ?>
<? if($lote[0]['semen'] == 'Refrigerado'){?> <td align="center">R</td> <? } ?>
  <td align="center"><?=$femea[0]['nome']?></td>
  <td align="center"><?=$femea[0]['fbb']?></td>
  <td align="center"><?=$data?></td>
  <td align="center"><?=$data?></td>
  <td align="center">1</td>
</tr>
<? } $x++; if($x > 60){ break;} } ?>
  </table>
  <!-- FIM DA TEBELA FEMEAS--></div>


  <div id="assinaturas" style="margin-top:4%; margin-bottom:1%;">
  <table width="796" border="0">
<tr>
  <td width="392" align="center">__________________________________</br><?=$user[0]['responsavel']?></td>
  <td width="394" align="center">__________________________________</br><?=$user[0]['tecnico']?> ***</td>
</tr>
<tr>
  <td align="center">Cod.:___________________</td>
  <td align="center">CRM:____________________</td>
</tr>
  </table>
  <!-- FIM DAS ASSINATURAS--></div>

  <div id="end">
<table width="98%" border="0">
<tr>
  <td align="center"> Avenida 7 de Setembro, 1159 – CX Postal, 145 – Bagé /RS – Cep 96400-970 </br>
Fone: (53) 3242.8422 – Fax: (53) 3242.9522 – E-mail: registro@arcoovinos.com.br </td>
  </tr>
</table>
<!--FIM DO END--></div>
<!-- FIM DO CORPO--></div>
</div>
<? } ?>
</body>
</html>
