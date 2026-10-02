<?php
require_once __DIR__ . '/../../includes/busca_animais.php';
$hRep = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$filtro = filter_var($_GET['filtro'] ?? 1, FILTER_VALIDATE_INT);
$ordensRep = array(1 => 'qtd_crias', 2 => 'qtd_avaliadas', 3 => 'venda_geral', 4 => 'nota', 5 => 'qtd_mortes', 6 => 'gmd');
$rotulosRep = array(1 => 'Crias', 2 => 'Crias avaliadas', 4 => 'Qualidade das crias', 3 => 'Média de venda', 5 => 'Morte no nascimento', 6 => 'Ganho de peso');
if (!isset($ordensRep[$filtro])) $filtro = 1;
$filtro2 = is_string($_GET['filtro2'] ?? null) ? $_GET['filtro2'] : 'Todos';
if (!in_array($filtro2, array('Todos', 'Rebanho', 'Mortos/Vendidos'), true)) $filtro2 = 'Todos';
$pesquisaRep = is_string($_GET['macho'] ?? null) ? trim($_GET['macho']) : '';
$machoIdRep = max(0, (int)filter_var($_GET['macho_id'] ?? 0, FILTER_VALIDATE_INT));
$machoOrigemRep = is_string($_GET['macho_origem'] ?? null) && in_array($_GET['macho_origem'], array('rebanho', 'terceiros'), true) ? $_GET['macho_origem'] : '';
$porPaginaRep = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaRep, array(10, 20, 50, 100), true)) $porPaginaRep = 10;
require_once __DIR__ . '/indicadores_reprodutores.php';
$registrosRep = consultarIndicadoresReprodutores();
// Referência fixa: todos os reprodutores do rebanho, antes dos filtros e da paginação.
$mediasRebanhoRep = array();
foreach (array('nota', 'venda_geral', 'qtd_mortes', 'gmd') as $campoMediaRep) {
    $valoresMediaRep = array();
    foreach ($registrosRep as $registroMediaRep) {
        if (!empty($registroMediaRep['animal_encontrado']) && (int)$registroMediaRep['status_animal'] === 0) {
            $valoresMediaRep[] = (float)($registroMediaRep[$campoMediaRep] ?? 0);
        }
    }
    $mediasRebanhoRep[$campoMediaRep] = $valoresMediaRep ? array_sum($valoresMediaRep) / count($valoresMediaRep) : null;
}
$atributosIndicadorRep = function ($campo, $valor) use ($mediasRebanhoRep, $hRep) {
    $media = $mediasRebanhoRep[$campo];
    if ($media === null) return 'title="Sem reprodutores no rebanho para comparação"';
    $fator = $campo === 'gmd' ? 1000 : 1;
    $decimais = $campo === 'qtd_mortes' ? 1 : 2;
    $diferenca = round((float)$valor * $fator, $decimais) - round($media * $fator, $decimais);
    $cor = abs($diferenca) < 0.000001 ? 'inherit' : (($campo === 'qtd_mortes' ? $diferenca < 0 : $diferenca > 0) ? '#008d4c' : '#dd4b39');
    $unidade = $campo === 'gmd' ? ' g/dia' : ($campo === 'qtd_mortes' ? '%' : '');
    $texto = 'Média do rebanho: ' . ($campo === 'venda_geral' ? 'R$ ' : '') . number_format($media * $fator, $decimais, ',', '.') . $unidade;
    return 'style="white-space:nowrap; color:' . $cor . ';" title="' . $hRep($texto) . '"';
};
$registrosRep = array_values(array_filter($registrosRep, function ($registro) use ($filtro2, $pesquisaRep, $machoIdRep, $machoOrigemRep) {
    if (empty($registro['animal_encontrado'])) return false;
    if ($machoIdRep > 0 && ($machoOrigemRep !== 'rebanho' || (int)$registro['id_macho'] !== $machoIdRep)) return false;
    if (!$machoIdRep && $pesquisaRep !== '' && mb_stripos($registro['nome_animal'] ?? '', $pesquisaRep, 0, 'UTF-8') === false) return false;
    return $filtro2 === 'Todos' || ($filtro2 === 'Rebanho' ? (int)$registro['status_animal'] === 0 : (int)$registro['status_animal'] >= 1);
}));
usort($registrosRep, function ($a, $b) use ($filtro, $ordensRep) {
    $campo = $ordensRep[$filtro];
    $ordem = (float)($a[$campo] ?? 0) <=> (float)($b[$campo] ?? 0);
    return ($filtro === 5 ? $ordem : -$ordem) ?: ((int)$a['id'] <=> (int)$b['id']);
});
$situacoesRep = array(0 => array('Rebanho', '#777'), 1 => array('Morto', '#dd4b39'), 2 => array('Vendido', '#008d4c'), 3 => array('Empréstimo', '#777'), 4 => array('Doação', '#777'), 5 => array('Abate', '#dd4b39'));
$totalRep = count($registrosRep);
$paginasRep = max(1, (int)ceil($totalRep / $porPaginaRep));
$paginaRep = min($paginasRep, max(1, (int)($_GET['pag'] ?? 1)));
$offsetRep = ($paginaRep - 1) * $porPaginaRep;
$urlRep = function ($pagina, $ordem = null) use ($filtro, $filtro2, $porPaginaRep, $hRep, $pesquisaRep, $machoIdRep, $machoOrigemRep) {
    return $hRep('geral.php?' . http_build_query(array('pg' => 'relatorio_reprodutores', 'filtro' => $ordem ?? $filtro, 'filtro2' => $filtro2, 'por_pagina' => $porPaginaRep, 'pag' => $pagina, 'macho' => $pesquisaRep, 'macho_id' => $machoIdRep, 'macho_origem' => $machoOrigemRep)));
};
?>
<section class="content-header">
  <h1>Relatório de reprodutores</h1>
  <ol class="breadcrumb"><li><i class="fa fa-book"></i> Relatórios</li><li class="active">Reprodutores</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;"><div class="box-body">
    <form id="filtros-reprodutores" action="geral.php" method="get">
      <input type="hidden" name="pg" value="relatorio_reprodutores">
      <input type="hidden" name="por_pagina" value="<?=$porPaginaRep?>">
      <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group col-sm-6 col-md-3">
          <label for="situacao-reprodutores">Situação</label>
          <select id="situacao-reprodutores" name="filtro2" class="form-control">
            <?php foreach (array('Todos', 'Rebanho', 'Mortos/Vendidos') as $situacaoRep): ?><option value="<?=$hRep($situacaoRep)?>" <?=$filtro2 === $situacaoRep ? 'selected' : ''?>><?=$hRep($situacaoRep)?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-3">
          <label for="ordem-reprodutores">Ordenar por</label>
          <select id="ordem-reprodutores" name="filtro" class="form-control">
            <?php foreach ($rotulosRep as $ordemRep => $rotuloRep): ?><option value="<?=$ordemRep?>" <?=$filtro === $ordemRep ? 'selected' : ''?>><?=$rotuloRep?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-3">
          <?php renderBuscaAnimais(array(
              'id' => 'pesquisa-macho-reprodutores', 'name' => 'macho', 'label' => 'Macho',
              'tipo' => 'machos', 'value' => $pesquisaRep, 'value_id' => $machoIdRep,
              'value_origem' => $machoOrigemRep, 'limite_origem' => 5,
              'classe' => 'busca-macho-relatorio-reprodutores'
          )); ?>
        </div>
        <div class="form-group col-sm-6 col-md-3"><button class="btn btn-primary" type="submit">Pesquisar</button> <a class="btn btn-default" href="geral.php?pg=relatorio_reprodutores">Limpar</a></div>
      </div>
    </form>
  </div></div>
  <div class="box" style="border-top:0;"><div class="box-body">
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:15px;">
      <h2 class="box-title" style="font-size:16px; margin:0;">Desempenho dos reprodutores</h2>
      <button type="button" class="btn btn-link text-muted" style="padding:0 4px;" data-toggle="collapse" data-target="#dicas-reprodutores" aria-controls="dicas-reprodutores" aria-expanded="false" aria-label="Dicas sobre os indicadores" title="Dicas sobre os indicadores"><i class="fa fa-question-circle" aria-hidden="true"></i></button>
    </div>
    <div id="dicas-reprodutores" class="collapse text-muted">
      <ul style="margin-top:10px;">
        <li>Crias: animais que têm o reprodutor como pai no rebanho. Crias avaliadas: crias com tipo maior que zero.</li>
        <li>Qualidade das crias: média do tipo das crias avaliadas.</li>
        <li>Média de venda: média dos valores das vendas das crias desde 01/01/2011.</li>
        <li>Morte no nascimento: crias com causa da perda “Nascimento” divididas pelo total de crias.</li>
        <li>Ganho de peso: média de (peso3 − peso inicial) dividida pelos dias entre o nascimento e a segunda avaliação (data3), em g/dia. Considera apenas pesos positivos e datas com intervalo maior que zero.</li>
        <li>Cores: comparação com a média dos indicadores de todos os reprodutores no rebanho atual, incluindo valores zero, independentemente dos filtros. Verde indica resultado favorável e vermelho, desfavorável; na mortalidade, menor é melhor. Valores iguais ficam neutros. Passe o mouse no valor para consultar a média.</li>
      </ul>
    </div>
    <div class="table-responsive"><table class="table table-bordered table-striped">
      <thead><tr><th>Nº</th><th>Animal</th>
        <?php foreach ($rotulosRep as $ordemRep => $rotuloRep): ?><th <?=$filtro === $ordemRep ? 'aria-sort="' . ($filtro === 5 ? 'ascending' : 'descending') . '"' : ''?>><a style="color:inherit; text-decoration:none;" href="<?=$urlRep(1, $ordemRep)?>"><?=$rotuloRep?><?php if ($filtro === $ordemRep): ?> <i class="fa fa-sort-<?=$filtro === 5 ? 'asc' : 'desc'?>" aria-hidden="true"></i><?php endif; ?></a></th><?php endforeach; ?>
        <th style="width:1%;"><span class="sr-only">Ações</span></th></tr></thead>
      <tbody>
        <?php if (!$registrosRep): ?><tr><td colspan="9" class="text-center text-muted" style="padding:30px;">Nenhum reprodutor encontrado para os filtros selecionados.</td></tr><?php endif; ?>
        <?php foreach (array_slice($registrosRep, $offsetRep, $porPaginaRep) as $indiceRep => $registroRep):
        ?>
        <tr>
          <td><?=$offsetRep + $indiceRep + 1?></td>
          <td style="cursor:pointer;" role="button" tabindex="0" data-relatorio-reprodutor="<?=(int)$registroRep['id_macho']?>" data-nome="<?=$hRep($registroRep['nome_animal'])?>" aria-label="Abrir relatório de <?=$hRep($registroRep['nome_animal'])?>" aria-haspopup="dialog"><?=$hRep($registroRep['nome_animal'])?> <?php $situacaoAnimalRep = $situacoesRep[(int)$registroRep['status_animal']] ?? array('Não informado', '#777'); ?><span style="color:<?=$situacaoAnimalRep[1]?>; white-space:nowrap;">(<?=$hRep($situacaoAnimalRep[0])?>)</span></td>
          <td><?=(int)($registroRep['qtd_crias'] ?? 0)?></td>
          <td><?=(int)($registroRep['qtd_avaliadas'] ?? 0)?></td>
          <td <?=$atributosIndicadorRep('nota', $registroRep['nota'] ?? 0)?>><?=number_format((float)($registroRep['nota'] ?? 0), 2, ',', '.')?></td>
          <td <?=$atributosIndicadorRep('venda_geral', $registroRep['venda_geral'] ?? 0)?>>R$ <?=number_format((float)($registroRep['venda_geral'] ?? 0), 2, ',', '.')?></td>
          <td <?=$atributosIndicadorRep('qtd_mortes', $registroRep['qtd_mortes'] ?? 0)?>><?=number_format((float)($registroRep['qtd_mortes'] ?? 0), 1, ',', '.')?>%</td>
          <td <?=$atributosIndicadorRep('gmd', $registroRep['gmd'] ?? 0)?>><?=number_format((float)($registroRep['gmd'] ?? 0) * 1000, 2, ',', '.')?> g/dia</td>
          <td><button type="button" class="btn btn-link" style="padding:0;" data-relatorio-reprodutor="<?=(int)$registroRep['id_macho']?>" data-nome="<?=$hRep($registroRep['nome_animal'])?>" aria-label="Abrir relatório de <?=$hRep($registroRep['nome_animal'])?>" title="Abrir relatório"><i class="fa fa-search" aria-hidden="true"></i></button></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px; border-top:1px solid #f4f4f4; padding-top:10px;">
        <label for="por-pagina-reprodutores" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-reprodutores" class="form-control" style="width:70px;" onchange="var form=document.getElementById('filtros-reprodutores'); form.elements.por_pagina.value=this.value; form.submit();">
          <?php foreach (array(10, 20, 50, 100) as $limite): ?><option value="<?=$limite?>" <?=$limite === $porPaginaRep ? 'selected' : ''?>><?=$limite?></option><?php endforeach; ?>
        </select>
        <span class="text-muted">Exibindo <?=$totalRep ? $offsetRep + 1 : 0?> a <?=min($offsetRep + $porPaginaRep, $totalRep)?> de <?=$totalRep?> reprodutores</span>
        <nav aria-label="Páginas do relatório de reprodutores"><ul class="pagination pagination-sm" style="margin:0;">
          <li class="<?=$paginaRep === 1 ? 'disabled' : ''?>"><?php if ($paginaRep > 1): ?><a href="<?=$urlRep($paginaRep - 1)?>" aria-label="Página anterior">«</a><?php else: ?><span>«</span><?php endif; ?></li>
          <?php for ($p = 1; $p <= $paginasRep; $p++):
              if ($p !== 1 && $p !== $paginasRep && abs($p - $paginaRep) > 1) { if ($p === 2 || $p === $paginasRep - 1) echo '<li class="disabled"><span>…</span></li>'; continue; } ?>
          <li class="<?=$p === $paginaRep ? 'active' : ''?>"><a href="<?=$urlRep($p)?>" <?=$p === $paginaRep ? 'aria-current="page"' : ''?>><?=$p?></a></li>
          <?php endfor; ?>
          <li class="<?=$paginaRep === $paginasRep ? 'disabled' : ''?>"><?php if ($paginaRep < $paginasRep): ?><a href="<?=$urlRep($paginaRep + 1)?>" aria-label="Próxima página">»</a><?php else: ?><span>»</span><?php endif; ?></li>
        </ul></nav>
      </div>
  </div></div>
</section>

<script>
(function () {
    'use strict';
    var form = document.getElementById('filtros-reprodutores');
    if (!form) return;
    form.elements.filtro2.addEventListener('change', function () { form.submit(); });
    form.elements.filtro.addEventListener('change', function () { form.submit(); });
})();
document.addEventListener('buscaanimais:selecionado', function (evento) {
    if (!evento.target.classList.contains('busca-macho-relatorio-reprodutores')) return;
    document.getElementById('filtros-reprodutores').submit();
});
</script>

<div class="modal fade" id="modal-relatorio-reprodutor" tabindex="-1" role="dialog" aria-labelledby="titulo-modal-reprodutor">
  <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">&times;</button>
      <h4 class="modal-title" id="titulo-modal-reprodutor">Relatório do reprodutor</h4>
    </div>
    <div class="modal-body">
      <ul class="nav nav-tabs" id="abas-modal-reprodutor" role="tablist" aria-label="Indicadores do reprodutor" style="margin-bottom:20px;">
        <?php foreach (array(1 => 'Vendas', 2 => 'Qualidade das crias', 3 => 'Mortalidade', 4 => 'Ganho de peso', 5 => 'Tipificação') as $abaIndicador => $tituloIndicador): ?>
        <li role="presentation" class="<?=$abaIndicador === 1 ? 'active' : ''?>"><a href="#conteudo-modal-reprodutor" id="aba-reprodutor-<?=$abaIndicador?>" role="tab" data-indicador="<?=$abaIndicador?>" aria-controls="conteudo-modal-reprodutor" aria-selected="<?=$abaIndicador === 1 ? 'true' : 'false'?>" tabindex="<?=$abaIndicador === 1 ? '0' : '-1'?>"><?=$tituloIndicador?></a></li>
        <?php endforeach; ?>
      </ul>
      <div id="conteudo-modal-reprodutor" role="tabpanel" aria-labelledby="aba-reprodutor-1" class="table-responsive" aria-live="polite" style="max-height:60vh; overflow:auto;"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button></div>
  </div></div>
</div>
<style>
#conteudo-modal-reprodutor table { width:100% !important; }
#abas-modal-reprodutor { display:flex; flex-wrap:wrap; overflow:visible; }
#abas-modal-reprodutor > li { flex-shrink:0; }
#abas-modal-reprodutor > li > a { color:inherit; }
#abas-modal-reprodutor > li.active > a { border-top:3px solid #00a65a; color:#008d4c; }
</style>
<script src="relatorios/reprodutor/modal_reprodutor.js?v=<?=filemtime(__DIR__ . '/modal_reprodutor.js')?>"></script>
