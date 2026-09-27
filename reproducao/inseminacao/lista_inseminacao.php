<?php
function dataInseminacao($valor)
{
    $valor = substr((string)$valor, 0, 10);
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
    return $data && $data->format('Y-m-d') === $valor ? $data : null;
}

$pai = isset($_GET['pai']) && is_string($_GET['pai']) ? trim($_GET['pai']) : '';
$mae = isset($_GET['mae']) && is_string($_GET['mae']) ? trim($_GET['mae']) : '';
$porPagina = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) { $porPagina = 10; }

$lotesPermitidos = null;
if ($mae !== '') {
    $maeEscapada = DBEscape($mae);
    $cadastroMae = DBRead('animais', "WHERE nome = '$maeEscapada' AND sexo = 'Fêmea'");
    $terceiroMae = 0;
    if (!$cadastroMae) {
        $cadastroMae = DBRead('terceiros', "WHERE nome = '$maeEscapada' AND sexo = 'Fêmea'");
        $terceiroMae = 1;
    }
    $lotesPermitidos = array();
    if ($cadastroMae) {
        $idMae = (int)$cadastroMae[0]['id'];
        $controles = DBRead('inseminacao_controle', "WHERE id_femea = '$idMae' AND terceiro = '$terceiroMae'") ?: array();
        foreach ($controles as $controle) { $lotesPermitidos[(int)$controle['id_lote']] = true; }
    }
}

$lotes = DBRead('inseminacao', 'ORDER BY id DESC') ?: array();
$lotesFiltrados = array();
foreach ($lotes as $lote) {
    if ($lotesPermitidos !== null && !isset($lotesPermitidos[(int)$lote['id']])) { continue; }
    $idMacho = (int)$lote['id_macho'];
    $macho = DBRead(!empty($lote['terceiro']) ? 'terceiros' : 'animais', "WHERE id = '$idMacho'");
    $nomeMacho = $macho[0]['nome'] ?? '--';
    if ($pai !== '' && strcasecmp($nomeMacho, $pai) !== 0) { continue; }
    $lote['nome_macho'] = $nomeMacho;
    $lotesFiltrados[] = $lote;
}

$totalLotes = count($lotesFiltrados);
$totalPaginas = max(1, (int)ceil($totalLotes / $porPagina));
$paginaInformada = filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT);
$pagina = min(max(1, $paginaInformada === false ? 1 : $paginaInformada), $totalPaginas);
$inicio = ($pagina - 1) * $porPagina;
$lotesPagina = array_slice($lotesFiltrados, $inicio, $porPagina);
$parametrosPagina = array('pg' => 'lista_inseminacao', 'pai' => $pai, 'mae' => $mae, 'por_pagina' => $porPagina);
$urlPagina = 'geral.php?' . htmlspecialchars(http_build_query($parametrosPagina), ENT_QUOTES, 'UTF-8');
?>
<script>
function inseminacao_lote(id) {
    if (/^\d+$/.test(String(id)) && Number(id) > 0) {
        window.location.href = 'geral.php?pg=inseminacao&id_lote=' + encodeURIComponent(id);
    }
}
document.addEventListener('buscaanimais:selecionado', function (evento) {
    var componente = evento.target;
    if (!componente.classList.contains('busca-mae-filtro-inseminacao') &&
        !componente.classList.contains('busca-pai-filtro-inseminacao')) { return; }
    document.getElementById('filtros-inseminacao').submit();
});
function confirmarExclusaoLoteInseminacao(botao) {
    var id = botao.getAttribute('data-id');
    if (!/^\d+$/.test(id) || Number(id) < 1) { return; }
    confirmarExclusao({
        titulo: 'Excluir lote de inseminação artificial?',
        nome: botao.getAttribute('data-nome'),
        descricao: 'Confirme se deseja excluir este lote. Esta ação não pode ser desfeita.',
        aoConfirmar: function () {
            window.location.href = 'reproducao/inseminacao/_excluir_lote.php?id_lote=' + encodeURIComponent(id);
        }
    });
}
</script>

<section class="content-header">
  <h1>Inseminação artificial</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li class="active">Inseminação artificial</li>
  </ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <form id="filtros-inseminacao" action="geral.php" method="get">
        <input type="hidden" name="pg" value="lista_inseminacao">
        <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <div class="form-group col-sm-6 col-md-3">
            <label for="lote-inseminacao">Lote</label>
            <select class="form-control" id="lote-inseminacao" onchange="inseminacao_lote(this.value)">
              <option value="">Selecionar</option>
              <?php foreach ($lotes as $loteSelecao):
                $idMachoSelecao = (int)$loteSelecao['id_macho'];
                $machoSelecao = DBRead(!empty($loteSelecao['terceiro']) ? 'terceiros' : 'animais', "WHERE id = '$idMachoSelecao'");
                $rotuloLote = $loteSelecao['codigo'] . ' - ' . ($machoSelecao[0]['nome'] ?? '--');
              ?>
              <option value="<?=(int)$loteSelecao['id']?>"><?=htmlspecialchars($rotuloLote, ENT_QUOTES, 'UTF-8')?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <?php renderBuscaAnimais(array(
                'id' => 'filtro-mae-inseminacao',
                'name' => 'mae',
                'name_id' => 'mae_id',
                'name_origem' => 'mae_origem',
                'label' => 'Mãe',
                'tipo' => 'femeas',
                'value' => $mae,
                'limite_origem' => 5,
                'classe' => 'busca-mae-filtro-inseminacao'
            )); ?>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <?php renderBuscaAnimais(array(
                'id' => 'filtro-pai-inseminacao',
                'name' => 'pai',
                'name_id' => 'pai_id',
                'name_origem' => 'pai_origem',
                'label' => 'Pai',
                'tipo' => 'machos',
                'value' => $pai,
                'limite_origem' => 5,
                'classe' => 'busca-pai-filtro-inseminacao'
            )); ?>
          </div>
          <div class="form-group col-sm-6 col-md-3">
            <button type="submit" class="btn btn-primary">Pesquisar</button>
            <a class="btn btn-default" href="geral.php?pg=lista_inseminacao">Limpar</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px;">
        <h3 class="box-title" style="font-size:16px; margin:0;"><?=$pai !== '' || $mae !== '' ? 'Resultado da pesquisa' : 'Últimos lotes cadastrados'?></h3>
        <a href="geral.php?pg=cadastrar_inseminacao" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> Adicionar novo lote</a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr><th>Lote</th><th>Macho</th><th>Data</th><th>Previsão</th><th style="width:1%;"><span class="sr-only">Ações</span></th></tr></thead>
          <tbody>
            <?php if (!$lotesPagina): ?><tr><td colspan="5" class="text-center">Nenhum lote encontrado.</td></tr><?php endif; ?>
            <?php foreach ($lotesPagina as $lote):
                $data = dataInseminacao($lote['data']);
                $dataFormatada = $data ? $data->format('d/m/Y') : '--';
                $previsaoInicio = $data ? $data->modify('+140 days')->format('d/m/Y') : '--';
                $previsaoFim = $data ? $data->modify('+160 days')->format('d/m/Y') : '--';
            ?>
            <tr>
              <td onclick="inseminacao_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=htmlspecialchars($lote['codigo'], ENT_QUOTES, 'UTF-8')?></td>
              <td onclick="inseminacao_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=htmlspecialchars($lote['nome_macho'], ENT_QUOTES, 'UTF-8')?></td>
              <td onclick="inseminacao_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=$dataFormatada?></td>
              <td onclick="inseminacao_lote(<?=(int)$lote['id']?>)" style="cursor:pointer;"><?=$previsaoInicio?> até <?=$previsaoFim?></td>
              <td style="white-space:nowrap;">
                <button type="button" class="text-primary" style="background:none; border:0; padding:0; margin-right:10px;" onclick="inseminacao_lote(<?=(int)$lote['id']?>)" title="Abrir lote" aria-label="Abrir lote"><i class="fa fa-search" aria-hidden="true"></i></button>
                <a href="reproducao/inseminacao/_imprimir.php?id_lote=<?=(int)$lote['id']?>" target="_blank" rel="noopener" class="text-muted" style="margin-right:10px;" title="Gerar PDF" aria-label="Gerar PDF"><i class="fa fa-print" aria-hidden="true"></i></a>
                <button type="button" class="text-danger" style="background:none; border:0; padding:0;" data-id="<?=(int)$lote['id']?>" data-nome="<?=htmlspecialchars('Lote ' . $lote['codigo'], ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoLoteInseminacao(this)" title="Excluir lote" aria-label="Excluir lote"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
        <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
          <input type="hidden" name="pg" value="lista_inseminacao">
          <input type="hidden" name="pai" value="<?=htmlspecialchars($pai, ENT_QUOTES, 'UTF-8')?>">
          <input type="hidden" name="mae" value="<?=htmlspecialchars($mae, ENT_QUOTES, 'UTF-8')?>">
          <label for="por-pagina-inseminacao" style="margin:0; font-weight:normal;">Por página</label>
          <select id="por-pagina-inseminacao" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
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
