<?php
$hReproducao = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$tiposReproducao = array(
    'Monta natural' => array('tipo' => 0, 'pagina' => 'monta', 'tituloQuantidade' => 'Fêmeas'),
    'Inseminação artificial' => array('tipo' => 1, 'pagina' => 'inseminacao', 'tituloQuantidade' => 'Fêmeas'),
    'Transplante de embriões' => array('tipo' => 2, 'pagina' => 'te', 'tituloQuantidade' => 'Receptoras')
);
$filtro = is_string($_GET['filtro'] ?? null) ? $_GET['filtro'] : 'Monta natural';
if (!isset($tiposReproducao[$filtro])) { $filtro = 'Monta natural'; }
$configuracaoReproducao = $tiposReproducao[$filtro];
$ordensReproducao = array(0 => 'id', 1 => 'femeas', 2 => 'ultrassom', 3 => 'vivos', 4 => 'mortes');
$rotulosOrdemReproducao = array(0 => 'Mais recentes', 1 => $configuracaoReproducao['tituloQuantidade'], 2 => 'Ultrassom', 3 => 'Crias vivas', 4 => 'Mortes no nascimento');
$ordemReproducao = filter_var($_GET['filtro2'] ?? 0, FILTER_VALIDATE_INT);
if (!array_key_exists($ordemReproducao, $ordensReproducao)) { $ordemReproducao = 0; }
$pesquisaReproducao = is_string($_GET['pesquisa'] ?? null) ? trim($_GET['pesquisa']) : '';
$porPaginaReproducao = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaReproducao, array(10, 20, 50, 100), true)) { $porPaginaReproducao = 10; }

function dataRelatorioReproducao($valor)
{
    $valor = substr((string)$valor, 0, 10);
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
    return $data && $data->format('Y-m-d') === $valor ? $data->format('d/m/Y') : 'Não informada';
}

function registrosRelatorioReproducao($filtro)
{
    if ($filtro === 'Monta natural') {
        $campos = "lr.*, b.codigo, b.data_inicio, b.data_fim,
            COALESCE(NULLIF(a.nome, ''), NULLIF(t.nome, ''), 'Animal não encontrado') AS nome_macho,
            (SELECT COUNT(DISTINCT cria.id)
             FROM monta_controle controle
             INNER JOIN animais cria ON cria.mae = controle.id_animal
                AND COALESCE(cria.terceiro_mae, 0) = COALESCE(controle.terceiro, 0)
                AND cria.pai = b.id_animal
                AND COALESCE(cria.terceiro_pai, 0) = COALESCE(b.terceiro, 0)
                AND cria.tipo_reproducao = 'Monta Natural'
                AND cria.data_de_nascimento BETWEEN DATE_ADD(b.data_inicio, INTERVAL 120 DAY) AND DATE_ADD(b.data_fim, INTERVAL 180 DAY)
             WHERE controle.id_monta = b.id
                AND NOT (COALESCE(cria.status, 0) = 1 AND LOWER(TRIM(COALESCE(cria.causa_da_perda, ''))) = 'nascimento')) AS vivos,
            (SELECT COUNT(DISTINCT cria.id)
             FROM monta_controle controle
             INNER JOIN animais cria ON cria.mae = controle.id_animal
                AND COALESCE(cria.terceiro_mae, 0) = COALESCE(controle.terceiro, 0)
                AND cria.pai = b.id_animal
                AND COALESCE(cria.terceiro_pai, 0) = COALESCE(b.terceiro, 0)
                AND cria.tipo_reproducao = 'Monta Natural'
                AND cria.data_de_nascimento BETWEEN DATE_ADD(b.data_inicio, INTERVAL 120 DAY) AND DATE_ADD(b.data_fim, INTERVAL 180 DAY)
             WHERE controle.id_monta = b.id
                AND COALESCE(cria.status, 0) = 1 AND LOWER(TRIM(COALESCE(cria.causa_da_perda, ''))) = 'nascimento') AS mortes";
        $juncao = "INNER JOIN monta b ON b.id = lr.id_lote
            LEFT JOIN animais a ON a.id = b.id_animal AND b.terceiro = 0
            LEFT JOIN terceiros t ON t.id = b.id_animal AND b.terceiro = 1
            WHERE lr.tipo = 0";
    } elseif ($filtro === 'Inseminação artificial') {
        $campos = "lr.*, b.codigo, b.data AS data_inicio, NULL AS data_fim,
            COALESCE(NULLIF(a.nome, ''), NULLIF(t.nome, ''), 'Animal não encontrado') AS nome_macho,
            (SELECT COUNT(DISTINCT cria.id)
             FROM inseminacao_controle controle
             INNER JOIN animais cria ON cria.mae = controle.id_femea
                AND COALESCE(cria.terceiro_mae, 0) = COALESCE(controle.terceiro, 0)
                AND cria.pai = b.id_macho
                AND COALESCE(cria.terceiro_pai, 0) = COALESCE(b.terceiro, 0)
                AND cria.tipo_reproducao = 'Inseminação Artificial'
                AND cria.data_de_nascimento BETWEEN DATE_ADD(b.data, INTERVAL 120 DAY) AND DATE_ADD(b.data, INTERVAL 180 DAY)
             WHERE controle.id_lote = b.id
                AND NOT (COALESCE(cria.status, 0) = 1 AND LOWER(TRIM(COALESCE(cria.causa_da_perda, ''))) = 'nascimento')) AS vivos,
            (SELECT COUNT(DISTINCT cria.id)
             FROM inseminacao_controle controle
             INNER JOIN animais cria ON cria.mae = controle.id_femea
                AND COALESCE(cria.terceiro_mae, 0) = COALESCE(controle.terceiro, 0)
                AND cria.pai = b.id_macho
                AND COALESCE(cria.terceiro_pai, 0) = COALESCE(b.terceiro, 0)
                AND cria.tipo_reproducao = 'Inseminação Artificial'
                AND cria.data_de_nascimento BETWEEN DATE_ADD(b.data, INTERVAL 120 DAY) AND DATE_ADD(b.data, INTERVAL 180 DAY)
             WHERE controle.id_lote = b.id
                AND COALESCE(cria.status, 0) = 1 AND LOWER(TRIM(COALESCE(cria.causa_da_perda, ''))) = 'nascimento') AS mortes";
        $juncao = "INNER JOIN inseminacao b ON b.id = lr.id_lote
            LEFT JOIN animais a ON a.id = b.id_macho AND b.terceiro = 0
            LEFT JOIN terceiros t ON t.id = b.id_macho AND b.terceiro = 1
            WHERE lr.tipo = 1";
    } else {
        $campos = "lr.*, b.codigo, b.data AS data_inicio, NULL AS data_fim,
            COALESCE(NULLIF(ap.nome, ''), NULLIF(tp.nome, ''), NULLIF(b.pai, ''), 'Animal não encontrado') AS nome_macho,
            COALESCE(NULLIF(am.nome, ''), NULLIF(tm.nome, ''), NULLIF(b.mae, ''), 'Animal não encontrado') AS nome_femea,
            (SELECT COUNT(DISTINCT cria.id) FROM animais cria
             WHERE cria.pai = b.id_pai AND COALESCE(cria.terceiro_pai, 0) = COALESCE(b.terceiro_pai, 0)
                AND cria.mae = b.id_mae AND COALESCE(cria.terceiro_mae, 0) = COALESCE(b.terceiro_mae, 0)
                AND cria.tipo_reproducao = 'Embrionagem'
                AND cria.data_de_nascimento BETWEEN DATE_ADD(b.data, INTERVAL 120 DAY) AND DATE_ADD(b.data, INTERVAL 180 DAY)
                AND NOT (COALESCE(cria.status, 0) = 1 AND LOWER(TRIM(COALESCE(cria.causa_da_perda, ''))) = 'nascimento')) AS vivos,
            (SELECT COUNT(DISTINCT cria.id) FROM animais cria
             WHERE cria.pai = b.id_pai AND COALESCE(cria.terceiro_pai, 0) = COALESCE(b.terceiro_pai, 0)
                AND cria.mae = b.id_mae AND COALESCE(cria.terceiro_mae, 0) = COALESCE(b.terceiro_mae, 0)
                AND cria.tipo_reproducao = 'Embrionagem'
                AND cria.data_de_nascimento BETWEEN DATE_ADD(b.data, INTERVAL 120 DAY) AND DATE_ADD(b.data, INTERVAL 180 DAY)
                AND COALESCE(cria.status, 0) = 1 AND LOWER(TRIM(COALESCE(cria.causa_da_perda, ''))) = 'nascimento') AS mortes";
        $juncao = "INNER JOIN transplante b ON b.id = lr.id_lote
            LEFT JOIN animais ap ON ap.id = b.id_pai AND b.terceiro_pai = 0
            LEFT JOIN terceiros tp ON tp.id = b.id_pai AND b.terceiro_pai = 1
            LEFT JOIN animais am ON am.id = b.id_mae AND b.terceiro_mae = 0
            LEFT JOIN terceiros tm ON tm.id = b.id_mae AND b.terceiro_mae = 1
            WHERE lr.tipo = 2";
    }
    return DBRead('lotes_reproducao lr', $juncao, $campos) ?: array();
}

$registrosReproducao = registrosRelatorioReproducao($filtro);
if ($pesquisaReproducao !== '') {
    $registrosReproducao = array_values(array_filter($registrosReproducao, function ($registro) use ($pesquisaReproducao) {
        $texto = ($registro['codigo'] ?? '') . ' ' . ($registro['nome_macho'] ?? '') . ' ' . ($registro['nome_femea'] ?? '');
        return mb_stripos($texto, $pesquisaReproducao, 0, 'UTF-8') !== false;
    }));
}
$campoOrdemReproducao = $ordensReproducao[$ordemReproducao];
usort($registrosReproducao, function ($a, $b) use ($campoOrdemReproducao) {
    return ((float)($b[$campoOrdemReproducao] ?? 0) <=> (float)($a[$campoOrdemReproducao] ?? 0))
        ?: ((int)$b['id'] <=> (int)$a['id']);
});
$totalReproducao = count($registrosReproducao);
$paginasReproducao = max(1, (int)ceil($totalReproducao / $porPaginaReproducao));
$paginaReproducao = min($paginasReproducao, max(1, (int)($_GET['pag'] ?? 1)));
$offsetReproducao = ($paginaReproducao - 1) * $porPaginaReproducao;
$registrosPaginaReproducao = array_slice($registrosReproducao, $offsetReproducao, $porPaginaReproducao);
$parametrosReproducao = array('pg' => 'relatorio_reproducao', 'filtro' => $filtro, 'filtro2' => $ordemReproducao, 'pesquisa' => $pesquisaReproducao, 'por_pagina' => $porPaginaReproducao);
$urlReproducao = function ($pagina) use ($parametrosReproducao, $hReproducao) {
    return $hReproducao('geral.php?' . http_build_query($parametrosReproducao + array('pag' => $pagina)));
};
?>
<section class="content-header">
  <h1>Relatório de reprodução</h1>
  <ol class="breadcrumb"><li><i class="fa fa-book"></i> Relatórios</li><li class="active">Reprodução</li></ol>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';
  var form = document.getElementById('filtros-relatorio-reproducao');
  if (!form) return;
  var temporizador;
  var pesquisa = form.elements.pesquisa;
  function enviar() { form.submit(); }
  form.elements.filtro.addEventListener('change', enviar);
  form.elements.filtro2.addEventListener('change', enviar);
  pesquisa.addEventListener('input', function () {
    window.clearTimeout(temporizador);
    temporizador = window.setTimeout(enviar, 500);
  });
  pesquisa.addEventListener('search', enviar);
});
</script>
<section class="content">
  <div class="box" style="border-top:0;"><div class="box-body">
    <form id="filtros-relatorio-reproducao" action="geral.php" method="get">
      <input type="hidden" name="pg" value="relatorio_reproducao">
      <input type="hidden" name="por_pagina" value="<?=$porPaginaReproducao?>">
      <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group col-sm-6 col-md-3">
          <label for="tipo-relatorio-reproducao">Tipo de reprodução</label>
          <select class="form-control" id="tipo-relatorio-reproducao" name="filtro">
            <?php foreach ($tiposReproducao as $tipoReproducao => $configuracao): ?>
            <option value="<?=$hReproducao($tipoReproducao)?>" <?=$tipoReproducao === $filtro ? 'selected' : ''?>><?=$hReproducao($tipoReproducao)?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-3">
          <label for="ordem-relatorio-reproducao">Ordenar por</label>
          <select class="form-control" id="ordem-relatorio-reproducao" name="filtro2">
            <?php foreach ($rotulosOrdemReproducao as $valorOrdem => $rotuloOrdem): ?>
            <option value="<?=$valorOrdem?>" <?=$valorOrdem === $ordemReproducao ? 'selected' : ''?>><?=$hReproducao($rotuloOrdem)?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group col-sm-6 col-md-3">
          <label for="pesquisa-relatorio-reproducao">Lote ou animal</label>
          <input type="search" class="form-control" id="pesquisa-relatorio-reproducao" name="pesquisa" value="<?=$hReproducao($pesquisaReproducao)?>" placeholder="Digite para pesquisar">
        </div>
        <div class="form-group col-sm-6 col-md-3">
          <button type="submit" class="btn btn-primary">Pesquisar</button>
          <a class="btn btn-default" href="geral.php?pg=relatorio_reproducao">Limpar</a>
        </div>
      </div>
    </form>
  </div></div>

  <div class="box" style="border-top:0;"><div class="box-body">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;">
      <h2 class="box-title" style="font-size:16px; margin:0;"><?=$hReproducao($filtro)?></h2>
      <span class="text-muted"><?=$totalReproducao?> lote(s)</span>
    </div>
    <div class="table-responsive"><table class="table table-bordered table-striped">
      <thead><tr><th style="width:60px;">Nº</th><th>Lote</th><th>Macho</th><?php if ($filtro === 'Transplante de embriões'): ?><th>Fêmea</th><?php endif; ?><th>Data</th><th><?=$hReproducao($configuracaoReproducao['tituloQuantidade'])?></th><th>Ultrassom</th><th>Crias vivas</th><th>Mortes no nascimento</th><th style="width:1%;"><span class="sr-only">Ações</span></th></tr></thead>
      <tbody>
      <?php $colunasReproducao = $filtro === 'Transplante de embriões' ? 10 : 9; ?>
      <?php if (!$registrosPaginaReproducao): ?><tr><td colspan="<?=$colunasReproducao?>" class="text-center text-muted" style="padding:30px;">Nenhum lote encontrado para os filtros selecionados.</td></tr><?php endif; ?>
      <?php foreach ($registrosPaginaReproducao as $indiceReproducao => $registroReproducao):
          $dataReproducao = dataRelatorioReproducao($registroReproducao['data_inicio'] ?? '');
          if (!empty($registroReproducao['data_fim'])) { $dataReproducao .= ' até ' . dataRelatorioReproducao($registroReproducao['data_fim']); }
          $percentualUltrassom = max(0, min(100, (float)($registroReproducao['ultrassom'] ?? 0)));
          $urlLote = 'geral.php?pg=' . $configuracaoReproducao['pagina'] . '&amp;id_lote=' . (int)$registroReproducao['id_lote'];
      ?>
      <tr>
        <td><?=$offsetReproducao + $indiceReproducao + 1?></td>
        <td><a style="color:inherit; text-decoration:none;" href="<?=$urlLote?>"><?=$hReproducao($registroReproducao['codigo'] ?? 'Não informado')?></a></td>
        <td><?=$hReproducao($registroReproducao['nome_macho'] ?? 'Não informado')?></td>
        <?php if ($filtro === 'Transplante de embriões'): ?><td><?=$hReproducao($registroReproducao['nome_femea'] ?? 'Não informado')?></td><?php endif; ?>
        <td style="white-space:nowrap;"><?=$hReproducao($dataReproducao)?></td>
        <td><?=(int)($registroReproducao['femeas'] ?? 0)?></td>
        <td style="white-space:nowrap;"><span class="text-success"><?=number_format($percentualUltrassom, 1, ',', '.')?>%</span> <span class="text-muted">/</span> <span class="text-danger"><?=number_format(100 - $percentualUltrassom, 1, ',', '.')?>%</span></td>
        <td><?=(int)($registroReproducao['vivos'] ?? 0)?></td>
        <td><?=(int)($registroReproducao['mortes'] ?? 0)?></td>
        <td><a href="<?=$urlLote?>" aria-label="Abrir lote <?=$hReproducao($registroReproducao['codigo'] ?? '')?>" title="Abrir lote"><i class="fa fa-search" aria-hidden="true"></i></a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px; border-top:1px solid #f4f4f4; padding-top:10px;">
      <label for="por-pagina-relatorio-reproducao" style="margin:0; font-weight:normal;">Por página</label>
      <select id="por-pagina-relatorio-reproducao" class="form-control" style="width:70px;" onchange="var form=document.getElementById('filtros-relatorio-reproducao'); form.elements.por_pagina.value=this.value; form.submit();">
        <?php foreach (array(10,20,50,100) as $limiteReproducao): ?><option value="<?=$limiteReproducao?>" <?=$limiteReproducao === $porPaginaReproducao ? 'selected' : ''?>><?=$limiteReproducao?></option><?php endforeach; ?>
      </select>
      <span class="text-muted">Exibindo <?=$totalReproducao ? $offsetReproducao + 1 : 0?> a <?=min($offsetReproducao + $porPaginaReproducao, $totalReproducao)?> de <?=$totalReproducao?> lotes</span>
      <nav aria-label="Páginas do relatório de reprodução"><ul class="pagination pagination-sm" style="margin:0;">
        <li class="<?=$paginaReproducao === 1 ? 'disabled' : ''?>"><?php if ($paginaReproducao > 1): ?><a href="<?=$urlReproducao($paginaReproducao - 1)?>" aria-label="Página anterior">«</a><?php else: ?><span>«</span><?php endif; ?></li>
        <?php for ($paginaItemReproducao=1; $paginaItemReproducao <= $paginasReproducao; $paginaItemReproducao++):
            if ($paginaItemReproducao !== 1 && $paginaItemReproducao !== $paginasReproducao && abs($paginaItemReproducao - $paginaReproducao) > 1) { if ($paginaItemReproducao === 2 || $paginaItemReproducao === $paginasReproducao - 1) echo '<li class="disabled"><span>…</span></li>'; continue; } ?>
        <li class="<?=$paginaItemReproducao === $paginaReproducao ? 'active' : ''?>"><a href="<?=$urlReproducao($paginaItemReproducao)?>" <?=$paginaItemReproducao === $paginaReproducao ? 'aria-current="page"' : ''?>><?=$paginaItemReproducao?></a></li>
        <?php endfor; ?>
        <li class="<?=$paginaReproducao === $paginasReproducao ? 'disabled' : ''?>"><?php if ($paginaReproducao < $paginasReproducao): ?><a href="<?=$urlReproducao($paginaReproducao + 1)?>" aria-label="Próxima página">»</a><?php else: ?><span>»</span><?php endif; ?></li>
      </ul></nav>
    </div>
  </div></div>
</section>
