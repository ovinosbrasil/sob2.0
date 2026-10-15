<?php
require_once __DIR__ . '/_selecao_femeas.php';
$hSelecao = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$entradaSelecao = array();
foreach (array('tipo','idade_inicio','idade_fim','por_pagina','pag') as $campo) {
    $entradaSelecao[$campo] = is_string($_GET[$campo] ?? null) ? trim($_GET[$campo]) : '';
}
if ($entradaSelecao['tipo'] === '') $entradaSelecao['tipo'] = 'sem_cria';
$tiposSelecao = array('sem_cria'=>'Sem cria', 'sem_lote_reproducao'=>'Sem lote de reprodução', 'gmd_acima_media'=>'KG apartado acima da média', 'gmd_abaixo_media'=>'KG apartado abaixo da média', 'crias_tipo_2'=>'Crias tipo 2', 'cria_morte_nascimento'=>'Cria com morte nascimento', 'duas_crias_morte_nascimento'=>'Duas crias seguidas com morte nascimento');
$exibirGmdSelecao = in_array($entradaSelecao['tipo'], array('gmd_acima_media','gmd_abaixo_media'), true);
$erroSelecao = ''; $selecao = null;
try { $selecao = consultarSelecaoFemeas($entradaSelecao); }
catch (InvalidArgumentException $erro) { $erroSelecao = $erro->getMessage(); }
$parametrosSelecao = array('pg'=>'selecao_femeas', 'tipo'=>$entradaSelecao['tipo'], 'idade_inicio'=>$entradaSelecao['idade_inicio'], 'idade_fim'=>$entradaSelecao['idade_fim'], 'por_pagina'=>$selecao['por_pagina'] ?? 10);
$urlSelecao = function ($pagina) use ($parametrosSelecao, $hSelecao) { return $hSelecao('geral.php?' . http_build_query($parametrosSelecao + array('pag'=>$pagina))); };
?>
<section class="content-header">
  <h1>Seleção de Fêmeas</h1>
  <ol class="breadcrumb"><li><i class="fa fa-book"></i> Relatórios</li><li class="active">Seleção de Fêmeas</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;"><div class="box-body">
    <form id="filtros-selecao-femeas" action="geral.php" method="get">
      <input type="hidden" name="pg" value="selecao_femeas">
      <input type="hidden" name="por_pagina" value="<?=$selecao['por_pagina'] ?? 10?>">
      <div class="row" style="display:flex;flex-wrap:wrap;align-items:flex-end;">
        <div class="form-group col-sm-6 col-md-3"><label for="tipo-selecao-femeas">Tipo</label><select id="tipo-selecao-femeas" name="tipo" class="form-control"><?php foreach ($tiposSelecao as $valor=>$rotulo): ?><option value="<?=$valor?>" <?=$entradaSelecao['tipo'] === $valor ? 'selected' : ''?>><?=$rotulo?></option><?php endforeach; ?></select></div>
        <div class="form-group col-sm-6 col-md-3"><label for="idade-inicio-selecao">Idade inicial (meses)<span class="text-danger">*</span></label><input type="number" class="form-control" id="idade-inicio-selecao" name="idade_inicio" min="0" step="1" required value="<?=$hSelecao($entradaSelecao['idade_inicio'])?>"></div>
        <div class="form-group col-sm-6 col-md-3"><label for="idade-fim-selecao">Idade final (meses)</label><input type="number" class="form-control" id="idade-fim-selecao" name="idade_fim" min="0" step="1" placeholder="Sem limite" value="<?=$hSelecao($entradaSelecao['idade_fim'])?>"></div>
        <div class="form-group col-sm-6 col-md-3"><a class="btn btn-default" href="geral.php?pg=selecao_femeas">Limpar</a><noscript> <button type="submit" class="btn btn-primary">Filtrar</button></noscript></div>
      </div>
    </form>
    <p class="text-muted" style="margin-bottom:0;">Fêmeas ativas do rebanho. Idade em meses completos, incluindo os limites informados. Idade final vazia significa sem limite máximo.
      <?php if ($entradaSelecao['tipo'] === 'sem_lote_reproducao'): ?>Sem vínculo com lotes de monta ou inseminação, nem como doadora em transplante de embriões. Considera também lotes históricos.<?php elseif ($exibirGmdSelecao): ?>KG apartado = peso 2 − peso inicial. Lista fêmeas com kg apartado <?=$entradaSelecao['tipo'] === 'gmd_abaixo_media' ? 'menor' : 'maior'?> que a média individual de kg apartado dos machos e fêmeas ativos, sem limitar a média pela idade informada. Desconsidera pesos ausentes ou zerados, datas futuras e intervalos inválidos.<?php elseif ($entradaSelecao['tipo'] === 'duas_crias_morte_nascimento'): ?>Fêmeas cujas duas crias mais recentes morreram com causa da perda Nascimento. Ordem pela data de nascimento e, em caso de empate, pelo maior ID.<?php elseif ($entradaSelecao['tipo'] === 'cria_morte_nascimento'): ?>Fêmeas com pelo menos uma cria morta com causa da perda Nascimento.<?php elseif ($entradaSelecao['tipo'] === 'crias_tipo_2'): ?>Fêmeas com pelo menos uma cria cadastrada como tipo 2, incluindo crias mortas ou vendidas.<?php else: ?>Sem crias cadastradas. Crias mortas ou vendidas também contam no histórico da mãe.<?php endif; ?></p>
  </div></div>
  <?php if ($erroSelecao !== ''): ?>
    <div class="alert alert-warning" role="alert"><?=$hSelecao($erroSelecao)?></div>
  <?php elseif (!$selecao['pronto']): ?>
    <div class="box" style="border-top:0;"><div class="box-body text-center text-muted" style="padding:30px;">Informe a idade inicial para gerar a lista. A idade final é opcional.</div></div>
  <?php else: ?>
  <div class="box" style="border-top:0;"><div class="box-body">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;"><h2 class="box-title" style="font-size:16px;margin:0;"><?=$hSelecao($tiposSelecao[$entradaSelecao['tipo']])?><?php if ($exibirGmdSelecao): ?> — Média do rebanho: <?=$selecao['media_rebanho'] !== null ? number_format($selecao['media_rebanho'], 3, ',', '.') . ' kg' : 'sem pesagens válidas'?><?php endif; ?></h2><span class="text-muted"><?=$selecao['total']?> fêmea(s)</span></div>
    <div class="table-responsive"><table class="table table-bordered table-striped">
      <thead><tr><th style="width:60px;">Nº</th><th>Animal</th><th>FBB</th><th>Tatuagem</th><th>Nascimento</th><th>Idade (A/M)</th><?php if ($exibirGmdSelecao): ?><th>KG apartado (kg)</th><?php endif; ?><th style="width:1%;"><span class="sr-only">Abrir animal</span></th></tr></thead>
      <tbody>
      <?php if (!$selecao['registros']): ?><tr><td colspan="<?=$exibirGmdSelecao ? 8 : 7?>" class="text-center text-muted" style="padding:30px;">Nenhuma fêmea encontrada para os filtros selecionados.</td></tr><?php endif; ?>
      <?php foreach ($selecao['registros'] as $indice=>$femeaSelecao):
          $urlAnimalSelecao = 'geral.php?pg=animal&amp;id_animal=' . (int)$femeaSelecao['id'];
          $dataSelecao = DateTimeImmutable::createFromFormat('!Y-m-d', $femeaSelecao['data_de_nascimento']);
      ?>
        <tr><td><?=$selecao['inicio']+$indice+1?></td><td><a href="<?=$urlAnimalSelecao?>" target="_blank" rel="noopener" style="color:inherit;"><?=$hSelecao($femeaSelecao['nome'])?></a></td><td><?=$hSelecao($femeaSelecao['fbb'] ?: '—')?></td><td><?=$hSelecao($femeaSelecao['tatuagem'] ?: '—')?></td><td><?=$dataSelecao ? $dataSelecao->format('d/m/Y') : '—'?></td><td><?=intdiv((int)$femeaSelecao['idade_meses'], 12)?>A <?=(int)$femeaSelecao['idade_meses'] % 12?>M</td><?php if ($exibirGmdSelecao): ?><td><?=number_format((float)$femeaSelecao['gmd_medio'], 3, ',', '.')?></td><?php endif; ?><td><a href="<?=$urlAnimalSelecao?>" target="_blank" rel="noopener" title="Abrir animal" aria-label="Abrir <?=$hSelecao($femeaSelecao['nome'])?>"><i class="fa fa-search" aria-hidden="true"></i></a></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <div style="display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:16px;border-top:1px solid #f4f4f4;padding-top:10px;">
      <form action="geral.php" method="get" style="display:flex;align-items:center;gap:8px;margin:0;">
        <?php foreach ($parametrosSelecao as $campo=>$valor): if ($campo === 'por_pagina') continue; ?><input type="hidden" name="<?=$campo?>" value="<?=$hSelecao($valor)?>"><?php endforeach; ?>
        <label for="por-pagina-selecao" style="font-weight:normal;margin:0;">Por página</label><select id="por-pagina-selecao" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
          <?php foreach (array(10,20,50,100) as $quantidade): ?><option value="<?=$quantidade?>" <?=$selecao['por_pagina'] === $quantidade ? 'selected' : ''?>><?=$quantidade?></option><?php endforeach; ?>
        </select>
      </form>
      <span class="text-muted">Exibindo <?=$selecao['total'] ? $selecao['inicio']+1 : 0?> a <?=min($selecao['inicio']+$selecao['por_pagina'],$selecao['total'])?> de <?=$selecao['total']?> fêmeas</span>
      <?php if ($selecao['paginas'] > 1):
          $visiveis = array(1,$selecao['paginas']);
          for ($n=max(1,$selecao['pagina']-1);$n<=min($selecao['paginas'],$selecao['pagina']+1);$n++) $visiveis[]=$n;
          $visiveis=array_unique($visiveis);sort($visiveis);$anterior=0;
      ?>
      <nav aria-label="Páginas da seleção de fêmeas"><ul class="pagination pagination-sm no-margin">
        <?php if ($selecao['pagina'] > 1): ?><li><a href="<?=$urlSelecao($selecao['pagina']-1)?>" aria-label="Página anterior">«</a></li><?php endif; ?>
        <?php foreach ($visiveis as $numero): ?>
          <?php if ($anterior && $numero>$anterior+1): ?><li class="disabled"><span>…</span></li><?php endif; ?>
          <li <?=$numero === $selecao['pagina'] ? 'class="active"' : ''?>><?php if ($numero === $selecao['pagina']): ?><span aria-current="page"><?=$numero?></span><?php else: ?><a href="<?=$urlSelecao($numero)?>"><?=$numero?></a><?php endif; ?></li>
        <?php $anterior=$numero;endforeach; ?>
        <?php if ($selecao['pagina']<$selecao['paginas']): ?><li><a href="<?=$urlSelecao($selecao['pagina']+1)?>" aria-label="Próxima página">»</a></li><?php endif; ?>
      </ul></nav>
      <?php endif; ?>
    </div>
  </div></div>
  <?php endif; ?>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var formulario = document.getElementById('filtros-selecao-femeas');
  var temporizador, enviando = false;
  function atualizarSelecaoFemeas() {
    window.clearTimeout(temporizador);
    temporizador = window.setTimeout(function () {
      if (!enviando && formulario.elements.idade_inicio.value.trim() !== '') {
        enviando = true;
        formulario.submit();
      }
    }, 400);
  }
  formulario.querySelectorAll('select, input[type="number"]').forEach(function (campo) {
    campo.addEventListener('change', atualizarSelecaoFemeas);
    campo.addEventListener('input', atualizarSelecaoFemeas);
  });
});
</script>
