<?php
if (empty($_SESSION['semen_csrf'])) {
    $_SESSION['semen_csrf'] = bin2hex(random_bytes(32));
}
$semenFlash = $_SESSION['semen_flash'] ?? null;
unset($_SESSION['semen_flash']);

$opcoesPorPagina = array(10, 20, 50, 100);
$porPaginaInformada = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
$porPagina = in_array($porPaginaInformada, $opcoesPorPagina, true) ? $porPaginaInformada : 10;
$contagem = DBRead('semen', 'WHERE qtd > 0', 'COUNT(*) AS total') ?: array();
$totalSemen = (int)($contagem[0]['total'] ?? 0);
$totalPaginas = max(1, (int)ceil($totalSemen / $porPagina));
$paginaInformada = filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT);
$pagina = min(max(1, $paginaInformada === false ? 1 : $paginaInformada), $totalPaginas);
$offset = ($pagina - 1) * $porPagina;
$registros = $totalSemen
    ? (DBRead('semen', "WHERE qtd > 0 ORDER BY data DESC, id DESC LIMIT $offset,$porPagina") ?: array())
    : array();

$dataInicialVenda = trim((string)($_GET['data_inicial'] ?? date('01/m/Y')));
$dataFinalVenda = trim((string)($_GET['data_final'] ?? date('t/m/Y')));
$converterDataVenda = function ($valor) {
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $valor);
    return $data && $data->format('d/m/Y') === $valor ? $data->format('Y-m-d') : false;
};
$dataInicialVendaBanco = $converterDataVenda($dataInicialVenda);
$dataFinalVendaBanco = $converterDataVenda($dataFinalVenda);
$erroPeriodoVenda = '';
$vendasSemen = array();
if (!$dataInicialVendaBanco || !$dataFinalVendaBanco) {
    $erroPeriodoVenda = 'Informe datas válidas no formato dd/mm/aaaa.';
} elseif ($dataInicialVendaBanco > $dataFinalVendaBanco) {
    $erroPeriodoVenda = 'A data inicial deve ser anterior ou igual à data final.';
} else {
    $linkVendas = DBConnect();
    mysqli_set_charset($linkVendas, 'utf8mb4');
    $sqlVendas = "SELECT v.*, COALESCE(a.nome, t.nome, 'Animal não encontrado') AS semen_nome,
                         COALESCE(m.nome, 'Cliente não encontrado') AS cliente_nome
                  FROM venda_semen v
                  LEFT JOIN semen s ON s.id = v.id_semen
                  LEFT JOIN animais a ON s.terceiro = 0 AND a.id = s.id_animal
                  LEFT JOIN terceiros t ON s.terceiro = 1 AND t.id = s.id_animal
                  LEFT JOIN mercado m ON m.id = v.comprador
                  WHERE v.data BETWEEN ? AND ?
                  ORDER BY v.data DESC, v.id DESC";
    $stmtVendas = mysqli_prepare($linkVendas, $sqlVendas);
    mysqli_stmt_bind_param($stmtVendas, 'ss', $dataInicialVendaBanco, $dataFinalVendaBanco);
    mysqli_stmt_execute($stmtVendas);
    $resultadoVendas = mysqli_stmt_get_result($stmtVendas);
    while ($venda = mysqli_fetch_assoc($resultadoVendas)) { $vendasSemen[] = $venda; }
    mysqli_stmt_close($stmtVendas);
    DBClose($linkVendas);
}

$nomesAnimais = array();
$nomesTerceiros = array();
foreach ($registros as $registro) {
    $idAnimal = (int)$registro['id_animal'];
    if ($idAnimal < 1) { continue; }
    if (!empty($registro['terceiro'])) { $nomesTerceiros[$idAnimal] = 'Animal não encontrado'; }
    else { $nomesAnimais[$idAnimal] = 'Animal não encontrado'; }
}
if ($nomesAnimais) {
    $ids = implode(',', array_keys($nomesAnimais));
    foreach ((DBRead('animais', "WHERE id IN ($ids)", 'id, nome') ?: array()) as $animal) {
        $nomesAnimais[(int)$animal['id']] = $animal['nome'];
    }
}
if ($nomesTerceiros) {
    $ids = implode(',', array_keys($nomesTerceiros));
    foreach ((DBRead('terceiros', "WHERE id IN ($ids)", 'id, nome') ?: array()) as $animal) {
        $nomesTerceiros[(int)$animal['id']] = $animal['nome'];
    }
}

function semenDataBr($valor)
{
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$valor, 0, 10));
    return $data ? $data->format('d/m/Y') : '--';
}

function semenUrlPagina($pagina, $porPagina)
{
    return 'geral.php?pg=semen&amp;pag=' . (int)$pagina . '&amp;por_pagina=' . (int)$porPagina;
}
?>
<script>
function validarSemen() {
  var componente = document.querySelector('#form-cadastrar-semen [data-busca-animais]');
  var campoId = componente.querySelector('[data-busca-animais-id]');
  var entrada = componente.querySelector('.sob-busca-animais__input');
  var faltantes = [], primeiro;
  var machoInvalido = !campoId.value;
  entrada.style.borderColor = machoInvalido ? '#dd4b39' : '';
  entrada.setAttribute('aria-invalid', String(machoInvalido));
  if (machoInvalido) { faltantes.push('Macho'); primeiro = entrada; }
  var nomes = {'data-semen':'Data', 'qtd-semen':'Quantidade'};
  Object.keys(nomes).forEach(function (id) {
    var campo = document.getElementById(id), vazio = !campo.value.trim();
    campo.style.borderColor = vazio ? '#dd4b39' : '';
    campo.setAttribute('aria-invalid', String(vazio));
    if (vazio) { faltantes.push(nomes[id]); primeiro = primeiro || campo; }
  });
  if (faltantes.length) { SobAlertas.camposObrigatorios(faltantes); primeiro.focus(); return false; }
  return conferirQuantidadeSemen('qtd-semen', 1);
}
function conferirQuantidadeSemen(id, minimo) {
  var campo = document.getElementById(id);
  var valido = /^\d+$/.test(campo.value) && Number(campo.value) >= minimo;
  campo.style.borderColor = valido ? '' : '#dd4b39';
  campo.setAttribute('aria-invalid', String(!valido));
  if (!valido) { SobAlertas.mostrar({tipo:'warning', titulo:'Atenção!', mensagem:'Informe uma quantidade inteira maior ou igual a ' + minimo + '.'}); campo.focus(); }
  return valido;
}
function validarQuantidadeSemen() {
  var campo = document.getElementById('qtd-alterar-semen');
  if (!campo.value.trim()) { campo.style.borderColor = '#dd4b39'; SobAlertas.camposObrigatorios(['Quantidade']); campo.focus(); return false; }
  return conferirQuantidadeSemen('qtd-alterar-semen', 0);
}

function abrirAlteracaoSemen(botao) {
  var formulario = document.getElementById('form-alterar-semen');
  formulario.action = 'reproducao/semen/_alterar.php?id_embriao=' + encodeURIComponent(botao.getAttribute('data-id'));
  document.getElementById('qtd-alterar-semen').value = botao.getAttribute('data-qtd');
  document.getElementById('titulo-alterar-semen').textContent = 'Alterar quantidade — ' + botao.getAttribute('data-macho');
  jQuery('#modal-alterar-semen').modal('show');
}

function confirmarExclusaoSemen(botao) {
  confirmarExclusao({
    titulo: 'Excluir sêmen do banco?',
    nome: botao.getAttribute('data-macho'),
    descricao: 'O registro e sua quantidade disponível serão excluídos.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/semen/_excluir.php?id_embriao=' + encodeURIComponent(botao.getAttribute('data-id'));
    }
  });
}

function confirmarExclusaoVendaSemen(botao) {
  confirmarExclusao({
    titulo: 'Excluir venda de sêmen?',
    nome: botao.getAttribute('data-descricao'),
    descricao: 'A venda e seu lançamento financeiro vinculado serão excluídos.',
    aoConfirmar: function () {
      window.location.href = 'reproducao/semen/_excluir_venda.php?id_venda=' + encodeURIComponent(botao.getAttribute('data-id'));
    }
  });
}
</script>

<section class="content-header">
  <h1>Banco de Sêmen</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li class="active">Sêmen</li>
  </ol>
</section>

<section class="content">


  <div class="box" style="border-top:0;">
    <form id="form-cadastrar-semen" method="post" action="reproducao/semen/_cadastrar.php" onsubmit="return validarSemen()" novalidate>
      <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['semen_csrf'], ENT_QUOTES, 'UTF-8')?>">
      <div class="box-body">
        <div class="row">
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <?php renderBuscaAnimais(array(
                'id' => 'macho-semen',
                'name' => 'macho',
                'name_id' => 'macho_id',
                'name_origem' => 'macho_origem',
                'label' => 'Macho',
                'tipo' => 'machos',
                'required' => true,
                'value' => $semenFlash['macho'] ?? '',
                'value_id' => $semenFlash['macho_id'] ?? 0,
                'value_origem' => $semenFlash['macho_origem'] ?? ''
            )); ?>
          </div></div>
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="data-semen">Data<span class="text-danger">*</span></label>
            <div class="input-group date">
              <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
              <input type="text" class="form-control pull-right" id="data-semen" name="data" placeholder="dd/mm/aaaa" value="<?=htmlspecialchars($semenFlash['data'] ?? '', ENT_QUOTES, 'UTF-8')?>">
            </div>
          </div></div>
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="qtd-semen">Quantidade<span class="text-danger">*</span></label>
            <input type="number" min="1" class="form-control" id="qtd-semen" name="qtd" value="<?=htmlspecialchars($semenFlash['qtd'] ?? '', ENT_QUOTES, 'UTF-8')?>">
          </div></div>
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="botijao-semen">Botijão</label>
            <input type="text" class="form-control" id="botijao-semen" name="botijao" maxlength="100" value="<?=htmlspecialchars($semenFlash['botijao'] ?? '', ENT_QUOTES, 'UTF-8')?>">
          </div></div>
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="palheta-semen">ID Palheta</label>
            <input type="text" class="form-control" id="palheta-semen" name="palheta" maxlength="30" value="<?=htmlspecialchars($semenFlash['palheta'] ?? '', ENT_QUOTES, 'UTF-8')?>">
          </div></div>
          <div class="col-sm-6 col-md-4"><div class="form-group">
            <label for="qualidade-semen">Qualidade</label>
            <input type="text" class="form-control" id="qualidade-semen" name="qualidade" maxlength="30" value="<?=htmlspecialchars($semenFlash['qualidade'] ?? '', ENT_QUOTES, 'UTF-8')?>">
          </div></div>
          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> Adicionar sêmen</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr>
            <th>Data</th><th>Macho</th><th>Quantidade</th><th>Botijão</th><th>ID Palheta</th><th>Qualidade</th>
            <th style="width:1%;"><span class="sr-only">Ações</span></th>
          </tr></thead>
          <tbody>
          <?php if (!$registros): ?><tr><td colspan="7" class="text-center">Nenhum sêmen disponível no banco.</td></tr><?php endif; ?>
          <?php foreach ($registros as $registro):
            $idAnimal = (int)$registro['id_animal'];
            $nomeMacho = !empty($registro['terceiro']) ? ($nomesTerceiros[$idAnimal] ?? 'Animal não encontrado') : ($nomesAnimais[$idAnimal] ?? 'Animal não encontrado');
          ?>
          <tr>
            <td><?=semenDataBr($registro['data'])?></td>
            <td><?=htmlspecialchars($nomeMacho, ENT_QUOTES, 'UTF-8')?><?=!empty($registro['terceiro']) ? ' <span class="text-muted">(Terceiro)</span>' : ''?></td>
            <td><?=(int)$registro['qtd']?></td>
            <td><?=htmlspecialchars($registro['botijao'], ENT_QUOTES, 'UTF-8')?></td>
            <td><?=htmlspecialchars($registro['palheta'], ENT_QUOTES, 'UTF-8')?></td>
            <td><?=htmlspecialchars($registro['qualidade'], ENT_QUOTES, 'UTF-8')?></td>
            <td style="white-space:nowrap;">
              <button type="button" class="text-primary" style="background:none;border:0;padding:0 5px;" title="Alterar quantidade" aria-label="Alterar quantidade" data-id="<?=(int)$registro['id']?>" data-qtd="<?=(int)$registro['qtd']?>" data-macho="<?=htmlspecialchars($nomeMacho, ENT_QUOTES, 'UTF-8')?>" onclick="abrirAlteracaoSemen(this)"><i class="fa fa-pencil" aria-hidden="true"></i></button>
              <a class="text-success" style="padding:0 5px;" href="geral.php?pg=vender_semen&amp;id_embriao=<?=(int)$registro['id']?>" title="Vender" aria-label="Vender"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
              <button type="button" class="text-danger" style="background:none;border:0;padding:0 5px;" title="Excluir" aria-label="Excluir" data-id="<?=(int)$registro['id']?>" data-macho="<?=htmlspecialchars($nomeMacho, ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoSemen(this)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalSemen > 0): ?>
      <div style="display:flex; align-items:center; justify-content:center; flex-wrap:wrap; gap:12px; margin-top:15px;">
        <form method="get" action="geral.php" style="display:flex; align-items:center; gap:8px; margin:0;">
          <input type="hidden" name="pg" value="semen">
          <label for="por-pagina-semen" style="margin:0; font-weight:normal;">Por página</label>
          <select class="form-control input-sm" id="por-pagina-semen" name="por_pagina" onchange="this.form.submit()" style="width:auto;">
            <?php foreach ($opcoesPorPagina as $opcao): ?><option value="<?=$opcao?>"<?=$opcao === $porPagina ? ' selected' : ''?>><?=$opcao?></option><?php endforeach; ?>
          </select>
        </form>
        <span class="text-muted">Exibindo <?=$offset + 1?> a <?=min($offset + count($registros), $totalSemen)?> de <?=$totalSemen?> registros</span>
        <?php if ($totalPaginas > 1): ?>
        <nav aria-label="Paginação do banco de sêmen"><ul class="pagination pagination-sm no-margin">
          <?php if ($pagina > 1): ?><li><a href="<?=semenUrlPagina($pagina - 1, $porPagina)?>" aria-label="Página anterior">&laquo;</a></li><?php else: ?><li class="disabled"><span>&laquo;</span></li><?php endif; ?>
          <?php
          $inicio = max(1, $pagina - 1); $fim = min($totalPaginas, $pagina + 1);
          if ($inicio > 1): ?><li><a href="<?=semenUrlPagina(1, $porPagina)?>">1</a></li><?php if ($inicio > 2): ?><li class="disabled"><span>…</span></li><?php endif; endif;
          for ($numero = $inicio; $numero <= $fim; $numero++): ?>
            <li<?=$numero === $pagina ? ' class="active"' : ''?>><a href="<?=semenUrlPagina($numero, $porPagina)?>"><?=$numero?></a></li>
          <?php endfor;
          if ($fim < $totalPaginas): if ($fim < $totalPaginas - 1): ?><li class="disabled"><span>…</span></li><?php endif; ?><li><a href="<?=semenUrlPagina($totalPaginas, $porPagina)?>"><?=$totalPaginas?></a></li><?php endif; ?>
          <?php if ($pagina < $totalPaginas): ?><li><a href="<?=semenUrlPagina($pagina + 1, $porPagina)?>" aria-label="Próxima página">&raquo;</a></li><?php else: ?><li class="disabled"><span>&raquo;</span></li><?php endif; ?>
        </ul></nav>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-header with-border"><h3 class="box-title">Vendas de sêmen</h3></div>
    <div class="box-body">
      <form method="get" action="geral.php" style="margin-bottom:15px;">
        <input type="hidden" name="pg" value="semen">
        <input type="hidden" name="por_pagina" value="<?=$porPagina?>">
        <div class="row" style="display:flex;flex-wrap:wrap;align-items:flex-end;">
          <div class="col-sm-5 col-md-4"><div class="form-group">
            <label for="data-inicial-venda">Data inicial<span class="text-danger">*</span></label>
            <div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right" id="data-inicial-venda" name="data_inicial" value="<?=htmlspecialchars($dataInicialVenda, ENT_QUOTES, 'UTF-8')?>" placeholder="dd/mm/aaaa"></div>
          </div></div>
          <div class="col-sm-5 col-md-4"><div class="form-group">
            <label for="data-final-venda">Data final<span class="text-danger">*</span></label>
            <div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right" id="data-final-venda" name="data_final" value="<?=htmlspecialchars($dataFinalVenda, ENT_QUOTES, 'UTF-8')?>" placeholder="dd/mm/aaaa"></div>
          </div></div>
          <div class="col-sm-2 col-md-4"><div class="form-group"><button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Pesquisar</button></div></div>
        </div>
      </form>

      <?php if ($erroPeriodoVenda): ?><div class="alert alert-warning" role="alert"><?=htmlspecialchars($erroPeriodoVenda, ENT_QUOTES, 'UTF-8')?></div><?php endif; ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr><th>Data</th><th>Sêmen</th><th>Cliente</th><th>Valor</th><th>Parcelas</th><th>Tipo</th><th style="width:1%;"><span class="sr-only">Ações</span></th></tr></thead>
          <tbody>
          <?php if (!$erroPeriodoVenda && !$vendasSemen): ?><tr><td colspan="7" class="text-center">Nenhuma venda encontrada no período.</td></tr><?php endif; ?>
          <?php foreach ($vendasSemen as $venda): ?>
          <tr>
            <td><?=semenDataBr($venda['data'])?></td>
            <td><?=htmlspecialchars($venda['semen_nome'], ENT_QUOTES, 'UTF-8')?></td>
            <td><?=htmlspecialchars($venda['cliente_nome'], ENT_QUOTES, 'UTF-8')?></td>
            <td>R$ <?=number_format((float)$venda['valor'], 2, ',', '.')?></td>
            <td><?=htmlspecialchars($venda['parcelas'], ENT_QUOTES, 'UTF-8')?>x</td>
            <td><?=htmlspecialchars(trim($venda['tipo_venda'] . ' - ' . $venda['forma_de_pagamento'], ' -'), ENT_QUOTES, 'UTF-8')?></td>
            <td><button type="button" class="text-danger" style="background:none;border:0;padding:0;" title="Excluir venda" aria-label="Excluir venda" data-id="<?=(int)$venda['id']?>" data-descricao="<?=htmlspecialchars($venda['semen_nome'] . ' — ' . $venda['cliente_nome'], ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoVendaSemen(this)"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="modal-alterar-semen" tabindex="-1" role="dialog" aria-labelledby="titulo-alterar-semen">
  <div class="modal-dialog" role="document" style="width:440px;max-width:calc(100vw - 32px);margin:10vh auto;">
    <div class="modal-content" style="border:0;border-radius:12px;">
      <form id="form-alterar-semen" method="post" onsubmit="return validarQuantidadeSemen()" novalidate>
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Fechar">&times;</button><h4 class="modal-title" id="titulo-alterar-semen">Alterar quantidade</h4></div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['semen_csrf'], ENT_QUOTES, 'UTF-8')?>">
          <div class="form-group"><label for="qtd-alterar-semen">Quantidade<span class="text-danger">*</span></label><input type="number" min="0" class="form-control" id="qtd-alterar-semen" name="qtd" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Alterar</button></div>
      </form>
    </div>
  </div>
</div>
