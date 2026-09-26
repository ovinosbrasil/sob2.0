<?php
require __DIR__ . "/../_config.php";
$complementar = ($_GET['campo'] ?? '') === 'pai_2';
$selecionar = $complementar ? 'linkar_pai_2' : 'linkar_pai';
$fechar = $complementar ? 'fechar_lista_pai_2' : 'fechar_lista_pai';
?>
<?
$nome = DBEscape($_GET['nome'] ?? '');

$animal = DBRead('animais',"WHERE nome LIKE '%$nome%' AND sexo = 'Macho' ORDER BY nome asc LIMIT 7");
foreach (($animal ?: []) as $animais) {
  $data = 'Não informado';
  $nascimento = $animais['data_de_nascimento'] ?? '';
  if (is_string($nascimento) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $nascimento, $partes)
      && checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
    $data = $partes[3] . '/' . $partes[2] . '/' . $partes[1];
  }
  ?>
  <button type="button" class="animal-search-result" onclick="<?=$selecionar?>(this.getAttribute('data-nome'), <?=(int)$animais['id']?>, 0)" data-nome="<?=htmlspecialchars($animais['nome'], ENT_QUOTES, 'UTF-8')?>" style="display:block; width:100%; text-align:left; border:0; border-bottom:1px solid #eee; color:#2d2c2c; padding:8px; overflow-wrap:anywhere;">
    <strong><?=htmlspecialchars($animais['nome'], ENT_QUOTES, 'UTF-8')?></strong><br>
    Nascimento: <?=$data?>
  <? if($animais['status'] == 0){ ?> <span style="color:#00a65a;">(Rebanho)</span> <? } ?>
  <? if($animais['status'] == 1){ ?> <span style="color:red;">(Morto)</span> <? } ?>
  <? if($animais['status'] == 2){ ?> <span style="color:green;">(Vendido)</span> <? } ?>
  <? if($animais['status'] == 3){ ?> <span style="color:red;">(Empréstimo)</span> <? } ?>
  <? if($animais['status'] == 4){ ?> <span style="color:red;">(Doação)</span> <? } ?>
  <? if($animais['status'] == 5){ ?> <span style="color:red;">(Abate)</span> <? } ?></button>

<? } ?>

<?
$animal = DBRead('terceiros',"WHERE nome LIKE '%$nome%' AND sexo = 'Macho' ORDER BY nome asc LIMIT 3");
foreach (($animal ?: []) as $animais) {
  $data = 'Não informado';
  $nascimento = $animais['data_de_nascimento'] ?? '';
  if (is_string($nascimento) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $nascimento, $partes)
      && checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
    $data = $partes[3] . '/' . $partes[2] . '/' . $partes[1];
  }
  ?>
  <button type="button" class="animal-search-result" onclick="<?=$selecionar?>(this.getAttribute('data-nome'), <?=(int)$animais['id']?>, 1)" data-nome="<?=htmlspecialchars($animais['nome'], ENT_QUOTES, 'UTF-8')?>" style="display:block; width:100%; text-align:left; border:0; border-bottom:1px solid #eee; color:#2d2c2c; padding:8px; overflow-wrap:anywhere;">
    <strong><?=htmlspecialchars($animais['nome'], ENT_QUOTES, 'UTF-8')?></strong><br>
    Nascimento: <?=$data?> <span class="text-muted">(Terceiros)</span></button>
<? } ?>



  <button type="button" onclick="<?=$fechar?>()" class="btn btn-link btn-sm">Fechar Pesquisa</button>
