<?php
$hExposicao = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$pesquisaExposicao = is_string($_GET['pesquisa'] ?? null) ? trim($_GET['pesquisa']) : '';
$paginaExposicao = filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT);
$paginaExposicao = $paginaExposicao && $paginaExposicao > 0 ? (int)$paginaExposicao : 1;
$porPaginaExposicao = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaExposicao, array(10, 20, 50, 100), true)) $porPaginaExposicao = 10;
$condicaoExposicao = '';
if ($pesquisaExposicao !== '') {
    $termoExposicao = DBEscape($pesquisaExposicao);
    $condicaoExposicao = "WHERE nome LIKE '%$termoExposicao%' OR cidade LIKE '%$termoExposicao%' OR local LIKE '%$termoExposicao%'";
}
$eventosExposicao = DBRead('julgamento', "$condicaoExposicao ORDER BY data DESC, id DESC") ?: array();
$totalExposicao = count($eventosExposicao);
$paginasExposicao = max(1, (int)ceil($totalExposicao / $porPaginaExposicao));
$paginaExposicao = min($paginaExposicao, $paginasExposicao);
$offsetExposicao = ($paginaExposicao - 1) * $porPaginaExposicao;
$eventosPaginaExposicao = array_slice($eventosExposicao, $offsetExposicao, $porPaginaExposicao);
$urlPaginaExposicao = function ($pagina) use ($pesquisaExposicao, $porPaginaExposicao, $hExposicao) {
    return $hExposicao('geral.php?' . http_build_query(array('pg' => 'cadastrar_exposicao', 'pesquisa' => $pesquisaExposicao, 'por_pagina' => $porPaginaExposicao, 'pag' => $pagina)));
};
?>
<section class="content-header">
  <h1>Exposição</h1>
  <ol class="breadcrumb"><li class="active"><i class="fa fa-trophy"></i> Exposição</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-header"><h2 class="box-title" style="font-size:16px;">Cadastrar exposição</h2></div>
    <form method="post" action="exposicao/_cadastrar.php">
      <div class="box-body">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="form-group col-sm-6 col-md-3"><label for="evento">Evento<span class="text-danger">*</span></label><input type="text" class="form-control" id="evento" name="evento" required></div>
          <div class="form-group col-sm-6 col-md-3"><label for="data">Data<span class="text-danger">*</span></label><div class="input-group date"><span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="data" name="data" required maxlength="10" placeholder="dd/mm/aaaa"></div></div>
          <div class="form-group col-sm-6 col-md-2"><label for="cidade">Cidade</label><input type="text" class="form-control" id="cidade" name="cidade"></div>
          <div class="form-group col-sm-6 col-md-2"><label for="local">Local</label><input type="text" class="form-control" id="local" name="local"></div>
          <div class="form-group col-sm-6 col-md-2"><button type="submit" class="btn btn-success btn-block"><i class="fa fa-plus" aria-hidden="true"></i> Cadastrar</button></div>
        </div>
      </div>
    </form>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-header"><h2 class="box-title" style="font-size:16px;">Exposições cadastradas</h2></div>
    <div class="box-body">
      <form id="pesquisa-exposicoes" action="geral.php" method="get" style="margin-bottom:15px;">
        <input type="hidden" name="pg" value="cadastrar_exposicao">
        <input type="hidden" name="por_pagina" value="<?=$porPaginaExposicao?>">
        <div class="row"><div class="form-group col-sm-6 col-md-4" style="margin-bottom:0;"><label for="pesquisa-exposicao">Pesquisar</label><div class="input-group"><input type="search" class="form-control" id="pesquisa-exposicao" name="pesquisa" value="<?=$hExposicao($pesquisaExposicao)?>" placeholder="Evento, cidade ou local"><span class="input-group-btn"><button class="btn btn-primary" type="submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Pesquisar</span></button></span></div></div></div>
      </form>
      <div class="table-responsive"><table class="table table-bordered table-striped">
        <thead><tr><th style="width:60px;">Nº</th><th>Evento</th><th>Data</th><th>Cidade</th><th>Local</th><th style="width:1%;"><span class="sr-only">Abrir</span></th></tr></thead>
        <tbody>
          <?php if (!$eventosPaginaExposicao): ?><tr><td colspan="6" class="text-center text-muted" style="padding:30px;">Nenhuma exposição encontrada.</td></tr><?php endif; ?>
          <?php foreach ($eventosPaginaExposicao as $indiceExposicao => $eventoExposicao): $dataEventoExposicao = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$eventoExposicao['data'], 0, 10)); $urlEventoExposicao = 'geral.php?pg=exposicao&amp;id_exposicao=' . (int)$eventoExposicao['id']; ?>
          <tr style="cursor:pointer;" tabindex="0" role="link" data-url-exposicao="<?=$urlEventoExposicao?>">
            <td><?=$offsetExposicao + $indiceExposicao + 1?></td><td><a href="<?=$urlEventoExposicao?>" style="color:inherit;"><?=$hExposicao($eventoExposicao['nome'])?></a></td><td><?=$dataEventoExposicao ? $dataEventoExposicao->format('d/m/Y') : 'Não informada'?></td><td><?=$hExposicao($eventoExposicao['cidade'])?></td><td><?=$hExposicao($eventoExposicao['local'])?></td><td><a href="<?=$urlEventoExposicao?>" title="Abrir exposição" aria-label="Abrir exposição <?=$hExposicao($eventoExposicao['nome'])?>"><i class="fa fa-search" aria-hidden="true"></i></a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
      <div style="display:flex; flex-wrap:wrap; justify-content:center; align-items:center; gap:16px; border-top:1px solid #f4f4f4; padding-top:10px;">
        <label for="por-pagina-exposicoes" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-exposicoes" class="form-control" style="width:70px;" onchange="var form=document.getElementById('pesquisa-exposicoes'); form.elements.por_pagina.value=this.value; form.submit();"><?php foreach (array(10, 20, 50, 100) as $limiteExposicao): ?><option value="<?=$limiteExposicao?>" <?=$limiteExposicao === $porPaginaExposicao ? 'selected' : ''?>><?=$limiteExposicao?></option><?php endforeach; ?></select>
        <span class="text-muted">Exibindo <?=$totalExposicao ? $offsetExposicao + 1 : 0?> a <?=min($offsetExposicao + $porPaginaExposicao, $totalExposicao)?> de <?=$totalExposicao?> exposições</span>
        <nav aria-label="Páginas das exposições"><ul class="pagination pagination-sm" style="margin:0;">
          <li class="<?=$paginaExposicao === 1 ? 'disabled' : ''?>"><?php if ($paginaExposicao > 1): ?><a href="<?=$urlPaginaExposicao($paginaExposicao - 1)?>">«</a><?php else: ?><span>«</span><?php endif; ?></li>
          <?php for ($p = 1; $p <= $paginasExposicao; $p++): if ($p !== 1 && $p !== $paginasExposicao && abs($p - $paginaExposicao) > 1) { if ($p === 2 || $p === $paginasExposicao - 1) echo '<li class="disabled"><span>…</span></li>'; continue; } ?><li class="<?=$p === $paginaExposicao ? 'active' : ''?>"><a href="<?=$urlPaginaExposicao($p)?>"><?=$p?></a></li><?php endfor; ?>
          <li class="<?=$paginaExposicao === $paginasExposicao ? 'disabled' : ''?>"><?php if ($paginaExposicao < $paginasExposicao): ?><a href="<?=$urlPaginaExposicao($paginaExposicao + 1)?>">»</a><?php else: ?><span>»</span><?php endif; ?></li>
        </ul></nav>
      </div>
    </div>
  </div>
</section>
<script>
(function () {
  'use strict';
  var pesquisa = document.getElementById('pesquisa-exposicao');
  var formulario = document.getElementById('pesquisa-exposicoes');
  var temporizador;
  pesquisa.addEventListener('input', function () { window.clearTimeout(temporizador); temporizador = window.setTimeout(function () { formulario.submit(); }, 500); });
  pesquisa.addEventListener('search', function () { formulario.submit(); });
  document.querySelectorAll('[data-url-exposicao]').forEach(function (linha) {
    linha.addEventListener('click', function (evento) { if (!evento.target.closest('a')) window.location.href = linha.getAttribute('data-url-exposicao').replace('&amp;', '&'); });
    linha.addEventListener('keydown', function (evento) { if (evento.key === 'Enter' || evento.key === ' ') { evento.preventDefault(); window.location.href = linha.getAttribute('data-url-exposicao').replace('&amp;', '&'); } });
  });
})();
</script>
