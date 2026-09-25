<?php
require_once __DIR__ . '/../_config.php';
$nome = DBEscape(isset($_GET['nome']) && is_string($_GET['nome']) ? $_GET['nome'] : '');
?>
<div id="titulo_geral" style="background-color:#00a65a; height:35px; color:#fff; padding-top:0.5%;">
  <div style="padding-left:1%; font-weight:bold; font-size:16px;">Pesquisa de Animais</div>
</div>
<?


$animal = DBRead('animais',"WHERE nome LIKE '%$nome%' ORDER BY nome asc LIMIT 10");
if (!$animal) { ?><div style="padding:8px;">Nenhum animal encontrado.</div><?php }
foreach (($animal ?: array()) as $animais) {
  $data = 'Não informada';
  if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $animais['data_de_nascimento'] ?? '', $partes)
      && checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
    $data = $partes[3] . '/' . $partes[2] . '/' . $partes[1];
  }
  ?>
  <a href="javascript:linkar_animal_peso('<?=$animais['id']?>');" style="color:#2d2c2c;">
     <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%;">
       <span style="font-weight:bold;"> <?=htmlspecialchars($animais['nome'], ENT_QUOTES, 'UTF-8')?></span> - Nascimento: <?=$data?>
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
