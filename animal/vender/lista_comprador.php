<?php
require_once __DIR__ . '/../../_config.php';
$nome = isset($_GET['nome']) && is_string($_GET['nome']) ? $_GET['nome'] : '';
$nome = DBEscape($nome);
$compradores = DBRead('mercado', "WHERE nome LIKE '%$nome%' ORDER BY nome ASC LIMIT 10") ?: array();
?>
<?php if (!$compradores) { ?>
  <div class="text-muted" style="padding:8px;">Nenhum comprador encontrado.</div>
<?php } ?>
<?php foreach ($compradores as $comprador_) { ?>
  <button type="button" onclick="linkar_comprador(this.getAttribute('data-nome'), this.getAttribute('data-id'))" data-id="<?=(int)$comprador_['id']?>" data-nome="<?=htmlspecialchars($comprador_['nome'], ENT_QUOTES, 'UTF-8')?>" class="sob-busca-animais__opcao">
    <strong><?=htmlspecialchars($comprador_['nome'], ENT_QUOTES, 'UTF-8')?></strong><br>
    Cidade: <?=htmlspecialchars($comprador_['cidade'] ?? '', ENT_QUOTES, 'UTF-8')?>
  </button>
<?php } ?>
  <button type="button" onclick="fechar_lista_comprador()" class="sob-busca-animais__fechar text-danger">Fechar Pesquisa</button>
