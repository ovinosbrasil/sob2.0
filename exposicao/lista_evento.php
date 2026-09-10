<?php
include __DIR__ . "/../_config.php";
$nome = isset($_GET['nome']) ? $_GET['nome'] : '';
$evento = DBRead('julgamento', "WHERE nome LIKE '%$nome%' ORDER BY nome asc LIMIT 10");
?>

<div id="titulo_geral" style="background-color:#00a65a; height:35px; color:#fff; padding-top:0.5%;">
  <div style="padding-left:1%; font-weight:bold; font-size:16px;">Pesquisa de eventos</div>
</div>

<?php
foreach ($evento as $evento_) {
  $data = $evento_['data'];
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
  <a href="javascript:linkar_evento('<?= $evento_['id'] ?>');" style="color:#2d2c2c;">
     <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%;">
       <span style="font-weight:bold;"> <?= $evento_['nome'] ?></span> - Data: <?= $data ?> - Local: <?= $evento_['cidade'] ?>
     </div>
  </a>
<?php } ?>

<a href="javascript:fechar_lista_evento();" style="color:#2d2c2c;">
  <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%; font-size:15px;"> <span style="color:red;"> Fechar Pesquisa </span></div>
</a>
