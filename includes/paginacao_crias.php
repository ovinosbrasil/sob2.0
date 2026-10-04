<?php
function paginacaoCrias($total, array $parametros, $chave = 'pagina_crias') {
    $porPagina = filter_var($_REQUEST['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
    if (!in_array($porPagina, array(10, 20, 50, 100), true)) $porPagina = 10;
    $paginas = max(1, (int)ceil($total / $porPagina));
    $pagina = min(max(1, (int)($_GET[$chave] ?? 1)), $paginas);
    return array('total' => $total, 'por_pagina' => $porPagina, 'paginas' => $paginas,
        'pagina' => $pagina, 'inicio' => ($pagina - 1) * $porPagina, 'parametros' => $parametros, 'chave' => $chave);
}
function renderPaginacaoCrias(array $p, $rotulo = 'crias') {
    $h = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
    $url = function ($pagina) use ($p, $h) {
        return $h('geral.php?' . http_build_query(array_merge($p['parametros'], array('por_pagina' => $p['por_pagina'], $p['chave'] => $pagina))));
    };
    ?>
    <div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
      <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
        <?php foreach ($p['parametros'] as $campo => $valor): ?>
        <input type="hidden" name="<?=$h($campo)?>" value="<?=$h($valor)?>">
        <?php endforeach; ?>
        <label for="<?=$h($p['chave'])?>-quantidade" style="margin:0; font-weight:normal;">Por página</label>
        <select id="<?=$h($p['chave'])?>-quantidade" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
          <?php foreach (array(10, 20, 50, 100) as $quantidade): ?>
          <option value="<?=$quantidade?>" <?=$quantidade === $p['por_pagina'] ? 'selected' : ''?>><?=$quantidade?></option>
          <?php endforeach; ?>
        </select>
        <noscript><button type="submit" class="btn btn-default btn-sm">Aplicar</button></noscript>
      </form>
      <span class="text-muted">Exibindo <?=$p['total'] ? $p['inicio'] + 1 : 0?> a <?=min($p['inicio'] + $p['por_pagina'], $p['total'])?> de <?=$p['total']?> <?=$h($rotulo)?></span>
      <?php if ($p['paginas'] > 1):
          $visiveis = array(1, $p['paginas']);
          for ($numero = max(1, $p['pagina'] - 1); $numero <= min($p['paginas'], $p['pagina'] + 1); $numero++) $visiveis[] = $numero;
          $visiveis = array_unique($visiveis); sort($visiveis); $anterior = 0;
      ?>
      <nav aria-label="Paginação: <?=$h($rotulo)?>"><ul class="pagination pagination-sm no-margin">
        <?php if ($p['pagina'] > 1): ?>
        <li><a href="<?=$url($p['pagina'] - 1)?>" aria-label="Página anterior">&laquo;</a></li>
        <?php else: ?><li class="disabled"><span aria-label="Página anterior" aria-disabled="true">&laquo;</span></li><?php endif; ?>
        <?php foreach ($visiveis as $numero): ?>
        <?php if ($anterior && $numero > $anterior + 1): ?><li class="disabled"><span>&hellip;</span></li><?php endif; ?>
        <?php if ($numero === $p['pagina']): ?>
        <li class="active"><span aria-current="page"><?=$numero?></span></li>
        <?php else: ?><li><a href="<?=$url($numero)?>"><?=$numero?></a></li><?php endif; ?>
        <?php $anterior = $numero; endforeach; ?>
        <?php if ($p['pagina'] < $p['paginas']): ?>
        <li><a href="<?=$url($p['pagina'] + 1)?>" aria-label="Próxima página">&raquo;</a></li>
        <?php else: ?><li class="disabled"><span aria-label="Próxima página" aria-disabled="true">&raquo;</span></li><?php endif; ?>
      </ul></nav>
      <?php endif; ?>
    </div>
    <?php
}
