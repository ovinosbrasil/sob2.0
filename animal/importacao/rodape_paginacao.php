<div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:flex-end; gap:16px;">
  <form method="get" class="form-inline">
    <input type="hidden" name="pg" value="atualizar_rebanho">
    <input type="hidden" name="aba" value="<?=hPreviaRebanho($grupo)?>">
    <input type="hidden" name="pesquisa" value="<?=hPreviaRebanho($pesquisaPrevia)?>">
    <label style="font-weight:normal;">Por página <select name="limite" class="form-control input-sm" onchange="this.form.submit()">
      <?php foreach (array(10,20,50,100) as $limite): ?><option <?=$limite === $paginacao['limite'] ? 'selected' : ''?>><?=$limite?></option><?php endforeach; ?>
    </select></label>
  </form>
  <span class="text-muted">Exibindo <?=$paginacao['total'] ? ($paginacao['pagina']-1)*$paginacao['limite']+1 : 0?> a <?=min($paginacao['pagina']*$paginacao['limite'], $paginacao['total'])?> de <?=$paginacao['total']?> animais</span>
  <?php if ($paginacao['paginas'] > 1): ?>
  <ul class="pagination pagination-sm no-margin">
    <?php
    $numeros = array_unique(array_merge(array(1, $paginacao['paginas']), range(max(1,$paginacao['pagina']-1), min($paginacao['paginas'],$paginacao['pagina']+1)))); sort($numeros);
    $links = array(array('«', max(1,$paginacao['pagina']-1)));
    $anterior = 0;
    foreach ($numeros as $numero) { if ($anterior && $numero > $anterior+1) $links[] = array('…',0); $links[] = array($numero,$numero); $anterior=$numero; }
    $links[] = array('»', min($paginacao['paginas'],$paginacao['pagina']+1));
    foreach ($links as list($rotulo,$numero)): ?>
    <li class="<?=$numero === 0 ? 'disabled' : ($rotulo === $paginacao['pagina'] ? 'active' : '')?>"><?php if (!$numero): ?><span>…</span><?php else: ?><a href="<?=hPreviaRebanho($urlPrevia($grupo,$numero))?>"><?=$rotulo?></a><?php endif; ?></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>
