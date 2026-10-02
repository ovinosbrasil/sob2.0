<?php
require_once __DIR__ . '/nomes_lotes.php';
function dataTransplanteLista($valor)
{
    $valor = substr((string)$valor, 0, 10);
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
    return $data && $data->format('Y-m-d') === $valor ? $data : null;
}

function filtroAnimalTransplante($nome, $id, $origem, $sexo, $campoId, $campoTerceiro)
{
    $nome = trim((string)$nome);
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if ($id && in_array($origem, array('rebanho', 'terceiros'), true)) {
        return array(
            $campoId . " = '" . (int)$id . "'",
            $campoTerceiro . " = '" . ($origem === 'terceiros' ? 1 : 0) . "'"
        );
    }
    if ($nome === '') { return array(); }

    $nomeEscapado = DBEscape($nome);
    $animal = DBRead('animais', "WHERE nome = '$nomeEscapado' AND sexo = '$sexo'") ?: array();
    $terceiro = 0;
    if (!$animal) {
        $animal = DBRead('terceiros', "WHERE nome = '$nomeEscapado' AND sexo = '$sexo'") ?: array();
        $terceiro = 1;
    }
    $idAnimal = (int)($animal[0]['id'] ?? 0);
    return array($campoId . " = '$idAnimal'", $campoTerceiro . " = '$terceiro'");
}

$pai = isset($_GET['pai']) && is_string($_GET['pai']) ? trim($_GET['pai']) : '';
$mae = isset($_GET['mae']) && is_string($_GET['mae']) ? trim($_GET['mae']) : '';
$paiId = $_GET['pai_id'] ?? '';
$paiOrigem = isset($_GET['pai_origem']) && is_string($_GET['pai_origem']) ? $_GET['pai_origem'] : '';
$maeId = $_GET['mae_id'] ?? '';
$maeOrigem = isset($_GET['mae_origem']) && is_string($_GET['mae_origem']) ? $_GET['mae_origem'] : '';

$condicoes = array_merge(
    filtroAnimalTransplante($pai, $paiId, $paiOrigem, 'Macho', 'id_pai', 'terceiro_pai'),
    filtroAnimalTransplante($mae, $maeId, $maeOrigem, 'Fêmea', 'id_mae', 'terceiro_mae')
);
$where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

$porPagina = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) { $porPagina = 10; }
$contagem = DBRead('transplante', $where, 'COUNT(*) AS total') ?: array();
$totalLotes = (int)($contagem[0]['total'] ?? 0);
$totalPaginas = max(1, (int)ceil($totalLotes / $porPagina));
$paginaInformada = filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT);
$pagina = min(max(1, $paginaInformada === false ? 1 : $paginaInformada), $totalPaginas);
$inicio = ($pagina - 1) * $porPagina;
$lotesPagina = DBRead('transplante', "$where ORDER BY data DESC, id DESC LIMIT $inicio, $porPagina") ?: array();
$lotesSelecao = nomesLotesTransplante(DBRead('transplante', 'ORDER BY id DESC') ?: array());
$nomesPorLote = array_column($lotesSelecao, null, 'id');
foreach ($lotesPagina as &$lotePagina) {
    if (isset($nomesPorLote[$lotePagina['id']])) $lotePagina = array_merge($lotePagina, $nomesPorLote[$lotePagina['id']]);
}
unset($lotePagina);

$parametrosPagina = array(
    'pg' => 'lista_te',
    'pai' => $pai,
    'pai_id' => $paiId,
    'pai_origem' => $paiOrigem,
    'mae' => $mae,
    'mae_id' => $maeId,
    'mae_origem' => $maeOrigem,
    'por_pagina' => $porPagina
);
$urlPagina = 'geral.php?' . htmlspecialchars(http_build_query($parametrosPagina), ENT_QUOTES, 'UTF-8');
?>
<script>
function te_lote(id) {
    if (/^\d+$/.test(String(id)) && Number(id) > 0) {
        window.location.href = 'geral.php?pg=te&id_lote=' + encodeURIComponent(id);
    }
}

document.addEventListener('buscaanimais:selecionado', function (evento) {
    var componente = evento.target;
    if (!componente.classList.contains('busca-mae-filtro-te') &&
        !componente.classList.contains('busca-pai-filtro-te')) { return; }
    document.getElementById('filtros-te').submit();
});

function confirmarExclusaoLoteTe(botao) {
    var id = botao.getAttribute('data-id');
    if (!/^\d+$/.test(id) || Number(id) < 1) { return; }
    confirmarExclusao({
        titulo: 'Excluir lote de transplante de embriões?',
        nome: botao.getAttribute('data-nome'),
        descricao: 'Confirme se deseja excluir este lote. Esta ação não pode ser desfeita.',
        aoConfirmar: function () {
            window.location.href = 'reproducao/te/_excluir_lote.php?id_lote=' + encodeURIComponent(id);
        }
    });
}
</script>

<section class="content-header">
  <h1>Transplante de embriões</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li class="active">Transplante de embriões</li>
  </ol>
</section>

<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <form id="filtros-te" action="geral.php" method="get">
        <input type="hidden" name="pg" value="lista_te">
        <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="form-group col-sm-6 col-md-3">
            <label for="lote-te">Lote</label>
            <select class="form-control" id="lote-te" onchange="te_lote(this.value)">
              <option value="">Selecionar</option>
              <?php foreach ($lotesSelecao as $loteSelecao):
                $rotulo = $loteSelecao['codigo'] . ' — Macho: ' . $loteSelecao['nome_pai'] . ' — Fêmea: ' . $loteSelecao['nome_mae'];
              ?>
              <option value="<?=(int)$loteSelecao['id']?>"><?=htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8')?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <?php renderBuscaAnimais(array(
                'id' => 'filtro-mae-te',
                'name' => 'mae',
                'name_id' => 'mae_id',
                'name_origem' => 'mae_origem',
                'label' => 'Mãe',
                'tipo' => 'femeas',
                'value' => $mae,
                'limite_origem' => 5,
                'classe' => 'busca-mae-filtro-te'
            )); ?>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <?php renderBuscaAnimais(array(
                'id' => 'filtro-pai-te',
                'name' => 'pai',
                'name_id' => 'pai_id',
                'name_origem' => 'pai_origem',
                'label' => 'Pai',
                'tipo' => 'machos',
                'value' => $pai,
                'limite_origem' => 5,
                'classe' => 'busca-pai-filtro-te'
            )); ?>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <button type="submit" class="btn btn-primary">Pesquisar</button>
            <a class="btn btn-default" href="geral.php?pg=lista_te">Limpar</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;">
        <h3 class="box-title" style="font-size:16px; margin:0;"><?=$pai !== '' || $mae !== '' ? 'Resultado da pesquisa' : 'Últimos lotes cadastrados'?></h3>
        <a href="geral.php?pg=cadastrar_te" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> Adicionar novo lote</a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr>
            <th>Lote</th>
            <th>Macho</th>
            <th>Macho complementar</th>
            <th>Fêmea</th>
            <th>Data</th>
            <th>Previsão</th>
            <th style="width:1%;"><span class="sr-only">Ações</span></th>
          </tr></thead>
          <tbody>
          <?php if (!$lotesPagina): ?><tr><td colspan="7" class="text-center">Nenhum lote encontrado.</td></tr><?php endif; ?>
          <?php foreach ($lotesPagina as $lote):
              $nomePai = $lote['nome_pai'] ?? 'Não informado';
              $nomeMae = $lote['nome_mae'] ?? 'Não informado';
              $nomePaiComplementar = $lote['nome_pai_complementar'] ?? 'Não informado';

              $data = dataTransplanteLista($lote['data']);
              $dataFormatada = $data ? $data->format('d/m/Y') : '--';
              $previsaoInicio = $data ? $data->modify('+146 days')->format('d/m/Y') : '--';
              $previsaoFim = $data ? $data->modify('+161 days')->format('d/m/Y') : '--';
          ?>
            <tr>
              <td onclick="te_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=htmlspecialchars($lote['codigo'], ENT_QUOTES, 'UTF-8')?></td>
              <td onclick="te_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=htmlspecialchars($nomePai, ENT_QUOTES, 'UTF-8')?></td>
              <td onclick="te_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=htmlspecialchars($nomePaiComplementar, ENT_QUOTES, 'UTF-8')?></td>
              <td onclick="te_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=htmlspecialchars($nomeMae, ENT_QUOTES, 'UTF-8')?></td>
              <td onclick="te_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;">Inicial: <?=$dataFormatada?></td>
              <td onclick="te_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=$previsaoInicio?> até <?=$previsaoFim?></td>
              <td style="white-space:nowrap;">
                <button type="button" class="text-primary" style="background:none; border:0; padding:0; margin-right:10px;" onclick="te_lote(<?=(int)$lote['id']?>)" title="Abrir lote" aria-label="Abrir lote"><i class="fa fa-search" aria-hidden="true"></i></button>
                <a href="reproducao/te/_imprimir.php?id_lote=<?=(int)$lote['id']?>" target="_blank" rel="noopener" class="text-muted" style="margin-right:10px;" title="Gerar PDF" aria-label="Gerar PDF"><i class="fa fa-print" aria-hidden="true"></i></a>
                <button type="button" class="text-danger" style="background:none; border:0; padding:0;" data-id="<?=(int)$lote['id']?>" data-nome="<?=htmlspecialchars('Lote ' . $lote['codigo'], ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoLoteTe(this)" title="Excluir lote" aria-label="Excluir lote"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
        <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
          <?php foreach ($parametrosPagina as $nomeParametro => $valorParametro): if ($nomeParametro === 'por_pagina') { continue; } ?>
          <input type="hidden" name="<?=htmlspecialchars($nomeParametro, ENT_QUOTES, 'UTF-8')?>" value="<?=htmlspecialchars((string)$valorParametro, ENT_QUOTES, 'UTF-8')?>">
          <?php endforeach; ?>
          <label for="por-pagina-te" style="margin:0; font-weight:normal;">Por página</label>
          <select id="por-pagina-te" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
            <?php foreach (array(10, 20, 50, 100) as $quantidade): ?><option value="<?=$quantidade?>" <?=$porPagina === $quantidade ? 'selected' : ''?>><?=$quantidade?></option><?php endforeach; ?>
          </select>
        </form>
        <span class="text-muted">Exibindo <?=$totalLotes ? $inicio + 1 : 0?> a <?=min($inicio + $porPagina, $totalLotes)?> de <?=$totalLotes?> lotes</span>
        <?php if ($totalPaginas > 1):
          $visiveis = array(1, $totalPaginas);
          for ($n = max(1, $pagina - 1); $n <= min($totalPaginas, $pagina + 1); $n++) { $visiveis[] = $n; }
          $visiveis = array_values(array_unique($visiveis)); sort($visiveis); $anterior = 0;
        ?>
        <nav aria-label="Paginação dos lotes"><ul class="pagination pagination-sm no-margin">
          <li class="<?=$pagina === 1 ? 'disabled' : ''?>"><a href="<?=$pagina === 1 ? '#' : $urlPagina . '&amp;pag=' . ($pagina - 1)?>">&laquo;</a></li>
          <?php foreach ($visiveis as $n): ?>
            <?php if ($anterior && $n > $anterior + 1): ?><li class="disabled"><span>&hellip;</span></li><?php endif; ?>
            <li class="<?=$n === $pagina ? 'active' : ''?>"><a href="<?=$urlPagina?>&amp;pag=<?=$n?>"><?=$n?></a></li>
          <?php $anterior = $n; endforeach; ?>
          <li class="<?=$pagina === $totalPaginas ? 'disabled' : ''?>"><a href="<?=$pagina === $totalPaginas ? '#' : $urlPagina . '&amp;pag=' . ($pagina + 1)?>">&raquo;</a></li>
        </ul></nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
