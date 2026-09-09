<div id="titulo_geral" style="background-color:#00a65a; height:35px; color:#fff; padding-top:0.5%;">
  <div style="padding-left:1%; font-weight:bold; font-size:16px;">Pesquisa de Animais</div>
</div>
<?
ini_set('display_errors', 0);
include "../_config.php";
$nome = $_GET['nome'];

$animal = DBRead('animais',"WHERE nome LIKE '%$nome%' ORDER BY nome asc LIMIT 10");
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

  ?>
  <a href="javascript:linkar_animal_peso('<?=$animais['id']?>');" style="color:#2d2c2c;">
     <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%;">
       <span style="font-weight:bold;"> <?=$animais['nome']?></span> - Nascimento: <?=$data?>
       <? if($animais['status'] == 0){ ?> <span style="color:#37abc0;">(Rebanho) <? } ?>
       <? if($animais['status'] == 1){ ?> <span style="color:red;">(Morto) <? } ?>
       <? if($animais['status'] == 2){ ?> <span style="color:green;">(Vendido) <? } ?>
       <? if($animais['status'] == 3){ ?> <span style="color:red;">(Empréstimo) <? } ?>
       <? if($animais['status'] == 4){ ?> <span style="color:red;">(Doação) <? } ?>
       <? if($animais['status'] == 5){ ?> <span style="color:red;">(Abate) <? } ?>

     </div>
</a>
<? } ?>

  <a href="javascript:fechar_lista_peso();" style="color:#2d2c2c;">
    <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%; font-size:15px;"> <span style="color:red;"> Fechar Pesquisa </span></div>
  </a>
