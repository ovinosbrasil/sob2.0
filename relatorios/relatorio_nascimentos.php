<?php
$hNascimentos = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$filtrosNascimentos = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? $_POST : $_GET;
$data_inicial = is_string($filtrosNascimentos['data_inicial'] ?? null) ? trim($filtrosNascimentos['data_inicial']) : date('01/m/Y');
$data_final = is_string($filtrosNascimentos['data_final'] ?? null) ? trim($filtrosNascimentos['data_final']) : date('t/m/Y');
$lerDataNascimentos = function ($valor) {
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $valor);
    return $data && $data->format('d/m/Y') === $valor ? $data : null;
};
$inicioNascimentos = $lerDataNascimentos($data_inicial);
$fimNascimentos = $lerDataNascimentos($data_final);
$erroNascimentos = '';
$nascimentos = array();
if (!$inicioNascimentos || !$fimNascimentos) {
    $erroNascimentos = 'Informe datas válidas no formato dd/mm/aaaa.';
} elseif ($inicioNascimentos > $fimNascimentos) {
    $erroNascimentos = 'A data final deve ser igual ou posterior à data inicial.';
} else {
    $inicioSql = $inicioNascimentos->format('Y-m-d');
    $fimSql = $fimNascimentos->format('Y-m-d');
    $nascimentos = DBRead('animais', "WHERE data_de_nascimento >= '$inicioSql' AND data_de_nascimento <= '$fimSql' ORDER BY data_de_nascimento ASC, id ASC") ?: array();
}
$tiposNascimentos = array('Monta Natural', 'Inseminação Artificial', 'Embrionagem', 'Não informado');
$tipoNascimento = function ($animal) { return trim($animal['tipo_reproducao'] ?? '') ?: 'Não informado'; };
foreach ($nascimentos as $animalNascimento) {
    $tipoDisponivel = $tipoNascimento($animalNascimento);
    if (!in_array($tipoDisponivel, $tiposNascimentos, true)) $tiposNascimentos[] = $tipoDisponivel;
}
$filtroTipoNascimentos = is_string($filtrosNascimentos['tipo'] ?? null) ? trim($filtrosNascimentos['tipo']) : '';
if ($filtroTipoNascimentos !== '') {
    if (!in_array($filtroTipoNascimentos, $tiposNascimentos, true)) $tiposNascimentos[] = $filtroTipoNascimentos;
    $nascimentos = array_values(array_filter($nascimentos, function ($animal) use ($tipoNascimento, $filtroTipoNascimentos) {
        return $tipoNascimento($animal) === $filtroTipoNascimentos;
    }));
}
$resumoNascimentos = array_fill_keys($tiposNascimentos, 0);
$mortesNascimento = 0;
foreach ($nascimentos as $animalNascimento) {
    $resumoNascimentos[$tipoNascimento($animalNascimento)]++;
    if (($animalNascimento['causa_da_perda'] ?? '') === 'Nascimento') $mortesNascimento++;
}
$totalNascimentos = count($nascimentos);
$resumoNascimentos = array('Mortes no nascimento' => $mortesNascimento) + $resumoNascimentos;
$rotuloTipoNascimento = function ($tipo) { return $tipo === 'Embrionagem' ? 'Transferência de embriões' : $tipo; };
$porPaginaNascimentos = filter_var($filtrosNascimentos['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaNascimentos, array(10, 20, 50, 100), true)) $porPaginaNascimentos = 10;
$paginasNascimentos = max(1, (int)ceil($totalNascimentos / $porPaginaNascimentos));
$paginaNascimentos = min($paginasNascimentos, max(1, (int)($filtrosNascimentos['pag'] ?? 1)));
$offsetNascimentos = ($paginaNascimentos - 1) * $porPaginaNascimentos;
$urlNascimentos = function ($pagina) use ($data_inicial, $data_final, $porPaginaNascimentos, $hNascimentos, $filtroTipoNascimentos) {
    return $hNascimentos('geral.php?' . http_build_query(array('pg' => 'relatorio_nascimentos', 'data_inicial' => $data_inicial, 'data_final' => $data_final, 'por_pagina' => $porPaginaNascimentos, 'pag' => $pagina, 'tipo' => $filtroTipoNascimentos)));
};
?>
<section class="content-header">
  <h1>Relatório de nascimentos</h1>
  <ol class="breadcrumb"><li><i class="fa fa-book"></i> Relatórios</li><li class="active">Nascimentos</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <form id="filtros-nascimentos" action="geral.php" method="get">
        <input type="hidden" name="pg" value="relatorio_nascimentos">
        <input type="hidden" name="por_pagina" value="<?=$porPaginaNascimentos?>">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <?php foreach (array('data_inicial' => 'Data inicial', 'data_final' => 'Data final') as $campo => $rotulo): ?>
          <div class="form-group col-sm-6 col-md-3">
            <label for="<?=$campo?>"><?=$rotulo?><span class="text-danger">*</span></label>
            <div class="input-group date">
              <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
              <input type="text" class="form-control" id="<?=$campo?>" name="<?=$campo?>" value="<?=$hNascimentos($$campo)?>" placeholder="dd/mm/aaaa" maxlength="10" required>
            </div>
          </div>
          <?php endforeach; ?>
          <div class="form-group col-sm-6 col-md-3">
            <label for="tipo-nascimentos">Tipo</label>
            <select class="form-control" id="tipo-nascimentos" name="tipo">
              <option value="">Todos</option>
              <?php foreach ($tiposNascimentos as $opcaoTipo): ?>
              <option value="<?=$hNascimentos($opcaoTipo)?>" <?=$filtroTipoNascimentos === $opcaoTipo ? 'selected' : ''?>><?=$hNascimentos($rotuloTipoNascimento($opcaoTipo))?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <button type="submit" class="btn btn-primary">Pesquisar</button>
            <a class="btn btn-default" href="geral.php?pg=relatorio_nascimentos">Limpar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php if ($erroNascimentos): ?>
  <div class="alert alert-warning" role="alert"><?=$hNascimentos($erroNascimentos)?></div>
  <?php else: ?>
  <div class="box" style="border-top:0;">
    <div class="box-header"><h2 class="box-title" style="font-size:16px;">Resumo do período</h2><span class="pull-right text-muted">Total no período: <strong><?=$totalNascimentos?></strong></span></div>
    <div class="box-body">
      <div class="row">
        <?php foreach ($resumoNascimentos as $causa => $quantidade): ?>
        <div class="col-xs-6 col-sm-4 col-md-2" style="margin-bottom:15px;">
          <div class="text-muted"><?=$hNascimentos($rotuloTipoNascimento($causa))?></div>
          <strong><?=$quantidade?></strong> <span class="text-muted">(<?=number_format($totalNascimentos ? $quantidade * 100 / $totalNascimentos : 0, 2, ',', '.')?>%)</span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;">
        <h2 class="box-title" style="font-size:16px; margin:0;">Nascimentos no período</h2>
        <span class="text-muted"><?=$hNascimentos($data_inicial)?> até <?=$hNascimentos($data_final)?></span>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr><th style="width:60px;">Nº</th><th>Animal</th><th>Nascimento</th><th>Tipo de reprodução</th><th>Morte no nascimento</th></tr></thead>
          <tbody>
            <?php if (!$nascimentos): ?><tr><td colspan="5" class="text-center text-muted" style="padding:30px;">Nenhum nascimento encontrado no período informado. Verifique os filtros selecionados.</td></tr><?php endif; ?>
            <?php foreach (array_slice($nascimentos, $offsetNascimentos, $porPaginaNascimentos) as $indice => $animalNascimento):
                $dataNascimento = DateTimeImmutable::createFromFormat('!Y-m-d', substr($animalNascimento['data_de_nascimento'], 0, 10)); ?>
            <tr>
              <td><?=$offsetNascimentos + $indice + 1?></td>
              <td><a style="color:inherit; text-decoration:none;" href="geral.php?pg=animal&amp;id_animal=<?=(int)$animalNascimento['id']?>"><?=$hNascimentos($animalNascimento['nome'])?></a></td>
              <td style="white-space:nowrap;"><?=$dataNascimento ? $dataNascimento->format('d/m/Y') : 'Não informada'?></td>
              <td><?=$hNascimentos($rotuloTipoNascimento($tipoNascimento($animalNascimento)))?></td>
              <td><?=($animalNascimento['causa_da_perda'] ?? '') === 'Nascimento' ? 'Sim' : 'Não'?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px; border-top:1px solid #f4f4f4; padding-top:10px;">
        <label for="por-pagina-nascimentos" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-nascimentos" class="form-control" style="width:70px;" onchange="var form=document.getElementById('filtros-nascimentos'); form.elements.por_pagina.value=this.value; form.submit();">
          <?php foreach (array(10, 20, 50, 100) as $limite): ?><option value="<?=$limite?>" <?=$limite === $porPaginaNascimentos ? 'selected' : ''?>><?=$limite?></option><?php endforeach; ?>
        </select>
        <span class="text-muted">Exibindo <?=$totalNascimentos ? $offsetNascimentos + 1 : 0?> a <?=min($offsetNascimentos + $porPaginaNascimentos, $totalNascimentos)?> de <?=$totalNascimentos?> registros</span>
        <nav aria-label="Páginas do relatório de nascimentos"><ul class="pagination pagination-sm" style="margin:0;">
          <li class="<?=$paginaNascimentos === 1 ? 'disabled' : ''?>"><?php if ($paginaNascimentos > 1): ?><a href="<?=$urlNascimentos($paginaNascimentos - 1)?>" aria-label="Página anterior">«</a><?php else: ?><span>«</span><?php endif; ?></li>
          <?php for ($p = 1; $p <= $paginasNascimentos; $p++):
              if ($p !== 1 && $p !== $paginasNascimentos && abs($p - $paginaNascimentos) > 1) { if ($p === 2 || $p === $paginasNascimentos - 1) echo '<li class="disabled"><span>…</span></li>'; continue; } ?>
          <li class="<?=$p === $paginaNascimentos ? 'active' : ''?>"><a href="<?=$urlNascimentos($p)?>" <?=$p === $paginaNascimentos ? 'aria-current="page"' : ''?>><?=$p?></a></li>
          <?php endfor; ?>
          <li class="<?=$paginaNascimentos === $paginasNascimentos ? 'disabled' : ''?>"><?php if ($paginaNascimentos < $paginasNascimentos): ?><a href="<?=$urlNascimentos($paginaNascimentos + 1)?>" aria-label="Próxima página">»</a><?php else: ?><span>»</span><?php endif; ?></li>
        </ul></nav>
      </div>
    </div>
  </div>
  <?php endif; ?>
</section>
