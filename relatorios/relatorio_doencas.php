<?php
$hDoencas = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$filtrosDoencas = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? $_POST : $_GET;
$data_inicial = is_string($filtrosDoencas['data_inicial'] ?? null) ? trim($filtrosDoencas['data_inicial']) : date('01/m/Y');
$data_final = is_string($filtrosDoencas['data_final'] ?? null) ? trim($filtrosDoencas['data_final']) : date('t/m/Y');
$lerDataDoencas = function ($valor) {
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $valor);
    return $data && $data->format('d/m/Y') === $valor ? $data : null;
};
$inicioDoencas = $lerDataDoencas($data_inicial);
$fimDoencas = $lerDataDoencas($data_final);
$erroDoencas = '';
$doencas = array();
if (!$inicioDoencas || !$fimDoencas) {
    $erroDoencas = 'Informe datas válidas no formato dd/mm/aaaa.';
} elseif ($inicioDoencas > $fimDoencas) {
    $erroDoencas = 'A data final deve ser igual ou posterior à data inicial.';
} else {
    $inicioSql = $inicioDoencas->format('Y-m-d');
    $fimSql = $fimDoencas->format('Y-m-d');
    $doencas = DBRead('doencas d', "LEFT JOIN animais a ON a.id = d.id_animal LEFT JOIN doenca t ON t.id = d.id_doenca WHERE d.data >= '$inicioSql' AND d.data <= '$fimSql' ORDER BY d.data DESC, d.id DESC", 'd.*, a.id AS animal_encontrado, a.nome AS nome_animal, t.nome AS nome_doenca') ?: array();
}
$listaTiposDoencas = DBRead('doenca', 'ORDER BY nome ASC', 'id, nome') ?: array();
$tiposDoencas = array();
foreach ($listaTiposDoencas as $tipoDoenca) $tiposDoencas[(int)$tipoDoenca['id']] = $tipoDoenca['nome'];
foreach ($doencas as $registroDoenca) {
    $idTipoDoenca = (int)$registroDoenca['id_doenca'];
    if (!isset($tiposDoencas[$idTipoDoenca])) $tiposDoencas[$idTipoDoenca] = 'Doença não informada (código ' . $idTipoDoenca . ')';
}
$filtroTipoDoencas = is_string($filtrosDoencas['tipo'] ?? null) ? trim($filtrosDoencas['tipo']) : '';
if ($filtroTipoDoencas !== '') {
    if (!ctype_digit($filtroTipoDoencas)) {
        $erroDoencas = 'Selecione uma doença válida.';
        $doencas = array();
    } else {
        $filtroTipoDoencas = (string)(int)$filtroTipoDoencas;
        if (!isset($tiposDoencas[(int)$filtroTipoDoencas])) $tiposDoencas[(int)$filtroTipoDoencas] = 'Doença não informada (código ' . $filtroTipoDoencas . ')';
        $doencas = array_values(array_filter($doencas, function ($registro) use ($filtroTipoDoencas) {
            return (int)$registro['id_doenca'] === (int)$filtroTipoDoencas;
        }));
    }
}
$animaisDoencas = array();
$resumoDoencas = array();
foreach ($doencas as $registroDoenca) {
    $idResumoDoenca = (int)$registroDoenca['id_doenca'];
    $resumoDoencas[$idResumoDoenca] = ($resumoDoencas[$idResumoDoenca] ?? 0) + 1;
    if (!empty($registroDoenca['id_animal'])) $animaisDoencas[(int)$registroDoenca['id_animal']] = true;
}
$totalDoencas = count($doencas);
$porPaginaDoencas = filter_var($filtrosDoencas['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaDoencas, array(10, 20, 50, 100), true)) $porPaginaDoencas = 10;
$paginasDoencas = max(1, (int)ceil($totalDoencas / $porPaginaDoencas));
$paginaDoencas = min($paginasDoencas, max(1, (int)($filtrosDoencas['pag'] ?? 1)));
$offsetDoencas = ($paginaDoencas - 1) * $porPaginaDoencas;
$urlDoencas = function ($pagina) use ($data_inicial, $data_final, $porPaginaDoencas, $hDoencas, $filtroTipoDoencas) {
    return $hDoencas('geral.php?' . http_build_query(array('pg' => 'relatorio_doencas', 'data_inicial' => $data_inicial, 'data_final' => $data_final, 'por_pagina' => $porPaginaDoencas, 'pag' => $pagina, 'tipo' => $filtroTipoDoencas)));
};
?>
<section class="content-header">
  <h1>Relatório de doenças</h1>
  <ol class="breadcrumb"><li><i class="fa fa-book"></i> Relatórios</li><li class="active">Doenças</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <form id="filtros-doencas" action="geral.php" method="get">
        <input type="hidden" name="pg" value="relatorio_doencas">
        <input type="hidden" name="por_pagina" value="<?=$porPaginaDoencas?>">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <?php foreach (array('data_inicial' => 'Data inicial', 'data_final' => 'Data final') as $campo => $rotulo): ?>
          <div class="form-group col-sm-6 col-md-3">
            <label for="<?=$campo?>"><?=$rotulo?><span class="text-danger">*</span></label>
            <div class="input-group date">
              <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
              <input type="text" class="form-control" id="<?=$campo?>" name="<?=$campo?>" value="<?=$hDoencas($$campo)?>" placeholder="dd/mm/aaaa" maxlength="10" required>
            </div>
          </div>
          <?php endforeach; ?>
          <div class="form-group col-sm-6 col-md-3">
            <label for="tipo-doencas">Doença</label>
            <select class="form-control" id="tipo-doencas" name="tipo">
              <option value="">Todas</option>
              <?php foreach ($tiposDoencas as $idTipo => $opcaoTipo): ?>
              <option value="<?=$idTipo?>" <?=$filtroTipoDoencas === (string)$idTipo ? 'selected' : ''?>><?=$hDoencas($opcaoTipo)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <button type="submit" class="btn btn-primary">Pesquisar</button>
            <a class="btn btn-default" href="geral.php?pg=relatorio_doencas">Limpar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php if ($erroDoencas): ?>
  <div class="alert alert-warning" role="alert"><?=$hDoencas($erroDoencas)?></div>
  <?php else: ?>
  <div class="box" style="border-top:0;">
    <div class="box-header"><h2 class="box-title" style="font-size:16px;">Resumo do período</h2></div>
    <div class="box-body">
      <div class="row">
        <?php foreach ($resumoDoencas as $idResumoDoenca => $quantidadeDoenca): ?>
        <div class="col-xs-6 col-sm-4 col-md-2" style="margin-bottom:15px;">
          <div class="text-muted"><?=$hDoencas($tiposDoencas[$idResumoDoenca])?></div>
          <strong><?=$quantidadeDoenca?></strong> <span class="text-muted">(<?=number_format($totalDoencas ? $quantidadeDoenca * 100 / $totalDoencas : 0, 2, ',', '.')?>%)</span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (!$resumoDoencas): ?><p class="text-muted">Sem ocorrências para os filtros selecionados.</p><?php endif; ?>
    </div>
  </div>
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;">
        <h2 class="box-title" style="font-size:16px; margin:0;">Doenças no período</h2>
        <span class="text-muted"><?=$hDoencas($data_inicial)?> até <?=$hDoencas($data_final)?></span>
      </div>
      <p class="text-muted"><?=$totalDoencas?> ocorrência(s) em <?=count($animaisDoencas)?> animal(is) no período e filtros selecionados.</p>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr><th style="width:60px;">Nº</th><th>Animal</th><th>Doença</th><th>Data</th><th>Observações</th></tr></thead>
          <tbody>
            <?php if (!$doencas): ?><tr><td colspan="5" class="text-center text-muted" style="padding:30px;">Nenhuma ocorrência de doença encontrada no período informado. Verifique os filtros selecionados.</td></tr><?php endif; ?>
            <?php foreach (array_slice($doencas, $offsetDoencas, $porPaginaDoencas) as $indice => $animalDoenca):
                $dataDoenca = DateTimeImmutable::createFromFormat('!Y-m-d', substr($animalDoenca['data'], 0, 10)); ?>
            <tr>
              <td><?=$offsetDoencas + $indice + 1?></td>
              <td><?php if (!empty($animalDoenca['animal_encontrado'])): ?><a style="color:inherit; text-decoration:none;" href="geral.php?pg=animal&amp;id_animal=<?=(int)$animalDoenca['id_animal']?>"><?=$hDoencas($animalDoenca['nome_animal'])?></a><?php else: ?>Animal não encontrado<?php endif; ?></td>
              <td><?=$hDoencas($tiposDoencas[(int)$animalDoenca['id_doenca']] ?? 'Não informada')?></td>
              <td style="white-space:nowrap;"><?=$dataDoenca ? $dataDoenca->format('d/m/Y') : 'Não informada'?></td>
              <td><?=$hDoencas($animalDoenca['obs'] ?? '')?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px; border-top:1px solid #f4f4f4; padding-top:10px;">
        <label for="por-pagina-doencas" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-doencas" class="form-control" style="width:70px;" onchange="var form=document.getElementById('filtros-doencas'); form.elements.por_pagina.value=this.value; form.submit();">
          <?php foreach (array(10, 20, 50, 100) as $limite): ?><option value="<?=$limite?>" <?=$limite === $porPaginaDoencas ? 'selected' : ''?>><?=$limite?></option><?php endforeach; ?>
        </select>
        <span class="text-muted">Exibindo <?=$totalDoencas ? $offsetDoencas + 1 : 0?> a <?=min($offsetDoencas + $porPaginaDoencas, $totalDoencas)?> de <?=$totalDoencas?> registros</span>
        <nav aria-label="Páginas do relatório de doenças"><ul class="pagination pagination-sm" style="margin:0;">
          <li class="<?=$paginaDoencas === 1 ? 'disabled' : ''?>"><?php if ($paginaDoencas > 1): ?><a href="<?=$urlDoencas($paginaDoencas - 1)?>" aria-label="Página anterior">«</a><?php else: ?><span>«</span><?php endif; ?></li>
          <?php for ($p = 1; $p <= $paginasDoencas; $p++):
              if ($p !== 1 && $p !== $paginasDoencas && abs($p - $paginaDoencas) > 1) { if ($p === 2 || $p === $paginasDoencas - 1) echo '<li class="disabled"><span>…</span></li>'; continue; } ?>
          <li class="<?=$p === $paginaDoencas ? 'active' : ''?>"><a href="<?=$urlDoencas($p)?>" <?=$p === $paginaDoencas ? 'aria-current="page"' : ''?>><?=$p?></a></li>
          <?php endfor; ?>
          <li class="<?=$paginaDoencas === $paginasDoencas ? 'disabled' : ''?>"><?php if ($paginaDoencas < $paginasDoencas): ?><a href="<?=$urlDoencas($paginaDoencas + 1)?>" aria-label="Próxima página">»</a><?php else: ?><span>»</span><?php endif; ?></li>
        </ul></nav>
      </div>
    </div>
  </div>
  <?php endif; ?>
</section>
