<?php
require_once __DIR__ . '/../_config.php';
$nome = trim((string)($_GET['nome'] ?? ''));
if (mb_strlen($nome, 'UTF-8') < 2) exit;
$nomeEscapado = DBEscape($nome);
$animais = DBRead('animais', "WHERE (nome LIKE '%$nomeEscapado%' OR chip LIKE '%$nomeEscapado%') ORDER BY nome ASC LIMIT 10") ?: array();
function vacinaBuscaH($valor){return htmlspecialchars((string)$valor,ENT_QUOTES,'UTF-8');}
function vacinaBuscaData($valor){$v=substr((string)$valor,0,10);$d=DateTimeImmutable::createFromFormat('!Y-m-d',$v);return $d&&$d->format('Y-m-d')===$v?$d->format('d/m/Y'):'Não informado';}
?>
<?php if(!$animais): ?><div class="text-muted" style="padding:10px;">Nenhum animal encontrado.</div><?php endif; ?>
<?php foreach($animais as $animal): ?>
<button type="button" class="vacina-search-result" data-id="<?=(int)$animal['id']?>" data-nome="<?=vacinaBuscaH($animal['nome'])?>" onclick="selecionarAnimalVacina(this.getAttribute('data-id'),this.getAttribute('data-nome'))"><strong><?=vacinaBuscaH($animal['nome'])?></strong><br><span class="text-muted">Nascimento: <?=vacinaBuscaH(vacinaBuscaData($animal['data_de_nascimento']??''))?><?=trim((string)($animal['chip']??''))!==''?' · Chip: '.vacinaBuscaH($animal['chip']):''?></span></button>
<?php endforeach; ?>
<button type="button" class="btn btn-link btn-sm" onclick="fecharListaAnimalVacina()">Fechar pesquisa</button>
