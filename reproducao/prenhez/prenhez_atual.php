<?php
require_once __DIR__ . '/_prenhez_atual.php';
$entradaFiltros = array();
foreach (array('reproducao', 'data_inicio', 'data_fim') as $campo) {
    $entradaFiltros[$campo] = isset($_GET[$campo]) && is_string($_GET[$campo]) ? trim($_GET[$campo]) : '';
}
$erroFiltros = '';
$mostrarTransplante = $entradaFiltros['reproducao'] === 'te';
try { $prenhezes = consultarPrenhezAtual($entradaFiltros); }
catch (InvalidArgumentException $erro) { $erroFiltros = $erro->getMessage(); $prenhezes = array(); }
$porPagina = filter_var($_GET['por_pagina'] ?? 15, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(15, 30, 50, 100), true)) { $porPagina = 15; }
$total = count($prenhezes);
$totalPaginas = max(1, (int)ceil($total / $porPagina));
$pagina = min($totalPaginas, max(1, (int)filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT)));
$inicio = ($pagina - 1) * $porPagina;
$prenhezesPagina = array_slice($prenhezes, $inicio, $porPagina);
$urlPagina = 'geral.php?' . htmlspecialchars(http_build_query(array_merge(array('pg'=>'prenhez_atual', 'por_pagina'=>$porPagina), $entradaFiltros)), ENT_QUOTES, 'UTF-8') . '&amp;pag=';
$prenhezH = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$prenhezData = function ($valor) {
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', (string)$valor);
    return $data && $data->format('Y-m-d') === $valor ? $data->format('d/m/Y') : '--';
};
?>
<section class="content-header">
  <h1>Prenhez atual</h1>
  <ol class="breadcrumb"><li><i class="fa fa-venus-mars"></i> Reprodução</li><li class="active">Prenhez atual</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;"><div class="box-body">
    <form id="filtros-prenhez" action="geral.php" method="get">
      <input type="hidden" name="pg" value="prenhez_atual">
      <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
      <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group col-sm-6 col-md-3">
          <label for="tipo-prenhez">Tipo de reprodução</label>
          <select id="tipo-prenhez" name="reproducao" class="form-control">
            <?php foreach (array(''=>'Todos', 'monta'=>'Monta natural', 'inseminacao'=>'Inseminação artificial', 'te'=>'Transplante de embriões') as $valor=>$rotulo): ?>
            <option value="<?=$valor?>" <?=$entradaFiltros['reproducao'] === $valor ? 'selected' : ''?>><?=$rotulo?></option>
            <?php endforeach; ?>
          </select>
        </div>
          <div class="form-group col-sm-6 col-md-3"><label for="data-inicio-prenhez">Início do período</label><input type="text" id="data-inicio-prenhez" name="data_inicio" class="form-control" placeholder="dd/mm/aaaa" value="<?=$prenhezH($entradaFiltros['data_inicio'])?>"></div>
        <div class="form-group col-sm-6 col-md-3"><label for="data-fim-prenhez">Fim do período</label><input type="text" id="data-fim-prenhez" name="data_fim" class="form-control" placeholder="dd/mm/aaaa" value="<?=$prenhezH($entradaFiltros['data_fim'])?>"></div>
        <div class="form-group col-sm-6 col-md-3"><button type="submit" formaction="reproducao/prenhez/_imprimir_prenhez_atual.php" formtarget="_blank" class="btn btn-primary"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Gerar PDF</button> <a href="geral.php?pg=prenhez_atual" class="btn btn-default">Limpar</a></div>
      </div>
    </form>
    <p class="text-muted">O período filtra a data inicial do lote, incluindo as duas datas informadas. Na inseminação e no TE, considera a data do procedimento.</p>
    <?php if ($erroFiltros !== ''): ?><div class="alert alert-danger" role="alert"><?=$prenhezH($erroFiltros)?></div><?php endif; ?>
    <p class="text-muted">Fêmeas e receptoras com ultrassom positivo, sem nascimento cadastrado e em lotes iniciados nos últimos 200 dias.</p>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead><tr><th>Tipo reprodução</th><th>Lote</th><?php if ($mostrarTransplante): ?><th>Doadora</th><?php endif; ?><th>Animal/Receptora</th><?php if ($mostrarTransplante): ?><th>Qtd. embriões</th><?php endif; ?><th>Data prevista do nascimento</th><th></th></tr></thead>
        <tbody>
        <?php if (!$prenhezes): ?><tr><td colspan="<?=$mostrarTransplante ? 7 : 5?>" class="text-center">Nenhuma prenhez encontrada com esses critérios.</td></tr><?php endif; ?>
        <?php foreach ($prenhezesPagina as $prenhez): ?>
          <tr>
            <td><?=$prenhezH($prenhez['tipo'])?></td>
            <td><?=$prenhezH($prenhez['codigo'])?> - <?=$prenhezH($prenhez['nome_macho'])?></td>
            <?php if ($mostrarTransplante): ?><td><?=$prenhezH($prenhez['nome_doadora'])?></td><?php endif; ?>
            <td><?=$prenhezH($prenhez['femea'])?><?=!empty($prenhez['terceiro']) ? ' (Terceiro)' : ''?></td>
            <?php if ($mostrarTransplante): ?><td><?=$prenhezH($prenhez['n_embrioes'] ?? '--')?></td><?php endif; ?>
            <td><?=$prenhezData($prenhez['previsao_fim'])?></td>
            <td><a href="geral.php?pg=<?=$prenhezH($prenhez['pagina'])?>&amp;id_lote=<?=(int)$prenhez['lote_id']?>" target="_blank" rel="noopener" title="Abrir lote em nova aba" aria-label="Abrir lote <?=$prenhezH($prenhez['codigo'])?> em nova aba"><i class="fa fa-search" aria-hidden="true"></i></a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
      <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
        <input type="hidden" name="pg" value="prenhez_atual">
        <?php foreach ($entradaFiltros as $campo=>$valor): ?><input type="hidden" name="<?=$campo?>" value="<?=$prenhezH($valor)?>"><?php endforeach; ?>
        <label for="por-pagina-prenhez" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-prenhez" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
          <?php foreach (array(15, 30, 50, 100) as $quantidade): ?>
          <option value="<?=$quantidade?>" <?=$porPagina === $quantidade ? 'selected' : ''?>><?=$quantidade?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <span class="text-muted">Exibindo <?=$total ? $inicio + 1 : 0?> a <?=min($inicio + $porPagina, $total)?> de <?=$total?> registros</span>
      <?php if ($totalPaginas > 1):
          $numeros = array(1, $totalPaginas);
          for ($n = max(1, $pagina - 1); $n <= min($totalPaginas, $pagina + 1); ++$n) { $numeros[] = $n; }
          $numeros = array_unique($numeros); sort($numeros); $anterior = 0;
      ?>
      <nav aria-label="Paginação da prenhez atual"><ul class="pagination pagination-sm no-margin">
        <?php if ($pagina > 1): ?><li><a href="<?=$urlPagina . ($pagina - 1)?>" aria-label="Página anterior">&laquo;</a></li><?php endif; ?>
        <?php foreach ($numeros as $numero): ?>
          <?php if ($anterior && $numero > $anterior + 1): ?><li class="disabled"><span>&hellip;</span></li><?php endif; ?>
          <?php if ($numero === $pagina): ?><li class="active"><span aria-current="page"><?=$numero?></span></li>
          <?php else: ?><li><a href="<?=$urlPagina . $numero?>"><?=$numero?></a></li><?php endif; ?>
        <?php $anterior = $numero; endforeach; ?>
        <?php if ($pagina < $totalPaginas): ?><li><a href="<?=$urlPagina . ($pagina + 1)?>" aria-label="Próxima página">&raquo;</a></li><?php endif; ?>
      </ul></nav>
      <?php endif; ?>
    </div>
  </div></div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var formulario = document.getElementById('filtros-prenhez');
  var temporizador;
  var enviando = false;
  var datas = formulario.querySelectorAll('input[name="data_inicio"], input[name="data_fim"]');
  function atualizarPrenhez() {
    clearTimeout(temporizador);
    temporizador = setTimeout(function () {
      var completas = Array.from(datas).every(function (campo) {
        if (!/\d/.test(campo.value)) { campo.value = ''; }
        return campo.value.trim() === '' || /^\d{2}\/\d{2}\/\d{4}$/.test(campo.value);
      });
      if (completas && !enviando) { enviando = true; formulario.submit(); }
    }, 300);
  }
  formulario.querySelector('select[name="reproducao"]').addEventListener('change', atualizarPrenhez);
  datas.forEach(function (campo) {
    campo.addEventListener('input', atualizarPrenhez);
    campo.addEventListener('change', atualizarPrenhez);
  });
  jQuery(datas).on('changeDate clearDate', atualizarPrenhez);
});
</script>
