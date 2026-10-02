<?php
/** Componente padrão de busca de compradores. */
function renderBuscaCompradores(array $opcoes = array())
{
    static $sequencia = 0;
    $sequencia++;
    $id = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string)($opcoes['id'] ?? ('busca-compradores-' . $sequencia)));
    $name = (string)($opcoes['name'] ?? $id);
    $nameId = (string)($opcoes['name_id'] ?? ($name . '_id'));
    $label = (string)($opcoes['label'] ?? 'Comprador');
    $valor = (string)($opcoes['value'] ?? '');
    $valorId = max(0, (int)($opcoes['value_id'] ?? 0));
    $placeholder = (string)($opcoes['placeholder'] ?? 'Digite para pesquisar');
    $obrigatorio = !empty($opcoes['required']);
    $novoUrl = trim((string)($opcoes['novo_url'] ?? ''));
    $novoTexto = (string)($opcoes['novo_texto'] ?? 'Novo');
    $botaoBusca = !empty($opcoes['botao_busca']);
    ?>
    <div class="sob-busca-animais sob-busca-compradores" data-busca-compradores>
      <label for="<?=htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($label, ENT_QUOTES, 'UTF-8')?><?php if ($obrigatorio): ?><span class="text-danger">*</span><?php endif; ?></label>
      <?php if ($novoUrl !== '' || $botaoBusca): ?><div class="input-group"><?php endif; ?>
      <input type="text" class="form-control sob-busca-animais__input" id="<?=htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?>" name="<?=htmlspecialchars($name, ENT_QUOTES, 'UTF-8')?>" value="<?=htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')?>" placeholder="<?=htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8')?>" autocomplete="off" aria-autocomplete="list" aria-controls="<?=$id?>-resultados" aria-expanded="false" <?=$obrigatorio?'required':''?>>
      <?php if ($novoUrl !== '' || $botaoBusca): ?><span class="input-group-btn"><?php if ($novoUrl !== ''): ?><a class="btn btn-success" href="<?=htmlspecialchars($novoUrl, ENT_QUOTES, 'UTF-8')?>" target="_blank" rel="noopener"><?=htmlspecialchars($novoTexto, ENT_QUOTES, 'UTF-8')?></a><?php else: ?><button class="btn btn-primary" type="submit" aria-label="Pesquisar"><i class="fa fa-search" aria-hidden="true"></i></button><?php endif; ?></span></div><?php endif; ?>
      <input type="hidden" name="<?=htmlspecialchars($nameId, ENT_QUOTES, 'UTF-8')?>" value="<?=$valorId ?: ''?>" data-busca-compradores-id>
      <div class="sob-busca-animais__resultados" id="<?=$id?>-resultados" role="listbox" aria-label="Resultados de compradores" hidden></div>
    </div>
    <?php
}
