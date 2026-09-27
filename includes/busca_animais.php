<?php
/**
 * Renderiza um campo de busca reutilizável para animais e receptoras.
 *
 * Opções: id, name, label, tipo (machos|femeas|receptoras|todos), value,
 * required, placeholder, name_id, name_origem, exibir_label, classe,
 * botao_busca, novo_url, novo_texto e limite_origem.
 */
function renderBuscaAnimais(array $opcoes = array())
{
    static $sequencia = 0;
    $sequencia++;

    $tipos = array('machos', 'femeas', 'receptoras', 'todos');
    $tipo = in_array($opcoes['tipo'] ?? '', $tipos, true) ? $opcoes['tipo'] : 'todos';
    $idOriginal = $opcoes['id'] ?? ('busca-animais-' . $sequencia);
    $id = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string)$idOriginal);
    $name = (string)($opcoes['name'] ?? $id);
    $nameId = (string)($opcoes['name_id'] ?? ($name . '_id'));
    $nameOrigem = (string)($opcoes['name_origem'] ?? ($name . '_origem'));
    $label = (string)($opcoes['label'] ?? 'Animal');
    $valor = (string)($opcoes['value'] ?? '');
    $placeholder = (string)($opcoes['placeholder'] ?? 'Digite para pesquisar');
    $obrigatorio = !empty($opcoes['required']);
    $exibirLabel = !array_key_exists('exibir_label', $opcoes) || $opcoes['exibir_label'];
    $classe = preg_replace('/[^a-zA-Z0-9 _-]/', '', (string)($opcoes['classe'] ?? ''));
    $botaoBusca = !empty($opcoes['botao_busca']);
    $novoUrl = trim((string)($opcoes['novo_url'] ?? ''));
    $novoTexto = (string)($opcoes['novo_texto'] ?? 'Novo');
    $temAcao = $botaoBusca || $novoUrl !== '';
    $limiteOrigem = filter_var($opcoes['limite_origem'] ?? 0, FILTER_VALIDATE_INT);
    $limiteOrigem = $limiteOrigem && $limiteOrigem >= 1 && $limiteOrigem <= 10 ? $limiteOrigem : 0;
    ?>
    <div class="sob-busca-animais <?=htmlspecialchars($classe, ENT_QUOTES, 'UTF-8')?>"
         data-busca-animais
         data-tipo="<?=htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8')?>"
         data-limite-origem="<?=$limiteOrigem?>">
      <?php if ($exibirLabel): ?>
      <label for="<?=htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?>">
        <?=htmlspecialchars($label, ENT_QUOTES, 'UTF-8')?><?php if ($obrigatorio): ?><span class="text-danger">*</span><?php endif; ?>
      </label>
      <?php endif; ?>
      <?php if ($temAcao): ?><div class="input-group"><?php endif; ?>
      <input type="text"
             class="form-control sob-busca-animais__input"
             id="<?=htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?>"
             name="<?=htmlspecialchars($name, ENT_QUOTES, 'UTF-8')?>"
             value="<?=htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')?>"
             placeholder="<?=htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8')?>"
             autocomplete="off"
             aria-autocomplete="list"
             aria-controls="<?=$id?>-resultados"
             aria-expanded="false"
             <?=$obrigatorio ? 'required' : ''?>>
      <?php if ($temAcao): ?>
      <span class="input-group-btn">
        <?php if ($novoUrl !== ''): ?>
        <a class="btn btn-success" href="<?=htmlspecialchars($novoUrl, ENT_QUOTES, 'UTF-8')?>" target="_blank" rel="noopener" title="Cadastrar animal em nova aba"><?=htmlspecialchars($novoTexto, ENT_QUOTES, 'UTF-8')?></a>
        <?php else: ?>
        <button type="submit" class="btn btn-flat" aria-label="Pesquisar"><i class="fa fa-search" aria-hidden="true"></i></button>
        <?php endif; ?>
      </span>
      </div>
      <?php endif; ?>
      <input type="hidden" name="<?=htmlspecialchars($nameId, ENT_QUOTES, 'UTF-8')?>" data-busca-animais-id>
      <input type="hidden" name="<?=htmlspecialchars($nameOrigem, ENT_QUOTES, 'UTF-8')?>" data-busca-animais-origem>
      <div class="sob-busca-animais__resultados"
           id="<?=$id?>-resultados"
           role="listbox"
           aria-label="Resultados de <?=htmlspecialchars($label, ENT_QUOTES, 'UTF-8')?>"
           hidden></div>
    </div>
    <?php
}
