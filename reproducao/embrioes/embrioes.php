<?php
if (empty($_SESSION['embriao_csrf'])) {
    $_SESSION['embriao_csrf'] = bin2hex(random_bytes(32));
}
$embriaoFlash = $_SESSION['embriao_flash'] ?? null;
unset($_SESSION['embriao_flash']);

$opcoesPorPaginaEmbriao = array(10, 20, 50, 100);
$porPaginaInformadaEmbriao = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
$porPaginaEmbriao = in_array($porPaginaInformadaEmbriao, $opcoesPorPaginaEmbriao, true) ? $porPaginaInformadaEmbriao : 10;
$contagem = DBRead('embriao', 'WHERE qtd > 0', 'COUNT(*) AS total') ?: array();
$totalEmbrioes = (int)($contagem[0]['total'] ?? 0);
$totalPaginas = max(1, (int)ceil($totalEmbrioes / $porPaginaEmbriao));
$paginaInformada = filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT);
$pagina = min(max(1, $paginaInformada === false ? 1 : $paginaInformada), $totalPaginas);
$offset = ($pagina - 1) * $porPaginaEmbriao;
$registrosEmbrioes = $totalEmbrioes
    ? (DBRead('embriao', "WHERE qtd > 0 ORDER BY data DESC, id DESC LIMIT $offset,$porPaginaEmbriao") ?: array())
    : array();

$dataInicialVendaEmbriao = trim((string)($_GET['data_inicial'] ?? date('01/m/Y')));
$dataFinalVendaEmbriao = trim((string)($_GET['data_final'] ?? date('t/m/Y')));
$converterDataVendaEmbriao = function ($valor) {
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $valor);
    return $data && $data->format('d/m/Y') === $valor ? $data->format('Y-m-d') : false;
};
$dataInicialVendaEmbriaoBanco = $converterDataVendaEmbriao($dataInicialVendaEmbriao);
$dataFinalVendaEmbriaoBanco = $converterDataVendaEmbriao($dataFinalVendaEmbriao);
$erroPeriodoVendaEmbriao = '';
$vendasEmbriao = array();
if (!$dataInicialVendaEmbriaoBanco || !$dataFinalVendaEmbriaoBanco) {
    $erroPeriodoVendaEmbriao = 'Informe datas válidas no formato dd/mm/aaaa.';
} elseif ($dataInicialVendaEmbriaoBanco > $dataFinalVendaEmbriaoBanco) {
    $erroPeriodoVendaEmbriao = 'A data inicial deve ser anterior ou igual à data final.';
} else {
    $linkVendasEmbriao = DBConnect();
    mysqli_set_charset($linkVendasEmbriao, 'utf8mb4');
    $sqlVendasEmbriao = "SELECT v.*, COALESCE(a.nome, t.nome, 'Animal não encontrado') AS pai_nome,
                         COALESCE(f.nome, ft.nome, 'Animal não encontrado') AS mae_nome,
                         COALESCE(m.nome, 'Cliente não encontrado') AS cliente_nome
                  FROM venda_embriao v
                  LEFT JOIN embriao e ON e.id = v.id_embriao
                  LEFT JOIN animais a ON e.terceiro = 0 AND a.id = e.pai
                  LEFT JOIN terceiros t ON e.terceiro = 1 AND t.id = e.pai
                  LEFT JOIN animais f ON e.terceiro_mae = 0 AND f.id = e.mae
                  LEFT JOIN terceiros ft ON e.terceiro_mae = 1 AND ft.id = e.mae
                  LEFT JOIN mercado m ON m.id = v.comprador
                  WHERE v.data BETWEEN ? AND ?
                  ORDER BY v.data DESC, v.id DESC";
    $stmtVendasEmbriao = mysqli_prepare($linkVendasEmbriao, $sqlVendasEmbriao);
    mysqli_stmt_bind_param($stmtVendasEmbriao, 'ss', $dataInicialVendaEmbriaoBanco, $dataFinalVendaEmbriaoBanco);
    mysqli_stmt_execute($stmtVendasEmbriao);
    $resultadoVendasEmbriao = mysqli_stmt_get_result($stmtVendasEmbriao);
    while ($venda = mysqli_fetch_assoc($resultadoVendasEmbriao)) { $vendasEmbriao[] = $venda; }
    mysqli_stmt_close($stmtVendasEmbriao);
    DBClose($linkVendasEmbriao);
}

$nomesAnimais = array();
$nomesTerceiros = array();
foreach ($registrosEmbrioes as $registro) {
    foreach (array(array('id' => (int)$registro['pai'], 'terceiro' => !empty($registro['terceiro'])), array('id' => (int)$registro['mae'], 'terceiro' => !empty($registro['terceiro_mae']))) as $vinculo) {
        if ($vinculo['id'] < 1) { continue; }
        if ($vinculo['terceiro']) { $nomesTerceiros[$vinculo['id']] = 'Animal não encontrado'; }
        else { $nomesAnimais[$vinculo['id']] = 'Animal não encontrado'; }
    }
}
if ($nomesAnimais) {
    $ids = implode(',', array_keys($nomesAnimais));
    foreach ((DBRead('animais', "WHERE id IN ($ids)", 'id, nome') ?: array()) as $animal) { $nomesAnimais[(int)$animal['id']] = $animal['nome']; }
}
if ($nomesTerceiros) {
    $ids = implode(',', array_keys($nomesTerceiros));
    foreach ((DBRead('terceiros', "WHERE id IN ($ids)", 'id, nome') ?: array()) as $animal) { $nomesTerceiros[(int)$animal['id']] = $animal['nome']; }
}

function embriaoDataBr($valor)
{
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$valor, 0, 10));
    return $data ? $data->format('d/m/Y') : '--';
}

function embriaoUrlPagina($pagina, $porPaginaEmbriao)
{
    return 'geral.php?pg=embrioes&amp;pag=' . (int)$pagina . '&amp;por_pagina=' . (int)$porPaginaEmbriao;
}
?>
<script>
function validarEmbriao() {
  var componentes = document.querySelectorAll('#form-cadastrar-embriao [data-busca-animais]');
  var ids = ['data-embriao', 'qtd-embriao', 'botijao-embriao'];
  var invalido = false;
  componentes.forEach(function (componente) {
    var campoId = componente.querySelector('[data-busca-animais-id]');
    var entrada = componente.querySelector('.sob-busca-animais__input');
    var vazio = !campoId || !campoId.value;
    entrada.style.border = vazio ? '1px solid red' : '';
    if (vazio) { invalido = true; }
  });
  ids.forEach(function (id) {
    var campo = document.getElementById(id);
    var vazio = !campo || !campo.value.trim();
    if (campo) { campo.style.border = vazio ? '1px solid red' : ''; }
    if (vazio) { invalido = true; }
  });
  return !invalido;
}

function abrirAlteracaoEmbriao(botao) {
  var formulario = document.getElementById('form-alterar-embriao');
  formulario.action = 'reproducao/embrioes/_alterar.php?id_embriao=' + encodeURIComponent(botao.getAttribute('data-id'));
  document.getElementById('qtd-alterar-embriao').value = botao.getAttribute('data-qtd');
  document.getElementById('titulo-alterar-embriao').textContent = 'Alterar quantidade — ' + botao.getAttribute('data-descricao');
  jQuery('#modal-alterar-embriao').modal('show');
}

function confirmarExclusaoEmbriao(botao) {
  confirmarExclusao({
    titulo: 'Excluir embrião do banco?',
    nome: botao.getAttribute('data-descricao'),
    descricao: 'O registro e sua quantidade disponível serão excluídos.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/embrioes/_excluir.php?id_embriao=' + encodeURIComponent(botao.getAttribute('data-id'));
    }
  });
}

function confirmarExclusaoVendaEmbriao(botao) {
  confirmarExclusao({
    titulo: 'Excluir venda de embrião?',
    nome: botao.getAttribute('data-descricao'),
    descricao: 'A venda e seu lançamento financeiro vinculado serão excluídos.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/embrioes/_excluir_venda.php?id_venda=' + encodeURIComponent(botao.getAttribute('data-id'));
    }
  });
}
</script>

<section class="content-header">
  <h1>Banco de Embriões</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li class="active">Embriões</li>
  </ol>
</section>

<section class="content">
  <?php if ($embriaoFlash !== null && !empty($embriaoFlash['erro'])): ?>
  <div class="alert alert-danger" role="alert"><?=htmlspecialchars($embriaoFlash['erro'], ENT_QUOTES, 'UTF-8')?></div>
  <?php endif; ?>

  <div class="box" style="border-top:0;">
    <form id="form-cadastrar-embriao" method="post" action="reproducao/embrioes/_cadastrar.php" onsubmit="return validarEmbriao()">
      <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['embriao_csrf'], ENT_QUOTES, 'UTF-8')?>">
      <div class="box-body"><div class="row">
        <div class="col-sm-6 col-md-4"><div class="form-group"><?php renderBuscaAnimais(array('id'=>'macho-embriao','name'=>'macho','name_id'=>'macho_id','name_origem'=>'macho_origem','label'=>'Macho','tipo'=>'machos','required'=>true,'value'=>$embriaoFlash['macho'] ?? '','value_id'=>$embriaoFlash['macho_id'] ?? 0,'value_origem'=>$embriaoFlash['macho_origem'] ?? '')); ?></div></div>
        <div class="col-sm-6 col-md-4"><div class="form-group"><?php renderBuscaAnimais(array('id'=>'femea-embriao','name'=>'femea','name_id'=>'femea_id','name_origem'=>'femea_origem','label'=>'Fêmea','tipo'=>'femeas','required'=>true,'value'=>$embriaoFlash['femea'] ?? '','value_id'=>$embriaoFlash['femea_id'] ?? 0,'value_origem'=>$embriaoFlash['femea_origem'] ?? '')); ?></div></div>
        <div class="col-sm-6 col-md-4"><div class="form-group"><label for="data-embriao">Data<span class="text-danger">*</span></label><div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right" id="data-embriao" name="data" placeholder="dd/mm/aaaa" value="<?=htmlspecialchars($embriaoFlash['data'] ?? '', ENT_QUOTES, 'UTF-8')?>"></div></div></div>
        <div class="col-sm-6 col-md-4"><div class="form-group"><label for="qtd-embriao">Quantidade<span class="text-danger">*</span></label><input type="number" min="1" class="form-control" id="qtd-embriao" name="qtd" value="<?=htmlspecialchars($embriaoFlash['qtd'] ?? '', ENT_QUOTES, 'UTF-8')?>"></div></div>
        <div class="col-sm-6 col-md-4"><div class="form-group"><label for="botijao-embriao">Botijão<span class="text-danger">*</span></label><input type="text" class="form-control" id="botijao-embriao" name="botijao" maxlength="30" value="<?=htmlspecialchars($embriaoFlash['botijao'] ?? '', ENT_QUOTES, 'UTF-8')?>"></div></div>
        <div class="col-sm-6 col-md-4"><div class="form-group"><label for="palheta-embriao">ID Palheta</label><input type="text" class="form-control" id="palheta-embriao" name="palheta" maxlength="30" value="<?=htmlspecialchars($embriaoFlash['palheta'] ?? '', ENT_QUOTES, 'UTF-8')?>"></div></div>
        <div class="col-sm-6 col-md-4"><div class="form-group"><label for="qualidade-embriao">Qualidade</label><input type="text" class="form-control" id="qualidade-embriao" name="qualidade" maxlength="30" value="<?=htmlspecialchars($embriaoFlash['qualidade'] ?? '', ENT_QUOTES, 'UTF-8')?>"></div></div>
        <div class="col-sm-12 text-right"><button type="submit" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> Adicionar embriões</button></div>
      </div></div>
    </form>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr>
            <th>Data</th><th>Macho</th><th>Fêmea</th><th>Quantidade</th><th>Botijão</th><th>ID Palheta</th><th>Qualidade</th>
            <th style="width:1%;"><span class="sr-only">Ações</span></th>
          </tr></thead>
          <tbody>
          <?php if (!$registrosEmbrioes): ?><tr><td colspan="8" class="text-center">Nenhum embrião disponível no banco.</td></tr><?php endif; ?>
          <?php foreach ($registrosEmbrioes as $registro):
            $idPai = (int)$registro['pai']; $idMae = (int)$registro['mae'];
            $nomeMacho = !empty($registro['terceiro']) ? ($nomesTerceiros[$idPai] ?? 'Animal não encontrado') : ($nomesAnimais[$idPai] ?? 'Animal não encontrado');
            $nomeFemea = !empty($registro['terceiro_mae']) ? ($nomesTerceiros[$idMae] ?? 'Animal não encontrado') : ($nomesAnimais[$idMae] ?? 'Animal não encontrado');
            $descricaoEmbriao = $nomeMacho . ' / ' . $nomeFemea;
          ?>
          <tr>
            <td><?=embriaoDataBr($registro['data'])?></td>
            <td><?=htmlspecialchars($nomeMacho, ENT_QUOTES, 'UTF-8')?><?=!empty($registro['terceiro']) ? ' <span class="text-muted">(Terceiro)</span>' : ''?></td>
            <td><?=htmlspecialchars($nomeFemea, ENT_QUOTES, 'UTF-8')?><?=!empty($registro['terceiro_mae']) ? ' <span class="text-muted">(Terceiro)</span>' : ''?></td>
            <td><?=(int)$registro['qtd']?></td>
            <td><?=htmlspecialchars($registro['botijao'], ENT_QUOTES, 'UTF-8')?></td>
            <td><?=htmlspecialchars($registro['palheta'], ENT_QUOTES, 'UTF-8')?></td>
            <td><?=htmlspecialchars($registro['qualidade'], ENT_QUOTES, 'UTF-8')?></td>
            <td style="white-space:nowrap;">
              <button type="button" class="text-primary" style="background:none;border:0;padding:0 5px;" title="Alterar quantidade" aria-label="Alterar quantidade" data-id="<?=(int)$registro['id']?>" data-qtd="<?=(int)$registro['qtd']?>" data-descricao="<?=htmlspecialchars($descricaoEmbriao, ENT_QUOTES, 'UTF-8')?>" onclick="abrirAlteracaoEmbriao(this)"><i class="fa fa-pencil" aria-hidden="true"></i></button>
              <a class="text-success" style="padding:0 5px;" href="geral.php?pg=vender_embrioes&amp;id_embriao=<?=(int)$registro['id']?>" title="Vender" aria-label="Vender"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
              <button type="button" class="text-danger" style="background:none;border:0;padding:0 5px;" title="Excluir" aria-label="Excluir" data-id="<?=(int)$registro['id']?>" data-descricao="<?=htmlspecialchars($descricaoEmbriao, ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoEmbriao(this)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalEmbrioes > 0): ?>
      <div style="display:flex; align-items:center; justify-content:center; flex-wrap:wrap; gap:12px; margin-top:15px;">
        <form method="get" action="geral.php" style="display:flex; align-items:center; gap:8px; margin:0;">
          <input type="hidden" name="pg" value="embrioes">
          <label for="por-pagina-embriao" style="margin:0; font-weight:normal;">Por página</label>
          <select class="form-control input-sm" id="por-pagina-embriao" name="por_pagina" onchange="this.form.submit()" style="width:auto;">
            <?php foreach ($opcoesPorPaginaEmbriao as $opcao): ?><option value="<?=$opcao?>"<?=$opcao === $porPaginaEmbriao ? ' selected' : ''?>><?=$opcao?></option><?php endforeach; ?>
          </select>
        </form>
        <span class="text-muted">Exibindo <?=$offset + 1?> a <?=min($offset + count($registrosEmbrioes), $totalEmbrioes)?> de <?=$totalEmbrioes?> registros</span>
        <?php if ($totalPaginas > 1): ?>
        <nav aria-label="Paginação do banco de embriões"><ul class="pagination pagination-sm no-margin">
          <?php if ($pagina > 1): ?><li><a href="<?=embriaoUrlPagina($pagina - 1, $porPaginaEmbriao)?>" aria-label="Página anterior">&laquo;</a></li><?php else: ?><li class="disabled"><span>&laquo;</span></li><?php endif; ?>
          <?php
          $inicio = max(1, $pagina - 1); $fim = min($totalPaginas, $pagina + 1);
          if ($inicio > 1): ?><li><a href="<?=embriaoUrlPagina(1, $porPaginaEmbriao)?>">1</a></li><?php if ($inicio > 2): ?><li class="disabled"><span>…</span></li><?php endif; endif;
          for ($numero = $inicio; $numero <= $fim; $numero++): ?>
            <li<?=$numero === $pagina ? ' class="active"' : ''?>><a href="<?=embriaoUrlPagina($numero, $porPaginaEmbriao)?>"><?=$numero?></a></li>
          <?php endfor;
          if ($fim < $totalPaginas): if ($fim < $totalPaginas - 1): ?><li class="disabled"><span>…</span></li><?php endif; ?><li><a href="<?=embriaoUrlPagina($totalPaginas, $porPaginaEmbriao)?>"><?=$totalPaginas?></a></li><?php endif; ?>
          <?php if ($pagina < $totalPaginas): ?><li><a href="<?=embriaoUrlPagina($pagina + 1, $porPaginaEmbriao)?>" aria-label="Próxima página">&raquo;</a></li><?php else: ?><li class="disabled"><span>&raquo;</span></li><?php endif; ?>
        </ul></nav>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-header with-border"><h3 class="box-title">Vendas de embriões</h3></div>
    <div class="box-body">
      <form method="get" action="geral.php" style="margin-bottom:15px;">
        <input type="hidden" name="pg" value="embrioes">
        <input type="hidden" name="por_pagina" value="<?=$porPaginaEmbriao?>">
        <div class="row" style="display:flex;flex-wrap:wrap;align-items:flex-end;">
          <div class="col-sm-5 col-md-4"><div class="form-group">
            <label for="data-inicial-venda-embriao">Data inicial<span class="text-danger">*</span></label>
            <div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right" id="data-inicial-venda-embriao" name="data_inicial" value="<?=htmlspecialchars($dataInicialVendaEmbriao, ENT_QUOTES, 'UTF-8')?>" placeholder="dd/mm/aaaa"></div>
          </div></div>
          <div class="col-sm-5 col-md-4"><div class="form-group">
            <label for="data-final-venda-embriao">Data final<span class="text-danger">*</span></label>
            <div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right" id="data-final-venda-embriao" name="data_final" value="<?=htmlspecialchars($dataFinalVendaEmbriao, ENT_QUOTES, 'UTF-8')?>" placeholder="dd/mm/aaaa"></div>
          </div></div>
          <div class="col-sm-2 col-md-4"><div class="form-group"><button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Pesquisar</button></div></div>
        </div>
      </form>

      <?php if ($erroPeriodoVendaEmbriao): ?><div class="alert alert-warning" role="alert"><?=htmlspecialchars($erroPeriodoVendaEmbriao, ENT_QUOTES, 'UTF-8')?></div><?php endif; ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr><th>Data</th><th>Embriões</th><th>Cliente</th><th>Valor</th><th>Parcelas</th><th>Tipo</th><th style="width:1%;"><span class="sr-only">Ações</span></th></tr></thead>
          <tbody>
          <?php if (!$erroPeriodoVendaEmbriao && !$vendasEmbriao): ?><tr><td colspan="7" class="text-center">Nenhuma venda encontrada no período.</td></tr><?php endif; ?>
          <?php foreach ($vendasEmbriao as $venda): ?>
          <tr>
            <td><?=embriaoDataBr($venda['data'])?></td>
            <td><?=htmlspecialchars($venda['pai_nome'] . ' / ' . $venda['mae_nome'], ENT_QUOTES, 'UTF-8')?></td>
            <td><?=htmlspecialchars($venda['cliente_nome'], ENT_QUOTES, 'UTF-8')?></td>
            <td>R$ <?=number_format((float)$venda['valor'], 2, ',', '.')?></td>
            <td><?=htmlspecialchars($venda['parcelas'], ENT_QUOTES, 'UTF-8')?>x</td>
            <td><?=htmlspecialchars(trim($venda['tipo_venda'] . ' - ' . $venda['forma_de_pagamento'], ' -'), ENT_QUOTES, 'UTF-8')?></td>
            <td><button type="button" class="text-danger" style="background:none;border:0;padding:0;" title="Excluir venda" aria-label="Excluir venda" data-id="<?=(int)$venda['id']?>" data-descricao="<?=htmlspecialchars(($venda['pai_nome'] . ' / ' . $venda['mae_nome']) . ' — ' . $venda['cliente_nome'], ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoVendaEmbriao(this)"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="modal-alterar-embriao" tabindex="-1" role="dialog" aria-labelledby="titulo-alterar-embriao">
  <div class="modal-dialog" role="document" style="width:440px;max-width:calc(100vw - 32px);margin:10vh auto;">
    <div class="modal-content" style="border:0;border-radius:12px;">
      <form id="form-alterar-embriao" method="post">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Fechar">&times;</button><h4 class="modal-title" id="titulo-alterar-embriao">Alterar quantidade</h4></div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['embriao_csrf'], ENT_QUOTES, 'UTF-8')?>">
          <div class="form-group"><label for="qtd-alterar-embriao">Quantidade<span class="text-danger">*</span></label><input type="number" min="0" class="form-control" id="qtd-alterar-embriao" name="qtd" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Alterar</button></div>
      </form>
    </div>
  </div>
</div>
