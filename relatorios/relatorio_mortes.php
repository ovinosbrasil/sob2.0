<?php
$hMortes = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$filtrosMortes = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? $_POST : $_GET;
$data_inicial = is_string($filtrosMortes['data_inicial'] ?? null) ? trim($filtrosMortes['data_inicial']) : date('01/m/Y');
$data_final = is_string($filtrosMortes['data_final'] ?? null) ? trim($filtrosMortes['data_final']) : date('t/m/Y');
$lerDataMortes = function ($valor) {
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $valor);
    return $data && $data->format('d/m/Y') === $valor ? $data : null;
};
$inicioMortes = $lerDataMortes($data_inicial);
$fimMortes = $lerDataMortes($data_final);
$erroMortes = '';
$mortes = array();
if (!$inicioMortes || !$fimMortes) {
    $erroMortes = 'Informe datas válidas no formato dd/mm/aaaa.';
} elseif ($inicioMortes > $fimMortes) {
    $erroMortes = 'A data final deve ser igual ou posterior à data inicial.';
} else {
    $inicioSql = $inicioMortes->format('Y-m-d');
    $fimSql = $fimMortes->format('Y-m-d');
    $mortes = DBRead('animais', "WHERE status IN (1, 5) AND data_de_saida >= '$inicioSql' AND data_de_saida <= '$fimSql' ORDER BY data_de_saida DESC, id DESC") ?: array();
}
$causasMortes = array_fill_keys(array('Nascimento', 'Verminose', 'Clostridiose', 'Pneumonia', 'Acidente', 'Parto', 'Idade', 'Intoxicação', 'Testículos', 'Outros'), 0);
$tipoMorte = function ($animal) { return (int)$animal['status'] === 5 ? 'Abate' : (trim($animal['causa_da_perda'] ?? '') ?: 'Não informado'); };
$tiposMortes = array_keys($causasMortes);
$tiposMortes[] = 'Abate';
$tiposMortes[] = 'Não informado';
foreach ($mortes as $animalMorte) {
    $tipoDisponivel = $tipoMorte($animalMorte);
    if (!in_array($tipoDisponivel, $tiposMortes, true)) $tiposMortes[] = $tipoDisponivel;
}
$filtroTipoMortes = is_string($filtrosMortes['tipo'] ?? null) ? trim($filtrosMortes['tipo']) : '';
if ($filtroTipoMortes !== '') {
    if (!in_array($filtroTipoMortes, $tiposMortes, true)) $tiposMortes[] = $filtroTipoMortes;
    $mortes = array_values(array_filter($mortes, function ($animal) use ($tipoMorte, $filtroTipoMortes) {
        return $tipoMorte($animal) === $filtroTipoMortes;
    }));
}
foreach ($mortes as $animalMorte) {
    $causa = $tipoMorte($animalMorte);
    $causasMortes[$causa] = ($causasMortes[$causa] ?? 0) + 1;
}
$totalMortes = count($mortes);
$porPaginaMortes = filter_var($filtrosMortes['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaMortes, array(10, 20, 50, 100), true)) $porPaginaMortes = 10;
$paginasMortes = max(1, (int)ceil($totalMortes / $porPaginaMortes));
$paginaMortes = min($paginasMortes, max(1, (int)($filtrosMortes['pag'] ?? 1)));
$offsetMortes = ($paginaMortes - 1) * $porPaginaMortes;
$urlMortes = function ($pagina) use ($data_inicial, $data_final, $porPaginaMortes, $hMortes, $filtroTipoMortes) {
    return $hMortes('geral.php?' . http_build_query(array('pg' => 'relatorio_mortes', 'data_inicial' => $data_inicial, 'data_final' => $data_final, 'por_pagina' => $porPaginaMortes, 'pag' => $pagina, 'tipo' => $filtroTipoMortes)));
};
?>
<section class="content-header">
  <h1>Relatório de mortes</h1>
  <ol class="breadcrumb"><li><i class="fa fa-book"></i> Relatórios</li><li class="active">Mortes</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <form id="filtros-mortes" action="geral.php" method="get">
        <input type="hidden" name="pg" value="relatorio_mortes">
        <input type="hidden" name="por_pagina" value="<?=$porPaginaMortes?>">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <?php foreach (array('data_inicial' => 'Data inicial', 'data_final' => 'Data final') as $campo => $rotulo): ?>
          <div class="form-group col-sm-6 col-md-3">
            <label for="<?=$campo?>"><?=$rotulo?><span class="text-danger">*</span></label>
            <div class="input-group date">
              <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
              <input type="text" class="form-control" id="<?=$campo?>" name="<?=$campo?>" value="<?=$hMortes($$campo)?>" placeholder="dd/mm/aaaa" maxlength="10" required>
            </div>
          </div>
          <?php endforeach; ?>
          <div class="form-group col-sm-6 col-md-3">
            <label for="tipo-mortes">Tipo</label>
            <select class="form-control" id="tipo-mortes" name="tipo">
              <option value="">Todos</option>
              <?php foreach ($tiposMortes as $opcaoTipo): ?>
              <option value="<?=$hMortes($opcaoTipo)?>" <?=$filtroTipoMortes === $opcaoTipo ? 'selected' : ''?>><?=$hMortes($opcaoTipo)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <button type="submit" class="btn btn-primary">Pesquisar</button>
            <a class="btn btn-default" href="geral.php?pg=relatorio_mortes">Limpar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php if ($erroMortes): ?>
  <div class="alert alert-warning" role="alert"><?=$hMortes($erroMortes)?></div>
  <?php else: ?>
  <div class="box" style="border-top:0;">
    <div class="box-header"><h2 class="box-title" style="font-size:16px;">Resumo por causa</h2><span class="pull-right text-muted">Total no período: <strong><?=$totalMortes?></strong></span></div>
    <div class="box-body">
      <div class="row">
        <?php foreach ($causasMortes as $causa => $quantidade): ?>
        <div class="col-xs-6 col-sm-4 col-md-2" style="margin-bottom:15px;">
          <div class="text-muted"><?=$hMortes($causa)?></div>
          <strong><?=$quantidade?></strong> <span class="text-muted">(<?=number_format($totalMortes ? $quantidade * 100 / $totalMortes : 0, 2, ',', '.')?>%)</span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;">
        <h2 class="box-title" style="font-size:16px; margin:0;">Mortes no período</h2>
        <span class="text-muted"><?=$hMortes($data_inicial)?> até <?=$hMortes($data_final)?></span>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr><th style="width:60px;">Nº</th><th>Animal</th><th>Data</th><th>Tipo da morte</th><th>Observações</th></tr></thead>
          <tbody>
            <?php if (!$mortes): ?><tr><td colspan="5" class="text-center text-muted" style="padding:30px;">Nenhuma morte encontrada no período informado. Verifique os filtros selecionados.</td></tr><?php endif; ?>
            <?php foreach (array_slice($mortes, $offsetMortes, $porPaginaMortes) as $indice => $animalMorte):
                $dataMorte = DateTimeImmutable::createFromFormat('!Y-m-d', substr($animalMorte['data_de_saida'], 0, 10)); ?>
            <tr>
              <td><?=$offsetMortes + $indice + 1?></td>
              <td><a style="color:inherit; text-decoration:none;" href="geral.php?pg=animal&amp;id_animal=<?=(int)$animalMorte['id']?>"><?=$hMortes($animalMorte['nome'])?></a></td>
              <td style="white-space:nowrap;"><?=$dataMorte ? $dataMorte->format('d/m/Y') : 'Não informada'?></td>
              <td><?=$hMortes($tipoMorte($animalMorte))?></td>
              <td><?=$hMortes($animalMorte['observacoes_de_saida'] ?? '')?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px; border-top:1px solid #f4f4f4; padding-top:10px;">
        <label for="por-pagina-mortes" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-mortes" class="form-control" style="width:70px;" onchange="var form=document.getElementById('filtros-mortes'); form.elements.por_pagina.value=this.value; form.submit();">
          <?php foreach (array(10, 20, 50, 100) as $limite): ?><option value="<?=$limite?>" <?=$limite === $porPaginaMortes ? 'selected' : ''?>><?=$limite?></option><?php endforeach; ?>
        </select>
        <span class="text-muted">Exibindo <?=$totalMortes ? $offsetMortes + 1 : 0?> a <?=min($offsetMortes + $porPaginaMortes, $totalMortes)?> de <?=$totalMortes?> registros</span>
        <nav aria-label="Páginas do relatório de mortes"><ul class="pagination pagination-sm" style="margin:0;">
          <li class="<?=$paginaMortes === 1 ? 'disabled' : ''?>"><?php if ($paginaMortes > 1): ?><a href="<?=$urlMortes($paginaMortes - 1)?>" aria-label="Página anterior">«</a><?php else: ?><span>«</span><?php endif; ?></li>
          <?php for ($p = 1; $p <= $paginasMortes; $p++):
              if ($p !== 1 && $p !== $paginasMortes && abs($p - $paginaMortes) > 1) { if ($p === 2 || $p === $paginasMortes - 1) echo '<li class="disabled"><span>…</span></li>'; continue; } ?>
          <li class="<?=$p === $paginaMortes ? 'active' : ''?>"><a href="<?=$urlMortes($p)?>" <?=$p === $paginaMortes ? 'aria-current="page"' : ''?>><?=$p?></a></li>
          <?php endfor; ?>
          <li class="<?=$paginaMortes === $paginasMortes ? 'disabled' : ''?>"><?php if ($paginaMortes < $paginasMortes): ?><a href="<?=$urlMortes($paginaMortes + 1)?>" aria-label="Próxima página">»</a><?php else: ?><span>»</span><?php endif; ?></li>
        </ul></nav>
      </div>
    </div>
  </div>
  <?php endif; ?>
</section>
