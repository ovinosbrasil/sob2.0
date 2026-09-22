<?php
if (empty($_SESSION['receptora_csrf'])) {
    $_SESSION['receptora_csrf'] = bin2hex(random_bytes(32));
}
$receptoraFlash = isset($_SESSION['receptora_flash']) ? $_SESSION['receptora_flash'] : null;
unset($_SESSION['receptora_flash']);
$receptoras = array();
$mediasPorParto = array();
$buscaReceptora = isset($_GET['busca']) && is_string($_GET['busca']) ? trim($_GET['busca']) : '';
$filtroPaginacao = '&amp;busca=' . rawurlencode($buscaReceptora);
$totalReceptoras = 0;
$porPagina = 40;
$paginaReceptoras = filter_input(INPUT_GET, 'pag', FILTER_VALIDATE_INT);
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
  <div class="box box-success">
    <div class="box-header with-border"><h3 class="box-title">Cadastrar receptora</h3></div>
    <form action="animal/_cadastrar_receptora.php" method="post">
      <div class="box-body">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['receptora_csrf'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="row" style="display: flex; flex-wrap: wrap; align-items: flex-end;">
          <div class="col-sm-6 col-md-3">
            <div class="form-group">
              <label for="nome_receptora">Nome <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nome_receptora" name="nome" maxlength="50" required value="<?= htmlspecialchars($receptoraFlash !== null ? $receptoraFlash['nome'] : '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="col-sm-6 col-md-9">
            <div class="form-group">
              <button type="submit" class="btn btn-success">Cadastrar receptora</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="box box-success">
    <div class="box-header with-border"><h3 class="box-title">Lista de receptoras</h3></div>
    <div class="box-body">
      <form action="geral.php" method="get">
        <input type="hidden" name="pg" value="lista_receptoras">
        <div class="row" style="display: flex; flex-wrap: wrap; align-items: flex-end;">
          <div class="col-sm-6 col-md-3">
            <div class="form-group">
              <label for="busca_receptora">Pesquisar receptora</label>
              <input type="search" class="form-control" id="busca_receptora" name="busca" placeholder="Nome da receptora" value="<?= htmlspecialchars($buscaReceptora, ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="col-sm-6 col-md-9">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Pesquisar</button>
              <?php if ($buscaReceptora !== ''): ?>
                <a href="geral.php?pg=lista_receptoras" class="btn btn-default">Limpar</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </form>
      <?php if ($erroReceptoras !== ''): ?>
        <div class="alert alert-danger" role="alert"><?= $erroReceptoras ?></div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-bordered" id="tabela_padrao">
            <thead><tr><th style="width: 60px;">Nº</th><th>Nome</th><th style="width: 80px;">Lotes</th><th style="width: 160px;">Ultrassom</th><th style="width: 140px;" title="Nascidos / Não nascidos">Nascimento</th><th>Observação</th><th style="width: 160px;">Status</th></tr></thead>
            <tbody>
              <?php if (!$receptoras): ?>
                <tr><td colspan="7"><?= $buscaReceptora !== '' ? 'Nenhuma receptora encontrada para esta pesquisa.' : 'Nenhuma receptora cadastrada.' ?></td></tr>
              <?php endif; ?>
              <?php foreach ($receptoras as $indiceReceptora => $receptora): ?>
                <tr>
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
                    <div style="color: <?= isset($mediasPorParto[$receptora['id']]) ? ($mediasPorParto[$receptora['id']] >= 30 ? '#008d4c' : '#dd4b39') : '#777' ?>;" title="Média da soma dos pesos de apartação por data de nascimento. Considera partos com data e todos os pesos informados.">
                      Média por parto: <?= isset($mediasPorParto[$receptora['id']]) ? number_format($mediasPorParto[$receptora['id']], 2, ',', '.') . ' kg' : 'N/A' ?>
                    </div>
                  </td>
                  <td>
                    <form action="animal/_atualizar_receptora.php" method="post">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['receptora_csrf'], ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="id" value="<?= (int) $receptora['id'] ?>">
                      <input type="hidden" name="pag" value="<?= $paginaReceptoras ?>">
                      <input type="hidden" name="busca" value="<?= htmlspecialchars($buscaReceptora, ENT_QUOTES, 'UTF-8') ?>">
                      <select name="ativo" class="form-control" aria-label="Status de <?= htmlspecialchars($receptora['nome'], ENT_QUOTES, 'UTF-8') ?>" style="width: 140px; max-width: 100%; margin: 0 auto; color: <?= $receptora['ativo'] ? '#008d4c' : '#dd4b39' ?>;" onchange="this.style.color = this.value === '1' ? '#008d4c' : '#dd4b39'; this.form.submit();">
                        <option value="1" style="color: #008d4c;"<?= $receptora['ativo'] ? ' selected' : '' ?>>Ativo</option>
                        <option value="0" style="color: #dd4b39;"<?= !$receptora['ativo'] ? ' selected' : '' ?>>Inativo</option>
                      </select>
                      <noscript><button type="submit" class="btn btn-success btn-sm">Salvar</button></noscript>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
    <?php if ($erroReceptoras === ''): ?>
      <div class="box-footer clearfix">
        <span><?= $totalReceptoras ?> receptora(s) — Página <?= $paginaReceptoras ?> de <?= $totalPaginas ?></span>
        <?php if ($totalPaginas > 1): ?>
          <nav aria-label="Paginação das receptoras" class="pull-right">
            <ul class="pagination pagination-sm no-margin">
              <?php if ($paginaReceptoras > 1): ?>
                <li><a href="geral.php?pg=lista_receptoras&amp;pag=1<?= $filtroPaginacao ?>" aria-label="Primeira página">&laquo;</a></li>
                <li><a href="geral.php?pg=lista_receptoras&amp;pag=<?= $paginaReceptoras - 1 ?><?= $filtroPaginacao ?>">Anterior</a></li>
              <?php endif; ?>
              <?php for ($numeroPagina = max(1, $paginaReceptoras - 2); $numeroPagina <= min($totalPaginas, $paginaReceptoras + 2); $numeroPagina++): ?>
                <li<?= $numeroPagina === $paginaReceptoras ? ' class="active"' : '' ?>><a href="geral.php?pg=lista_receptoras&amp;pag=<?= $numeroPagina ?><?= $filtroPaginacao ?>"<?= $numeroPagina === $paginaReceptoras ? ' aria-current="page"' : '' ?>><?= $numeroPagina ?></a></li>
              <?php endfor; ?>
              <?php if ($paginaReceptoras < $totalPaginas): ?>
                <li><a href="geral.php?pg=lista_receptoras&amp;pag=<?= $paginaReceptoras + 1 ?><?= $filtroPaginacao ?>">Próxima</a></li>
                <li><a href="geral.php?pg=lista_receptoras&amp;pag=<?= $totalPaginas ?><?= $filtroPaginacao ?>" aria-label="Última página">&raquo;</a></li>
              <?php endif; ?>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
