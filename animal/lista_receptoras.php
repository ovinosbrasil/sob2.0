<?php
if (empty($_SESSION['receptora_csrf'])) {
    $_SESSION['receptora_csrf'] = bin2hex(random_bytes(32));
}
$receptoraFlash = isset($_SESSION['receptora_flash']) ? $_SESSION['receptora_flash'] : null;
unset($_SESSION['receptora_flash']);
$receptoras = array();
$mediasPorParto = array();
$historicosReceptoras = array();
$criasPorParto = array();
$buscaReceptora = isset($_GET['busca']) && is_string($_GET['busca']) ? trim($_GET['busca']) : '';
$filtroPaginacao = '&amp;busca=' . rawurlencode($buscaReceptora);
$totalReceptoras = 0;
$porPagina = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) { $porPagina = 10; }
$offsetReceptoras = 0;
$paginaReceptoras = filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT);
$paginaReceptoras = max(1, (int) $paginaReceptoras);
$totalPaginas = 1;
$erroReceptoras = '';
$linkReceptoras = DBConnect();
try {
    if (!mysqli_set_charset($linkReceptoras, 'utf8mb4')) {
        throw new RuntimeException('Falha ao configurar conexão.');
    }
    $filtroNome = $buscaReceptora !== '' ? " WHERE nome LIKE ? ESCAPE '!'" : '';
    $padraoBusca = '%' . strtr($buscaReceptora, array('!' => '!!', '%' => '!%', '_' => '!_')) . '%';
    $consultaTotal = mysqli_prepare($linkReceptoras, 'SELECT COUNT(*) AS total FROM receptora' . $filtroNome);
    if (!$consultaTotal) {
        throw new RuntimeException('Falha ao preparar pesquisa.');
    }
    if ($filtroNome !== '') {
        mysqli_stmt_bind_param($consultaTotal, 's', $padraoBusca);
    }
    if (!mysqli_stmt_execute($consultaTotal)) {
        throw new RuntimeException('Falha ao pesquisar receptoras.');
    }
    $resultado = mysqli_stmt_get_result($consultaTotal);
    mysqli_stmt_close($consultaTotal);
    if (!$resultado) {
        throw new RuntimeException('Falha ao consultar receptoras.');
    }
    $totalReceptoras = (int) mysqli_fetch_assoc($resultado)['total'];
    $totalPaginas = max(1, (int) ceil($totalReceptoras / $porPagina));
    $paginaReceptoras = min($paginaReceptoras, $totalPaginas);
    $offsetReceptoras = ($paginaReceptoras - 1) * $porPagina;
    $consultaLista = mysqli_prepare($linkReceptoras, "SELECT r.id, r.nome, r.ativo,
            COUNT(tc.id) AS lotes,
            COALESCE(SUM(tc.ultrassom = 1), 0) AS ultrassom_positivo,
            COALESCE(SUM(tc.ultrassom = 2), 0) AS ultrassom_negativo,
            COALESCE(SUM(tc.ultrassom = 0), 0) AS ultrassom_nao_informado,
            COALESCE(SUM(tc.status_nascimento = 1), 0) AS nascidos,
            COALESCE(SUM(tc.status_nascimento = 0), 0) AS nao_nascidos,
            COALESCE(
                (SELECT historico.ultrassom
                 FROM transplante_controle AS historico
                 INNER JOIN transplante AS lote ON lote.id = historico.id_lote
                 WHERE historico.id_receptora = r.id
                 ORDER BY lote.id DESC, historico.id DESC LIMIT 1) = 2
                AND
                (SELECT historico.ultrassom
                 FROM transplante_controle AS historico
                 INNER JOIN transplante AS lote ON lote.id = historico.id_lote
                 WHERE historico.id_receptora = r.id
                 ORDER BY lote.id DESC, historico.id DESC LIMIT 1 OFFSET 1) = 2,
                0
            ) AS duas_ultimas_negativas
        FROM (SELECT id, nome, ativo FROM receptora $filtroNome ORDER BY id DESC LIMIT $offsetReceptoras, $porPagina) AS r
        LEFT JOIN transplante_controle AS tc ON tc.id_receptora = r.id
        GROUP BY r.id, r.nome, r.ativo
        ORDER BY r.id DESC");
    if (!$consultaLista) {
        throw new RuntimeException('Falha ao preparar listagem.');
    }
    if ($filtroNome !== '') {
        mysqli_stmt_bind_param($consultaLista, 's', $padraoBusca);
    }
    if (!mysqli_stmt_execute($consultaLista)) {
        throw new RuntimeException('Falha ao pesquisar receptoras.');
    }
    $resultado = mysqli_stmt_get_result($consultaLista);
    mysqli_stmt_close($consultaLista);
    if (!$resultado) {
        throw new RuntimeException('Falha ao listar receptoras.');
    }
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $receptoras[] = $linha;
    }
    // Agrupa os animais vinculados diretamente à receptora por data do parto.
    if ($receptoras) {
        $idsReceptoras = implode(',', array_map('intval', array_column($receptoras, 'id')));
        $resultadoCrias = mysqli_query($linkReceptoras, "SELECT cria.id_receptora, cria.data_de_nascimento,
                cria.nome AS cria, CASE WHEN cria.terceiro_pai = 1 THEN externo.nome ELSE pai.nome END AS pai
            FROM animais AS cria
            LEFT JOIN animais AS pai ON pai.id = cria.pai AND COALESCE(cria.terceiro_pai, 0) = 0
            LEFT JOIN terceiros AS externo ON externo.id = cria.pai AND cria.terceiro_pai = 1
            WHERE cria.id_receptora IN ($idsReceptoras)
              AND cria.data_de_nascimento IS NOT NULL
              AND CAST(cria.data_de_nascimento AS CHAR) <> '0000-00-00'
            ORDER BY cria.nome, cria.id");
        if (!$resultadoCrias) {
            throw new RuntimeException('Falha ao consultar crias e pais das receptoras.');
        }
        while ($cria = mysqli_fetch_assoc($resultadoCrias)) {
            $criasPorParto[(int) $cria['id_receptora']][$cria['data_de_nascimento']][] = $cria;
        }
        $resultadoHistorico = mysqli_query($linkReceptoras, "SELECT tc.id_receptora, tc.id_lote,
                tc.ultrassom, tc.status_nascimento, t.codigo, t.data,
                partos.data_de_nascimento, partos.peso_parto
            FROM transplante_controle AS tc
            LEFT JOIN transplante AS t ON t.id = tc.id_lote
            LEFT JOIN (
                SELECT a.id_receptora, a.data_de_nascimento,
                    CASE WHEN COUNT(*) = COUNT(CASE WHEN a.peso2 > 0 THEN 1 END)
                         THEN SUM(a.peso2) ELSE NULL END AS peso_parto
                FROM animais AS a
                WHERE a.id_receptora IN ($idsReceptoras)
                  AND a.data_de_nascimento IS NOT NULL
                  AND CAST(a.data_de_nascimento AS CHAR) <> '0000-00-00'
                GROUP BY a.id_receptora, a.data_de_nascimento
            ) AS partos ON partos.id_receptora = tc.id_receptora
                AND partos.data_de_nascimento BETWEEN DATE_ADD(t.data, INTERVAL 146 DAY)
                                                   AND DATE_ADD(t.data, INTERVAL 161 DAY)
            WHERE tc.id_receptora IN ($idsReceptoras)
            ORDER BY tc.id_lote DESC, tc.id DESC, partos.data_de_nascimento");
        if (!$resultadoHistorico) {
            throw new RuntimeException('Falha ao consultar histórico das receptoras.');
        }
        while ($historico = mysqli_fetch_assoc($resultadoHistorico)) {
            $historicosReceptoras[(int) $historico['id_receptora']][] = $historico;
        }
        $resultadoMedias = mysqli_query($linkReceptoras, "SELECT partos.id_receptora, AVG(partos.peso_parto) AS media_peso
            FROM (
                SELECT a.id_receptora, a.data_de_nascimento, SUM(a.peso2) AS peso_parto
                FROM animais AS a
                WHERE a.id_receptora IN ($idsReceptoras)
                  AND a.data_de_nascimento IS NOT NULL
                  AND CAST(a.data_de_nascimento AS CHAR) <> '0000-00-00'
                GROUP BY a.id_receptora, a.data_de_nascimento
                HAVING COUNT(*) = COUNT(CASE WHEN a.peso2 > 0 THEN 1 END)
            ) AS partos
            GROUP BY partos.id_receptora");
        if (!$resultadoMedias) {
            throw new RuntimeException('Falha ao calcular a média por parto.');
        }
        while ($mediaParto = mysqli_fetch_assoc($resultadoMedias)) {
            $mediasPorParto[(int) $mediaParto['id_receptora']] = (float) $mediaParto['media_peso'];
        }
    }
} catch (Exception $e) {
    $erroReceptoras = 'Não foi possível carregar as receptoras. Verifique se as migrações de receptoras foram aplicadas.';
}
DBClose($linkReceptoras);
?>
<style>
.table > tbody > tr.receptora-inativa > td:not(:last-child) {
  opacity: .5;
}
</style>
<section class="content-header">
  <h1>Receptoras</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-github-alt"></i> Animais</li>
    <li class="active">Receptoras</li>
  </ol>
</section>
<section class="content">
  <?php if ($receptoraFlash !== null): ?>
    <div class="alert <?= $receptoraFlash['erro'] !== '' ? 'alert-danger' : 'alert-success' ?>" role="alert">
      <?= htmlspecialchars($receptoraFlash['erro'] !== '' ? $receptoraFlash['erro'] : (isset($receptoraFlash['sucesso']) ? $receptoraFlash['sucesso'] : 'Receptora cadastrada com sucesso.'), ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:20px;">
        <form action="geral.php" method="get" style="display:flex; flex-wrap:wrap; align-items:flex-end; gap:8px; margin:0;">
          <input type="hidden" name="pg" value="lista_receptoras">
          <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
          <div style="width:320px; max-width:100%;">
            <label for="busca_receptora">Pesquisar receptora</label>
            <input type="search" class="form-control" id="busca_receptora" name="busca" placeholder="Nome da receptora" value="<?=htmlspecialchars($buscaReceptora, ENT_QUOTES, 'UTF-8')?>">
          </div>
          <button type="submit" class="btn btn-primary">Pesquisar</button>
          <a href="geral.php?pg=lista_receptoras" class="btn btn-default">Limpar</a>
        </form>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#cadastro-receptora"><i class="fa fa-plus" aria-hidden="true"></i> Cadastrar receptora</button>
      </div>
      <?php if ($erroReceptoras !== ''): ?>
        <div class="alert alert-danger" role="alert"><?= $erroReceptoras ?></div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead><tr><th style="width:1%; white-space:nowrap;">Nº</th><th>Nome</th><th style="width: 80px;">Lotes</th><th style="width: 160px;">Ultrassom</th><th style="width: 140px;" title="Nascidos / Não nascidos">Nascimento</th><th>Observação</th><th style="width:1%;"><span class="sr-only">Ações</span></th></tr></thead>
            <tbody>
              <?php if (!$receptoras): ?>
                <tr><td colspan="7"><?= $buscaReceptora !== '' ? 'Nenhuma receptora encontrada para esta pesquisa.' : 'Nenhuma receptora cadastrada.' ?></td></tr>
              <?php endif; ?>
              <?php foreach ($receptoras as $indiceReceptora => $receptora): ?>
                <tr class="receptora-historico<?=$receptora['ativo'] ? '' : ' receptora-inativa'?>" data-modal="#historico-receptora-<?= (int) $receptora['id'] ?>" style="cursor: pointer;">
                  <td><?= $offsetReceptoras + $indiceReceptora + 1 ?></td>
                  <td><?= htmlspecialchars($receptora['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= (int) $receptora['lotes'] ?></td>
                  <td style="white-space: nowrap;">
                    <span style="color: #008d4c;" title="Positivos"><?= (int) $receptora['ultrassom_positivo'] ?></span> /
                    <span style="color: #dd4b39;" title="Negativos"><?= (int) $receptora['ultrassom_negativo'] ?></span> /
                    <span style="color: #777;" title="Não informados"><?= (int) $receptora['ultrassom_nao_informado'] ?> N/A</span>
                  </td>
                  <td style="white-space: nowrap;">
                    <span style="color: #008d4c;" title="Nascidos"><?= (int) $receptora['nascidos'] ?></span> /
                    <span style="color: #dd4b39;" title="Não nascidos"><?= (int) $receptora['nao_nascidos'] ?></span>
                  </td>
                  <td>
                    <?php if ($receptora['duas_ultimas_negativas']): ?>
                      <span style="color: #dd4b39;">Receptora com os dois últimos ultrassons negativos.</span>
                    <?php endif; ?>
                    <div style="color: <?= isset($mediasPorParto[$receptora['id']]) ? ($mediasPorParto[$receptora['id']] > 30 ? '#008d4c' : ($mediasPorParto[$receptora['id']] < 30 ? '#dd4b39' : '#777')) : '#777' ?>;" title="Média da soma dos pesos de apartação por data de nascimento. Considera partos com data e todos os pesos informados.">
                      Média por parto: <?= isset($mediasPorParto[$receptora['id']]) ? number_format($mediasPorParto[$receptora['id']], 2, ',', '.') . ' kg' : 'N/A' ?>
                    </div>
                  </td>
                  <td>
                    <div style="display:flex; align-items:center; gap:12px; white-space:nowrap;">
                    <form action="animal/_atualizar_receptora.php" method="post" style="margin:0;">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['receptora_csrf'], ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="id" value="<?= (int) $receptora['id'] ?>">
                      <input type="hidden" name="pag" value="<?= $paginaReceptoras ?>">
                      <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
                      <input type="hidden" name="busca" value="<?= htmlspecialchars($buscaReceptora, ENT_QUOTES, 'UTF-8') ?>">
                      <div class="sob-controle-status">
                        <button type="submit" name="ativo" value="<?=$receptora['ativo'] ? '0' : '1'?>" class="sob-interruptor" role="switch" aria-checked="<?=$receptora['ativo'] ? 'true' : 'false'?>" aria-label="Receptora ativa: <?=htmlspecialchars($receptora['nome'], ENT_QUOTES, 'UTF-8')?>" title="<?=$receptora['ativo'] ? 'Inativar receptora' : 'Ativar receptora'?>">
                          <span class="sob-interruptor__indicador"><i class="fa <?=$receptora['ativo'] ? 'fa-check' : 'fa-times'?>" aria-hidden="true"></i></span>
                        </button>

                      </div>
                    </form>
                  <button type="button" class="text-primary" style="background:none; border:0; padding:0; cursor:pointer;" title="Ver histórico" data-toggle="modal" data-target="#historico-receptora-<?= (int) $receptora['id'] ?>" aria-label="Ver histórico de <?= htmlspecialchars($receptora['nome'], ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-search" aria-hidden="true"></i></button>
                    <form action="animal/_excluir_receptora.php" method="post" style="margin:0;" onsubmit="confirmarExclusaoReceptora(event, this)">
                      <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['receptora_csrf'], ENT_QUOTES, 'UTF-8')?>">
                      <input type="hidden" name="id" value="<?=(int)$receptora['id']?>">
                      <input type="hidden" name="pag" value="<?=$paginaReceptoras?>">
                      <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
                      <input type="hidden" name="busca" value="<?=htmlspecialchars($buscaReceptora, ENT_QUOTES, 'UTF-8')?>">
                      <button type="submit" class="text-danger" style="background:none; border:0; padding:0; cursor:pointer;" data-nome="<?=htmlspecialchars($receptora['nome'], ENT_QUOTES, 'UTF-8')?>" title="Excluir receptora" aria-label="Excluir receptora"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
    <?php if ($erroReceptoras === ''): ?>
      <?php
      $filtros = array('busca' => $buscaReceptora);
      $totalAnimais = $totalReceptoras;
      $loop = $offsetReceptoras;
      $pagina = $paginaReceptoras - 1;
      $qtd_pag = $totalPaginas;
      $urlPagina = 'geral.php?pg=lista_receptoras' . $filtroPaginacao . '&amp;por_pagina=' . $porPagina;
      ?>
          <div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
            <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
              <input type="hidden" name="pg" value="lista_receptoras">
              <?php foreach ($filtros as $campo => $valor): ?>
              <input type="hidden" name="<?=$campo?>" value="<?=htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')?>">
              <?php endforeach; ?>
              <label for="por-pagina" style="margin:0; font-weight:normal;">Por página</label>
              <select id="por-pagina" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
                <?php foreach (array(10, 20, 50, 100) as $quantidade): ?>
                <option value="<?=$quantidade?>" <?=$porPagina === $quantidade ? 'selected' : ''?>><?=$quantidade?></option>
                <?php endforeach; ?>
              </select>
              <noscript><button type="submit" class="btn btn-default btn-sm">Aplicar</button></noscript>
            </form>
            <span class="text-muted">
              Exibindo <?=$totalAnimais ? $loop + 1 : 0?> a <?=min($loop + $porPagina, $totalAnimais)?> de <?=$totalAnimais?> receptoras
            </span>
            <?php if ($qtd_pag > 1):
                $paginasVisiveis = array(0, $qtd_pag - 1);
                $inicioPaginas = max(0, min($pagina - 1, $qtd_pag - 3));
                $fimPaginas = min($qtd_pag - 1, max($pagina + 1, 2));
                for ($numero = $inicioPaginas; $numero <= $fimPaginas; $numero++) {
                    $paginasVisiveis[] = $numero;
                }
                $paginasVisiveis = array_unique($paginasVisiveis);
                sort($paginasVisiveis);
                $anterior = -1;
            ?>
            <nav aria-label="Paginação das receptoras">
              <ul class="pagination pagination-sm no-margin">
                <?php if ($pagina > 0): ?>
                <li><a href="<?=$urlPagina?>&amp;pag=<?=$pagina?>" aria-label="Página anterior" title="Página anterior" rel="prev">&laquo;</a></li>
                <?php else: ?>
                <li class="disabled"><span aria-label="Página anterior" aria-disabled="true">&laquo;</span></li>
                <?php endif; ?>
                <?php foreach ($paginasVisiveis as $numero): ?>
                  <?php if ($anterior >= 0 && $numero > $anterior + 1): ?>
                  <li class="disabled"><span aria-hidden="true">&hellip;</span></li>
                  <?php endif; ?>
                  <?php if ($numero === $pagina): ?>
                  <li class="active"><span aria-current="page" aria-label="Página <?=$numero+1?>"><?=$numero+1?></span></li>
                  <?php else: ?>
                  <li><a href="<?=$urlPagina?>&amp;pag=<?=$numero+1?>" aria-label="Página <?=$numero+1?>"><?=$numero+1?></a></li>
                  <?php endif; ?>
                <?php $anterior = $numero; endforeach; ?>
                <?php if ($pagina < $qtd_pag - 1): ?>
                <li><a href="<?=$urlPagina?>&amp;pag=<?=$pagina+2?>" aria-label="Próxima página" title="Próxima página" rel="next">&raquo;</a></li>
                <?php else: ?>
                <li class="disabled"><span aria-label="Próxima página" aria-disabled="true">&raquo;</span></li>
                <?php endif; ?>
              </ul>
            </nav>
            <?php endif; ?>
          </div>
    <?php endif; ?>
  </div>
</section>

<div class="modal fade" id="cadastro-receptora" tabindex="-1" role="dialog" aria-labelledby="titulo-cadastro-receptora">
  <div class="modal-dialog" role="document" style="width:440px; max-width:calc(100vw - 32px); margin:10vh auto;">
    <div class="modal-content" style="border:0; border-radius:12px;">
      <form action="animal/_cadastrar_receptora.php" method="post">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">&times;</button>
          <h4 class="modal-title" id="titulo-cadastro-receptora">Cadastrar receptora</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['receptora_csrf'], ENT_QUOTES, 'UTF-8')?>">
          <?php if ($receptoraFlash !== null && $receptoraFlash['erro'] !== '' && !isset($receptoraFlash['sucesso'])): ?>
          <div class="alert alert-danger" role="alert"><?=htmlspecialchars($receptoraFlash['erro'], ENT_QUOTES, 'UTF-8')?></div>
          <?php endif; ?>
          <div class="form-group">
            <label for="nome_receptora">Nome <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nome_receptora" name="nome" maxlength="50" required placeholder="Nome da receptora" value="<?=htmlspecialchars($receptoraFlash['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php if ($erroReceptoras === ''): ?>
  <?php foreach ($receptoras as $receptora): ?>
    <div class="modal fade" id="historico-receptora-<?= (int) $receptora['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="titulo-historico-<?= (int) $receptora['id'] ?>">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="titulo-historico-<?= (int) $receptora['id'] ?>">Histórico da receptora <?= htmlspecialchars($receptora['nome'], ENT_QUOTES, 'UTF-8') ?></h4>
          </div>
          <div class="modal-body">
            <p style="color: <?= isset($mediasPorParto[$receptora['id']]) ? ($mediasPorParto[$receptora['id']] > 30 ? '#008d4c' : ($mediasPorParto[$receptora['id']] < 30 ? '#dd4b39' : '#777')) : '#777' ?>;"><strong>Média por parto:</strong> <?= isset($mediasPorParto[$receptora['id']]) ? number_format($mediasPorParto[$receptora['id']], 2, ',', '.') . ' kg' : 'N/A' ?></p>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead><tr><th>Lote</th><th>Data</th><th>Pai</th><th>Cria</th><th>Ultrassom</th><th>Nascimento</th><th>Peso total do parto (kg)</th></tr></thead>
                <tbody>
                  <?php if (empty($historicosReceptoras[$receptora['id']])): ?>
                    <tr><td colspan="7">Esta receptora não possui lotes vinculados.</td></tr>
                  <?php endif; ?>
                  <?php foreach (($historicosReceptoras[$receptora['id']] ?? array()) as $historico): ?>
                    <?php $criasDoParto = $criasPorParto[$receptora['id']][$historico['data_de_nascimento']] ?? array(); ?>
                    <tr>
                      <td><?= htmlspecialchars($historico['codigo'] ?: 'Lote #' . $historico['id_lote'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= $historico['data'] && $historico['data'] !== '0000-00-00' ? htmlspecialchars(date('d/m/Y', strtotime($historico['data'])), ENT_QUOTES, 'UTF-8') : 'N/A' ?></td>
                      <td>
                        <?php if (!$criasDoParto): ?>Não informado<?php endif; ?>
                        <?php foreach ($criasDoParto as $cria): ?>
                          <div><?= htmlspecialchars($cria['pai'] ?: 'Não informado', ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endforeach; ?>
                      </td>
                      <td>
                        <?php if (!$criasDoParto): ?>Não informado<?php endif; ?>
                        <?php foreach ($criasDoParto as $cria): ?>
                          <div><?= htmlspecialchars($cria['cria'] ?: 'Não informado', ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endforeach; ?>
                      </td>
                      <td class="<?= (int) $historico['ultrassom'] === 1 ? 'text-success' : ((int) $historico['ultrassom'] === 2 ? 'text-danger' : 'text-muted') ?>"><?= array(0 => 'Não informado', 1 => 'Positivo', 2 => 'Negativo')[(int) $historico['ultrassom']] ?? 'Não informado' ?></td>
                      <td style="<?= $historico['status_nascimento'] === null ? '' : ((int) $historico['status_nascimento'] === 1 ? 'color: #008d4c;' : 'color: #dd4b39;') ?>"><?= $historico['status_nascimento'] === null ? 'Não informado' : ((int) $historico['status_nascimento'] === 1 ? 'Nascido' : 'Não nascido') ?></td>
                      <td style="color: <?= $historico['peso_parto'] !== null && (float) $historico['peso_parto'] > 30 ? '#008d4c' : ($historico['peso_parto'] !== null && (float) $historico['peso_parto'] < 30 ? '#dd4b39' : '#777') ?>;"><?= $historico['peso_parto'] !== null ? number_format((float) $historico['peso_parto'], 2, ',', '.') : 'N/A' ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <p class="help-block">O peso total do parto soma os pesos de apartação dos animais desta receptora nascidos na mesma data. Cada data é apresentada separadamente, sem média do lote. A janela de 146 a 161 dias após a data do lote é usada apenas para vincular o nascimento ao lote. N/A indica ausência de parto ou peso incompleto.</p>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button></div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
<script>
function confirmarExclusaoReceptora(event, formulario) {
    event.preventDefault();
    confirmarExclusao({
        titulo: 'Excluir receptora?',
        nome: formulario.querySelector('[data-nome]').getAttribute('data-nome'),
        descricao: 'Confirme se deseja excluir esta receptora. Receptoras com lotes ou animais vinculados devem ser inativadas para preservar o histórico.',
        aoConfirmar: function () { formulario.submit(); }
    });
}
document.addEventListener('DOMContentLoaded', function () {
    jQuery('#cadastro-receptora').on('shown.bs.modal', function () {
        document.getElementById('nome_receptora').focus();
    });
    <?php if ($receptoraFlash !== null && $receptoraFlash['erro'] !== '' && !isset($receptoraFlash['sucesso'])): ?>
    jQuery('#cadastro-receptora').modal('show');
    <?php endif; ?>
    jQuery('.receptora-historico').on('click', function (event) {
        if (jQuery(event.target).closest('button, a, input, select, textarea, label, form').length) {
            return;
        }
        jQuery(jQuery(this).attr('data-modal')).modal('show');
    });
});
</script>
