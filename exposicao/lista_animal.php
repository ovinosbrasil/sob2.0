<?php
include __DIR__ . "/../_config.php";
$nome = isset($_GET['nome']) ? $_GET['nome'] : '';

$animal = DBRead('animais', "WHERE nome LIKE '%$nome%' ORDER BY nome asc LIMIT 10");
?>

<div id="titulo_geral" style="background-color:#00a65a; height:35px; color:#fff; padding-top:0.5%;">
  <div style="padding-left:1%; font-weight:bold; font-size:16px;">Pesquisa de Animais</div>
</div>

<?php
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
  <a href="javascript:linkar_animal_exposicao('<?= $animais['id'] ?>');" style="color:#2d2c2c;">
     <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%;">
       <span style="font-weight:bold;"> <?= $animais['nome'] ?></span> - Nascimento: <?= $data ?>
       <?php if($animais['status'] == 0){ ?> <span style="color:#37abc0;">(Rebanho) <?php } ?>
       <?php if($animais['status'] == 1){ ?> <span style="color:red;">(Morto) <?php } ?>
       <?php if($animais['status'] == 2){ ?> <span style="color:green;">(Vendido) <?php } ?>
       <?php if($animais['status'] == 3){ ?> <span style="color:red;">(Empréstimo) <?php } ?>
       <?php if($animais['status'] == 4){ ?> <span style="color:red;">(Doação) <?php } ?>
       <?php if($animais['status'] == 5){ ?> <span style="color:red;">(Abate) <?php } ?>

     </div>
</a>
<?php } ?>

<a href="javascript:fechar_lista_animal_exposicao();" style="color:#2d2c2c;">
  <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%; font-size:15px;"> <span style="color:red;"> Fechar Pesquisa </span></div>
</a>
