<?php
require_once __DIR__ . '/../includes/busca_animais.php';
require_once __DIR__ . '/reprodutor/indicadores_reprodutores.php';
require_once __DIR__ . '/matriz/indicadores_matrizes.php';
$hTip = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$grupoTip = ($_GET['filtro'] ?? '') === 'Matrizes' ? 'Matrizes' : 'Reprodutores';
$matrizesTip = $grupoTip === 'Matrizes';
$camposTip = array('qtd_avaliadas'=>'Crias avaliadas', 'tamanho'=>'Pesagem', 'cabeca'=>'Cabeça', 'pescoco'=>'Pescoço', 'quarto_anterior'=>'Quarto anterior', 'barril'=>'Barril', 'quarto_posterior'=>'Quarto posterior', 'comprimento'=>'Comprimento', 'orgao'=>'Órgão sexual', 'distribuicao'=>'Gordura', 'cobertura'=>'Cobertura', 'cor'=>'Cor', 'conformacao'=>'Conformação');
$ordemTip = filter_var($_GET['tipo'] ?? 1, FILTER_VALIDATE_INT);
if (!$ordemTip || $ordemTip < 1 || $ordemTip > count($camposTip)) $ordemTip = 1;
$campoOrdemTip = array_keys($camposTip)[$ordemTip - 1];
$pesquisaTip = is_string($_GET['animal'] ?? null) ? trim($_GET['animal']) : '';
$idTip = max(0, (int)filter_var($_GET['animal_id'] ?? 0, FILTER_VALIDATE_INT));
$origemTip = is_string($_GET['animal_origem'] ?? null) ? $_GET['animal_origem'] : '';
$situacaoTip = is_string($_GET['situacao'] ?? null) ? $_GET['situacao'] : 'Todos';
if (!in_array($situacaoTip, array('Todos','Rebanho','Mortos/Vendidos'), true)) $situacaoTip = 'Todos';
$porPaginaTip = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaTip, array(10,20,50,100), true)) $porPaginaTip = 10;
$avaliacoesTip = $matrizesTip ? consultarTipificacaoMatrizes() : consultarTipificacaoReprodutores();
$cadastrosTip = DBRead('animais', '', 'id, nome, status') ?: array();
$cadastrosTip = array_column($cadastrosTip, null, 'id');
$registrosTip = array();
foreach ($avaliacoesTip as $itemTip) {
    $idAnimalTip = (int)$itemTip[$matrizesTip ? 'id_femea' : 'id_macho'];
    if (!isset($cadastrosTip[$idAnimalTip])) continue;
    $registrosTip[] = array_merge($itemTip, $cadastrosTip[$idAnimalTip]);
}
$mediasTip = array();
foreach ($camposTip as $campoTip => $rotuloTip) {
    $valoresTip = array();
    foreach ($registrosTip as $itemTip) if ((int)$itemTip['status'] === 0 && isset($itemTip[$campoTip])) $valoresTip[] = (float)$itemTip[$campoTip];
    $mediasTip[$campoTip] = $valoresTip ? array_sum($valoresTip) / count($valoresTip) : null;
}
$registrosTip = array_values(array_filter($registrosTip, function ($item) use ($pesquisaTip, $idTip, $origemTip, $situacaoTip) {
    if ((int)$item['qtd_avaliadas'] < 3) return false;
    if ($idTip && ($origemTip !== 'rebanho' || (int)$item['id'] !== $idTip)) return false;
    if (!$idTip && $pesquisaTip !== '' && mb_stripos($item['nome'], $pesquisaTip, 0, 'UTF-8') === false) return false;
    return $situacaoTip === 'Todos' || ($situacaoTip === 'Rebanho' ? (int)$item['status'] === 0 : (int)$item['status'] >= 1);
}));
usort($registrosTip, function ($a, $b) use ($campoOrdemTip) {
    if (!isset($a[$campoOrdemTip]) || !isset($b[$campoOrdemTip])) return (!isset($a[$campoOrdemTip])) <=> (!isset($b[$campoOrdemTip]));
    return ((float)$b[$campoOrdemTip] <=> (float)$a[$campoOrdemTip]) ?: ((int)$a['id'] <=> (int)$b['id']);
});
$totalTip = count($registrosTip);
$paginasTip = max(1, (int)ceil($totalTip / $porPaginaTip));
$paginaTip = min($paginasTip, max(1, (int)($_GET['pag'] ?? 1)));
$offsetTip = ($paginaTip - 1) * $porPaginaTip;
$urlTip = function ($pagina, $ordem = null) use ($hTip, $grupoTip, $ordemTip, $pesquisaTip, $idTip, $origemTip, $situacaoTip, $porPaginaTip) {
    return $hTip('geral.php?' . http_build_query(array('pg'=>'relatorio_tipificacao', 'filtro'=>$grupoTip, 'tipo'=>$ordem ?? $ordemTip, 'animal'=>$pesquisaTip, 'animal_id'=>$idTip, 'animal_origem'=>$origemTip, 'situacao'=>$situacaoTip, 'por_pagina'=>$porPaginaTip, 'pag'=>$pagina)));
};
$situacoesTip = array(0=>array('Rebanho','#777'),1=>array($matrizesTip ? 'Morta' : 'Morto','#dd4b39'),2=>array($matrizesTip ? 'Vendida' : 'Vendido','#008d4c'),3=>array('Empréstimo','#777'),4=>array('Doação','#777'),5=>array('Abate','#dd4b39'));
?>
<section class="content-header">
  <h1>Relatório de tipificação das crias</h1>
  <ol class="breadcrumb"><li><i class="fa fa-book"></i> Relatórios</li><li class="active">Tipificação</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;"><div class="box-body">
    <form id="filtros-tipificacao" action="geral.php" method="get">
      <input type="hidden" name="pg" value="relatorio_tipificacao">
      <input type="hidden" name="tipo" value="<?=$ordemTip?>">
      <input type="hidden" name="por_pagina" value="<?=$porPaginaTip?>">
      <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group col-sm-6 col-md-3">
          <label for="grupo-tipificacao">Grupo</label>
          <select class="form-control" id="grupo-tipificacao" name="filtro" onchange="this.form.elements.animal.value=''; this.form.elements.animal_id.value=''; this.form.elements.animal_origem.value=''; this.form.submit();">
            <?php foreach (array('Reprodutores','Matrizes') as $grupo): ?><option <?=$grupoTip === $grupo ? 'selected' : ''?>><?=$grupo?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-3">
          <label for="situacao-tipificacao">Situação</label>
          <select class="form-control" id="situacao-tipificacao" name="situacao"><?php foreach (array('Todos','Rebanho','Mortos/Vendidos') as $situacao): ?><option <?=$situacaoTip === $situacao ? 'selected' : ''?>><?=$situacao?></option><?php endforeach; ?></select>
        </div>
        <div class="form-group col-sm-6 col-md-3">
          <?php renderBuscaAnimais(array('id'=>'animal-tipificacao','name'=>'animal','label'=>$matrizesTip ? 'Fêmea' : 'Macho','tipo'=>$matrizesTip ? 'femeas' : 'machos','value'=>$pesquisaTip,'value_id'=>$idTip,'value_origem'=>$origemTip,'limite_origem'=>5,'classe'=>'busca-tipificacao')); ?>
        </div>
        <div class="form-group col-sm-6 col-md-3"><button class="btn btn-primary" type="submit">Pesquisar</button> <a class="btn btn-default" href="geral.php?pg=relatorio_tipificacao">Limpar</a></div>
      </div>
    </form>
  </div></div>
  <div class="box" style="border-top:0;"><div class="box-body">
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:15px;">
      <h2 class="box-title" style="font-size:16px; margin:0;">Tipificação — <?=$grupoTip?></h2>
      <button class="btn btn-link text-muted" type="button" style="padding:0 4px;" data-toggle="collapse" data-target="#dicas-tipificacao" aria-controls="dicas-tipificacao" aria-expanded="false" aria-label="Dicas sobre os indicadores" title="Dicas sobre os indicadores"><i class="fa fa-question-circle" aria-hidden="true"></i></button>
    </div>
    <div class="collapse text-muted" id="dicas-tipificacao"><p>Indicadores calculados em tempo real. Cada cria conta uma vez: segunda avaliação quando disponível, senão primeira; em caso de repetição, o maior ID. Verde indica valor acima da média dos animais do rebanho do grupo selecionado; vermelho, abaixo; iguais ficam neutros. A referência não muda com a pesquisa ou paginação.</p></div>
    <p class="text-muted">Exibindo animais com três ou mais crias avaliadas.</p>
    <div class="table-responsive"><table class="table table-bordered table-striped">
      <thead><tr><th>Animal</th><?php $numeroTip = 0; foreach ($camposTip as $campoTip=>$rotuloTip): $numeroTip++; ?>
        <th <?=$campoTip === $campoOrdemTip ? 'aria-sort="descending"' : ''?>><a style="color:inherit; text-decoration:none;" href="<?=$urlTip(1,$numeroTip)?>"><?=$rotuloTip?><?php if ($campoTip === $campoOrdemTip): ?> <i class="fa fa-sort-desc" aria-hidden="true"></i><?php endif; ?></a></th>
      <?php endforeach; ?></tr></thead>
      <tbody>
        <?php if (!$registrosTip): ?><tr><td colspan="14" class="text-center text-muted" style="padding:30px;">Nenhum animal encontrado para os filtros selecionados.</td></tr><?php endif; ?>
        <?php foreach (array_slice($registrosTip,$offsetTip,$porPaginaTip) as $itemTip): $situacaoAnimalTip=$situacoesTip[(int)$itemTip['status']] ?? array('Não informado','#777'); ?>
        <tr>
          <td style="min-width:220px;"><a style="color:inherit; text-decoration:none;" href="geral.php?pg=animal&amp;id_animal=<?=(int)$itemTip['id']?>"><?=$hTip($itemTip['nome'])?></a> <span style="color:<?=$situacaoAnimalTip[1]?>; white-space:nowrap;">(<?=$hTip($situacaoAnimalTip[0])?>)</span></td>
          <?php foreach ($camposTip as $campoTip=>$rotuloTip):
              $mediaTip=$mediasTip[$campoTip]; $valorTip=isset($itemTip[$campoTip]) ? (float)$itemTip[$campoTip] : null; $corTip='inherit';
              if ($campoTip !== 'qtd_avaliadas' && $mediaTip !== null && $valorTip !== null && round($valorTip,2) !== round($mediaTip,2)) $corTip=$valorTip > $mediaTip ? '#008d4c' : '#dd4b39'; ?>
          <td style="color:<?=$corTip?>;" <?=$campoTip !== 'qtd_avaliadas' ? 'title="' . $hTip($mediaTip === null ? 'Sem média de referência' : 'Média do rebanho: ' . number_format($mediaTip,2,',','.')) . '"' : ''?>><?=$valorTip === null ? 'Não informado' : ($campoTip === 'qtd_avaliadas' ? (int)$valorTip : number_format($valorTip,2,',','.'))?></td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px; border-top:1px solid #f4f4f4; padding-top:10px;">
        <label for="por-pagina-tipificacao" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-tipificacao" class="form-control" style="width:70px;" onchange="var form=document.getElementById('filtros-tipificacao'); form.elements.por_pagina.value=this.value; form.submit();">
          <?php foreach (array(10, 20, 50, 100) as $limite): ?><option value="<?=$limite?>" <?=$limite === $porPaginaTip ? 'selected' : ''?>><?=$limite?></option><?php endforeach; ?>
        </select>
        <span class="text-muted">Exibindo <?=$totalTip ? $offsetTip + 1 : 0?> a <?=min($offsetTip + $porPaginaTip, $totalTip)?> de <?=$totalTip?> registros</span>
        <nav aria-label="Páginas do relatório de tipificacao"><ul class="pagination pagination-sm" style="margin:0;">
          <li class="<?=$paginaTip === 1 ? 'disabled' : ''?>"><?php if ($paginaTip > 1): ?><a href="<?=$urlTip($paginaTip - 1)?>" aria-label="Página anterior">«</a><?php else: ?><span>«</span><?php endif; ?></li>
          <?php for ($p = 1; $p <= $paginasTip; $p++):
              if ($p !== 1 && $p !== $paginasTip && abs($p - $paginaTip) > 1) { if ($p === 2 || $p === $paginasTip - 1) echo '<li class="disabled"><span>…</span></li>'; continue; } ?>
          <li class="<?=$p === $paginaTip ? 'active' : ''?>"><a href="<?=$urlTip($p)?>" <?=$p === $paginaTip ? 'aria-current="page"' : ''?>><?=$p?></a></li>
          <?php endfor; ?>
          <li class="<?=$paginaTip === $paginasTip ? 'disabled' : ''?>"><?php if ($paginaTip < $paginasTip): ?><a href="<?=$urlTip($paginaTip + 1)?>" aria-label="Próxima página">»</a><?php else: ?><span>»</span><?php endif; ?></li>
        </ul></nav>
      </div>
  </div></div>
</section>
<script>
document.addEventListener('buscaanimais:selecionado', function(evento) {
  if (evento.target.classList.contains('busca-tipificacao')) document.getElementById('filtros-tipificacao').submit();
});
</script>
