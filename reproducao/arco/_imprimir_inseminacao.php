<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Relatório Arco -  Sistema Ovinos Brasil</title>
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
$date = date("d/m/Y");
$numeracao = $_GET['numeracao'];
$data_inicial = $_GET['data_inicial'];
$data_final = $_GET['data_final'];
$raca = $_GET['raca'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Imprimir Relatório de Monta Natural</title>
<style type="text/css">
#tabela_borda{
	border-collapse: collapse;
}

#tabela_borda td{
		border-collapse: collapse;
}
</style>
</head>

<body style="width:100%; margin-left:0%; -webkit-print-color-adjust: exact; font-family: Arial, 'Helvetica Neue'; font-size: 14px; color:#5a5a5a;" onload="window.print()">
  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_topo" style="text-align:center; width:100%;">
      <div id="topo" style="height:80px;"><img src="../../img/topo_relatorio.png" height="80"/><br/></div>
      <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
      <samp style="color:#F06;"><div id="topo3" style=" font-size:20px;"> NOTIFICAÇÃO DE NASCIMENTO</div></samp>
    </div>

    <div id="corpo_dados">
      <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
      	<li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$numeracao?>-1</span></li>
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
          <li style="float:left; margin-right:150px;">Raça: <span style="font-weight:bold;"> <?=$raca?></span></li>
          <li>Categoria: <span style="font-weight:bold;">PO(X) PC( )</span></li>
          <li>Monta Natural( ) &nbsp Transplante de Embriões( ) &nbsp Inseminação Artificial(X)  &nbsp Sêmen Congelado( )  &nbsp Sêmen a Fresco( )</li>
      </ul>
    </div>


  <div id="imprimir" style="margin-bottom:10px;">
  <table class="table table-bordered" id="tabela_padrao" border="1" style="width:98%; margin-left:1%; text-align:center; font-size:12px;">
      <tr>
        <th>Fbb</th>
        <th>Nome</th>
        <th>Tat.</th>
        <th>Sexo</th>
        <th>Nascimento</th>
        <th>COD.</th>
        <th>Pai</th>
        <th>Fbb</th>
        <th>Mãe</th>
        <th>Fbb</th>
      </tr>
      <?
      $animal = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial' AND data_de_nascimento <= '$data_final' AND tipo_reproducao = 'Inseminação artificial' ORDER BY data_de_nascimento asc");
      $qtd = count($animal);
      $x=1;
      foreach ($animal as $animais) {
      $data = $animais['data_de_nascimento'];
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

      $id_pai = $animais['pai'];
      if($animais['terceiro_pai']){
        $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
      }else{
        $pai = DBRead('animais', "WHERE id = '$id_pai'");
      }

      $id_mae = $animais['mae'];
      if($animais['terceiro_mae']){
        $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
      }else{
        $mae = DBRead('animais', "WHERE id = '$id_mae'");
      }
      ?>
        <tr>
            <td><?=$animais['fbb']?></td>
            <td><?=$animais['nome']?></td>
            <td><?=$animais['tatuagem']?></td>
            <td><?=$animais['sexo']?></td>
            <td><?=$data?></td>
            <td><? if($animais['status'] == '1'){ echo "Óbito"; } ?></td>
            <td><?=$pai[0]['nome']?></td>
            <td><?=$pai[0]['fbb']?></td>
            <td><?=$mae[0]['nome']?></td>
            <td><?=$mae[0]['fbb']?></td>
          </tr>
        <? $x++; if($x > 15){ break; } } ?>
        </table>
</div>

<div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%; font-size:10px;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase ">
    <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
    <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
  </ul>
</div>

<? if($qtd > 15){ ?>

<div id="quebra" style="margin-top:0%; height:1%;"></div>

<div id="corpo" style="margin-top:1%;">
  <div id="corpo_topo" style="text-align:center; width:100%;">
    <div id="topo" style="height:80px;"><img src="../../img/topo_relatorio.png" height="80"/><br/></div>
    <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
    <samp style="color:#F06;"><div id="topo3" style=" font-size:20px;"> NOTIFICAÇÃO DE NASCIMENTO</div></samp>
  </div>

  <div id="corpo_dados">
    <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
      <li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$numeracao?>-2</span></li>
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
        <li style="float:left; margin-right:150px;">Raça: <span style="font-weight:bold;"> <?=$raca?></span></li>
        <li>Categoria: <span style="font-weight:bold;">PO(X) PC( )</span></li>
        <li>Monta Natural( ) &nbsp Transplante de Embriões( ) &nbsp Inseminação Artificial(X)  &nbsp Sêmen Congelado( )  &nbsp Sêmen a Fresco( )</li>
    </ul>
  </div>


<div id="imprimir" style="margin-bottom:10px;">
<table class="table table-bordered" id="tabela_padrao" border="1" style="width:98%; margin-left:1%; text-align:center; font-size:12px;">
    <tr>
      <th>Fbb</th>
      <th>Nome</th>
      <th>Tat.</th>
      <th>Sexo</th>
      <th>Nascimento</th>
      <th>COD.</th>
      <th>Pai</th>
      <th>Fbb</th>
      <th>Mãe</th>
      <th>Fbb</th>
    </tr>
    <?
    $x=1;
    $animal = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial' AND data_de_nascimento <= '$data_final' AND tipo_reproducao = 'Inseminação artificial' ORDER BY data_de_nascimento asc");
    foreach ($animal as $animais) {
    if($x <= 15){
      $x++;
    }else{
    $data = $animais['data_de_nascimento'];
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

    $id_pai = $animais['pai'];
    if($animais['terceiro_pai']){
      $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
    }else{
      $pai = DBRead('animais', "WHERE id = '$id_pai'");
    }

    $id_mae = $animais['mae'];
    if($animais['terceiro_mae']){
      $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
    }else{
      $mae = DBRead('animais', "WHERE id = '$id_mae'");
    }
    ?>
      <tr>
          <td><?=$animais['fbb']?></td>
          <td><?=$animais['nome']?></td>
          <td><?=$animais['tatuagem']?></td>
          <td><?=$animais['sexo']?></td>
          <td><?=$data?></td>
          <td><? if($animais['status'] == '1'){ echo "Óbito"; } ?></td>
          <td><?=$pai[0]['nome']?></td>
          <td><?=$pai[0]['fbb']?></td>
          <td><?=$mae[0]['nome']?></td>
          <td><?=$mae[0]['fbb']?></td>
        </tr>
      <? $x++; if($x > 30){ break; } } } ?>
      </table>
</div>

<div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%; font-size:10px;">
<ul style="list-style:none; font-weight: bold; text-transform:uppercase ">
  <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
  <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
</ul>
</div>
</div>
<? } ?>

<? if($qtd > 30){ ?>
  <div id="quebra" style="margin-top:0%; height:1%;"></div>

  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_topo" style="text-align:center; width:100%;">
      <div id="topo" style="height:80px;"><img src="../../img/topo_relatorio.png" height="80"/><br/></div>
      <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
      <samp style="color:#F06;"><div id="topo3" style=" font-size:20px;"> NOTIFICAÇÃO DE NASCIMENTO</div></samp>
    </div>

    <div id="corpo_dados">
      <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
        <li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$numeracao?>-3</span></li>
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
          <li style="float:left; margin-right:150px;">Raça: <span style="font-weight:bold;"> <?=$raca?></span></li>
          <li>Categoria: <span style="font-weight:bold;">PO(X) PC( )</span></li>
          <li>Monta Natural( ) &nbsp Transplante de Embriões( ) &nbsp Inseminação Artificial(X)  &nbsp Sêmen Congelado( )  &nbsp Sêmen a Fresco( )</li>
      </ul>
    </div>


  <div id="imprimir" style="margin-bottom:10px;">
  <table class="table table-bordered" id="tabela_padrao" border="1" style="width:98%; margin-left:1%; text-align:center; font-size:12px;">
      <tr>
        <th>Fbb</th>
        <th>Nome</th>
        <th>Tat.</th>
        <th>Sexo</th>
        <th>Nascimento</th>
        <th>COD.</th>
        <th>Pai</th>
        <th>Fbb</th>
        <th>Mãe</th>
        <th>Fbb</th>
      </tr>
      <?
      $x=1;
      $animal = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial' AND data_de_nascimento <= '$data_final' AND tipo_reproducao = 'Inseminação artificial' ORDER BY data_de_nascimento asc");
      foreach ($animal as $animais) {
      if($x <= 30){
        $x++;
      }else{
      $data = $animais['data_de_nascimento'];
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

      $id_pai = $animais['pai'];
      if($animais['terceiro_pai']){
        $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
      }else{
        $pai = DBRead('animais', "WHERE id = '$id_pai'");
      }

      $id_mae = $animais['mae'];
      if($animais['terceiro_mae']){
        $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
      }else{
        $mae = DBRead('animais', "WHERE id = '$id_mae'");
      }
      ?>
        <tr>
            <td><?=$animais['fbb']?></td>
            <td><?=$animais['nome']?></td>
            <td><?=$animais['tatuagem']?></td>
            <td><?=$animais['sexo']?></td>
            <td><?=$data?></td>
            <td><? if($animais['status'] == '1'){ echo "Óbito"; } ?></td>
            <td><?=$pai[0]['nome']?></td>
            <td><?=$pai[0]['fbb']?></td>
            <td><?=$mae[0]['nome']?></td>
            <td><?=$mae[0]['fbb']?></td>
          </tr>
        <? $x++; if($x > 45){ break; } } } ?>
        </table>
  </div>

  <div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%; font-size:10px;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase ">
    <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
    <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
  </ul>
  </div>
  </div>
<? } ?>

<? if($qtd > 45){ ?>
  <div id="quebra" style="margin-top:0%; height:1%;"></div>

  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_topo" style="text-align:center; width:100%;">
      <div id="topo" style="height:80px;"><img src="../../img/topo_relatorio.png" height="80"/><br/></div>
      <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
      <samp style="color:#F06;"><div id="topo3" style=" font-size:20px;"> NOTIFICAÇÃO DE NASCIMENTO</div></samp>
    </div>

    <div id="corpo_dados">
      <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
        <li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$numeracao?>-4</span></li>
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
          <li style="float:left; margin-right:150px;">Raça: <span style="font-weight:bold;"> <?=$raca?></span></li>
          <li>Categoria: <span style="font-weight:bold;">PO(X) PC( )</span></li>
          <li>Monta Natural( ) &nbsp Transplante de Embriões( ) &nbsp Inseminação Artificial(X)  &nbsp Sêmen Congelado( )  &nbsp Sêmen a Fresco( )</li>
      </ul>
    </div>


  <div id="imprimir" style="margin-bottom:10px;">
  <table class="table table-bordered" id="tabela_padrao" border="1" style="width:98%; margin-left:1%; text-align:center; font-size:12px;">
      <tr>
        <th>Fbb</th>
        <th>Nome</th>
        <th>Tat.</th>
        <th>Sexo</th>
        <th>Nascimento</th>
        <th>COD.</th>
        <th>Pai</th>
        <th>Fbb</th>
        <th>Mãe</th>
        <th>Fbb</th>
      </tr>
      <?
      $x=1;
      $animal = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial' AND data_de_nascimento <= '$data_final' AND tipo_reproducao = 'Inseminação artificial' ORDER BY data_de_nascimento asc");
      foreach ($animal as $animais) {
      if($x <= 45){
        $x++;
      }else{
      $data = $animais['data_de_nascimento'];
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

      $id_pai = $animais['pai'];
      if($animais['terceiro_pai']){
        $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
      }else{
        $pai = DBRead('animais', "WHERE id = '$id_pai'");
      }

      $id_mae = $animais['mae'];
      if($animais['terceiro_mae']){
        $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
      }else{
        $mae = DBRead('animais', "WHERE id = '$id_mae'");
      }
      ?>
        <tr>
            <td><?=$animais['fbb']?></td>
            <td><?=$animais['nome']?></td>
            <td><?=$animais['tatuagem']?></td>
            <td><?=$animais['sexo']?></td>
            <td><?=$data?></td>
            <td><? if($animais['status'] == '1'){ echo "Óbito"; } ?></td>
            <td><?=$pai[0]['nome']?></td>
            <td><?=$pai[0]['fbb']?></td>
            <td><?=$mae[0]['nome']?></td>
            <td><?=$mae[0]['fbb']?></td>
          </tr>
        <? $x++; if($x > 60){ break; } } } ?>
        </table>
  </div>

  <div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%; font-size:10px;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase ">
    <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
    <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
  </ul>
  </div>
  </div>
<? } ?>

<? if($qtd > 60){ ?>
  <div id="quebra" style="margin-top:0%; height:1%;"></div>

  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_topo" style="text-align:center; width:100%;">
      <div id="topo" style="height:80px;"><img src="../../img/topo_relatorio.png" height="80"/><br/></div>
      <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
      <samp style="color:#F06;"><div id="topo3" style=" font-size:20px;"> NOTIFICAÇÃO DE NASCIMENTO</div></samp>
    </div>

    <div id="corpo_dados">
      <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
        <li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$numeracao?>-5</span></li>
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
          <li style="float:left; margin-right:150px;">Raça: <span style="font-weight:bold;"> <?=$raca?></span></li>
          <li>Categoria: <span style="font-weight:bold;">PO(X) PC( )</span></li>
          <li>Monta Natural( ) &nbsp Transplante de Embriões( ) &nbsp Inseminação Artificial(X)  &nbsp Sêmen Congelado( )  &nbsp Sêmen a Fresco( )</li>
      </ul>
    </div>


  <div id="imprimir" style="margin-bottom:10px;">
  <table class="table table-bordered" id="tabela_padrao" border="1" style="width:98%; margin-left:1%; text-align:center; font-size:12px;">
      <tr>
        <th>Fbb</th>
        <th>Nome</th>
        <th>Tat.</th>
        <th>Sexo</th>
        <th>Nascimento</th>
        <th>COD.</th>
        <th>Pai</th>
        <th>Fbb</th>
        <th>Mãe</th>
        <th>Fbb</th>
      </tr>
      <?
      $x=1;
      $animal = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial' AND data_de_nascimento <= '$data_final' AND tipo_reproducao = 'Inseminação artificial' ORDER BY data_de_nascimento asc");
      foreach ($animal as $animais) {
      if($x <= 60){
        $x++;
      }else{
      $data = $animais['data_de_nascimento'];
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

      $id_pai = $animais['pai'];
      if($animais['terceiro_pai']){
        $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
      }else{
        $pai = DBRead('animais', "WHERE id = '$id_pai'");
      }

      $id_mae = $animais['mae'];
      if($animais['terceiro_mae']){
        $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
      }else{
        $mae = DBRead('animais', "WHERE id = '$id_mae'");
      }
      ?>
        <tr>
            <td><?=$animais['fbb']?></td>
            <td><?=$animais['nome']?></td>
            <td><?=$animais['tatuagem']?></td>
            <td><?=$animais['sexo']?></td>
            <td><?=$data?></td>
            <td><? if($animais['status'] == '1'){ echo "Óbito"; } ?></td>
            <td><?=$pai[0]['nome']?></td>
            <td><?=$pai[0]['fbb']?></td>
            <td><?=$mae[0]['nome']?></td>
            <td><?=$mae[0]['fbb']?></td>
          </tr>
        <? $x++; if($x > 75){ break; } } } ?>
        </table>
  </div>

  <div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%; font-size:10px;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase ">
    <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
    <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
  </ul>
  </div>
  </div>
<? } ?>

<? if($qtd > 75){ ?>
  <div id="quebra" style="margin-top:0%; height:1%;"></div>

  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_topo" style="text-align:center; width:100%;">
      <div id="topo" style="height:80px;"><img src="../../img/topo_relatorio.png" height="80"/><br/></div>
      <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
      <samp style="color:#F06;"><div id="topo3" style=" font-size:20px;"> NOTIFICAÇÃO DE NASCIMENTO</div></samp>
    </div>

    <div id="corpo_dados">
      <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
        <li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$numeracao?>-6</span></li>
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
          <li style="float:left; margin-right:150px;">Raça: <span style="font-weight:bold;"> <?=$raca?></span></li>
          <li>Categoria: <span style="font-weight:bold;">PO(X) PC( )</span></li>
          <li>Monta Natural( ) &nbsp Transplante de Embriões( ) &nbsp Inseminação Artificial(X)  &nbsp Sêmen Congelado( )  &nbsp Sêmen a Fresco( )</li>
      </ul>
    </div>


  <div id="imprimir" style="margin-bottom:10px;">
  <table class="table table-bordered" id="tabela_padrao" border="1" style="width:98%; margin-left:1%; text-align:center; font-size:12px;">
      <tr>
        <th>Fbb</th>
        <th>Nome</th>
        <th>Tat.</th>
        <th>Sexo</th>
        <th>Nascimento</th>
        <th>COD.</th>
        <th>Pai</th>
        <th>Fbb</th>
        <th>Mãe</th>
        <th>Fbb</th>
      </tr>
      <?
      $x=1;
      $animal = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial' AND data_de_nascimento <= '$data_final' AND tipo_reproducao = 'Inseminação artificial' ORDER BY data_de_nascimento asc");
      foreach ($animal as $animais) {
      if($x <= 75){
        $x++;
      }else{
      $data = $animais['data_de_nascimento'];
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

      $id_pai = $animais['pai'];
      if($animais['terceiro_pai']){
        $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
      }else{
        $pai = DBRead('animais', "WHERE id = '$id_pai'");
      }

      $id_mae = $animais['mae'];
      if($animais['terceiro_mae']){
        $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
      }else{
        $mae = DBRead('animais', "WHERE id = '$id_mae'");
      }
      ?>
        <tr>
            <td><?=$animais['fbb']?></td>
            <td><?=$animais['nome']?></td>
            <td><?=$animais['tatuagem']?></td>
            <td><?=$animais['sexo']?></td>
            <td><?=$data?></td>
            <td><? if($animais['status'] == '1'){ echo "Óbito"; } ?></td>
            <td><?=$pai[0]['nome']?></td>
            <td><?=$pai[0]['fbb']?></td>
            <td><?=$mae[0]['nome']?></td>
            <td><?=$mae[0]['fbb']?></td>
          </tr>
        <? $x++; if($x > 90){ break; } } } ?>
        </table>
  </div>

  <div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%; font-size:10px;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase ">
    <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
    <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
  </ul>
  </div>
  </div>
<? } ?>

<? if($qtd > 90){ ?>
  <div id="quebra" style="margin-top:0%; height:1%;"></div>

  <div id="corpo" style="margin-top:1%;">
    <div id="corpo_topo" style="text-align:center; width:100%;">
      <div id="topo" style="height:80px;"><img src="../../img/topo_relatorio.png" height="80"/><br/></div>
      <div id="topo2" style="">ASSOCIAÇÃO BRASILEIRA DE CRIADORES DE OVINOS - A.R.C.O<br /></div>
      <samp style="color:#F06;"><div id="topo3" style=" font-size:20px;"> NOTIFICAÇÃO DE NASCIMENTO</div></samp>
    </div>

    <div id="corpo_dados">
      <ul style="list-style:none; font-weight: bold; text-transform:uppercase; font-size:12px;">
        <li style="margin-bottom:10px;">NÚMERO:<span style="font-weight:100;"><?=$numeracao?>-7</span></li>
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
          <li style="float:left; margin-right:150px;">Raça: <span style="font-weight:bold;"> <?=$raca?></span></li>
          <li>Categoria: <span style="font-weight:bold;">PO(X) PC( )</span></li>
          <li>Monta Natural( ) &nbsp Transplante de Embriões( ) &nbsp Inseminação Artificial(X)  &nbsp Sêmen Congelado( )  &nbsp Sêmen a Fresco( )</li>
      </ul>
    </div>


  <div id="imprimir" style="margin-bottom:10px;">
  <table class="table table-bordered" id="tabela_padrao" border="1" style="width:98%; margin-left:1%; text-align:center; font-size:12px;">
      <tr>
        <th>Fbb</th>
        <th>Nome</th>
        <th>Tat.</th>
        <th>Sexo</th>
        <th>Nascimento</th>
        <th>COD.</th>
        <th>Pai</th>
        <th>Fbb</th>
        <th>Mãe</th>
        <th>Fbb</th>
      </tr>
      <?
      $x=1;
      $animal = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial' AND data_de_nascimento <= '$data_final' AND tipo_reproducao = 'Inseminação artificial' ORDER BY data_de_nascimento asc");
      foreach ($animal as $animais) {
      if($x <= 105){
        $x++;
      }else{
      $data = $animais['data_de_nascimento'];
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

      $id_pai = $animais['pai'];
      if($animais['terceiro_pai']){
        $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
      }else{
        $pai = DBRead('animais', "WHERE id = '$id_pai'");
      }

      $id_mae = $animais['mae'];
      if($animais['terceiro_mae']){
        $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
      }else{
        $mae = DBRead('animais', "WHERE id = '$id_mae'");
      }
      ?>
        <tr>
            <td><?=$animais['fbb']?></td>
            <td><?=$animais['nome']?></td>
            <td><?=$animais['tatuagem']?></td>
            <td><?=$animais['sexo']?></td>
            <td><?=$data?></td>
            <td><? if($animais['status'] == '1'){ echo "Óbito"; } ?></td>
            <td><?=$pai[0]['nome']?></td>
            <td><?=$pai[0]['fbb']?></td>
            <td><?=$mae[0]['nome']?></td>
            <td><?=$mae[0]['fbb']?></td>
          </tr>
        <? $x++; if($x > 105){ break; } } } ?>
        </table>
  </div>

  <div id="assinatura" style="text-align:center; float:left; width:100%; margin-top:4%; font-size:10px;">
  <ul style="list-style:none; font-weight: bold; text-transform:uppercase ">
    <li style="margin-top:1%;">Avenida 7 de setembro, 1159 - CX Postal, 145 - Bagé/RS - Cep 96400-970</li>
    <li>Fone: (53) 3242-8422 - Fax: (53) 3242-9522 - E-mail: registro@arcoovinos.com.br</li>
  </ul>
  </div>
  </div>
<? } ?>
</body>
</html>
