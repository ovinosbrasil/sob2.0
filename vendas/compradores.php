<?php
require_once __DIR__ . '/../includes/busca_compradores.php';
function compradoresH($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$buscaComprador = trim(isset($_GET['busca']) ? $_GET['busca'] : '');
$paginaComprador = max(1, (int)(isset($_GET['pagina']) ? $_GET['pagina'] : 1));
$porPaginaComprador = (int)(isset($_GET['por_pagina']) ? $_GET['por_pagina'] : 10);
if (!in_array($porPaginaComprador, array(10, 20, 50, 100), true)) $porPaginaComprador = 10;

$condicaoComprador = '';
if ($buscaComprador !== '') {
    $buscaCompradorSql = DBEscape($buscaComprador);
    $condicaoComprador = "WHERE nome LIKE '%$buscaCompradorSql%' OR email LIKE '%$buscaCompradorSql%' OR cpf LIKE '%$buscaCompradorSql%' OR cidade LIKE '%$buscaCompradorSql%'";
}

$todosCompradores = DBRead('mercado', trim($condicaoComprador . ' ORDER BY nome ASC'));
$totalCompradores = count((array)$todosCompradores);
$totalPaginasCompradores = max(1, (int)ceil($totalCompradores / $porPaginaComprador));
$paginaComprador = min($paginaComprador, $totalPaginasCompradores);
$inicioCompradores = ($paginaComprador - 1) * $porPaginaComprador;
$compradores = DBRead('mercado', trim($condicaoComprador . " ORDER BY nome ASC LIMIT $inicioCompradores, $porPaginaComprador"));

function urlCompradores($alteracoes = array()) {
    $parametros = array(
        'pg' => 'compradores',
        'busca' => isset($_GET['busca']) ? $_GET['busca'] : '',
        'por_pagina' => isset($_GET['por_pagina']) ? $_GET['por_pagina'] : 10,
        'pagina' => isset($_GET['pagina']) ? $_GET['pagina'] : 1
    );
    foreach ($alteracoes as $chave => $valor) {
        if ($valor === null || $valor === '') unset($parametros[$chave]);
        else $parametros[$chave] = $valor;
    }
    return 'geral.php?' . http_build_query($parametros);
}
?>


<script>
document.addEventListener('buscacompradores:selecionado', function (evento) {
  if (!evento.target.closest('#form-pesquisa-compradores')) return;
  document.getElementById('form-pesquisa-compradores').submit();
});

function excluirComprador(id, nome) {
  confirmarExclusao({
    titulo: 'Excluir comprador?',
    nome: nome,
    descricao: 'O cadastro do comprador será removido. Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      var dados = new FormData();
      dados.append('id_comprador', id);

      fetch('vendas/_excluir_comprador.php', {
        method: 'POST',
        body: dados,
        credentials: 'same-origin'
      })
      .then(function (resposta) { return resposta.json(); })
      .then(function (resultado) {
        if (resultado.sucesso) {
          window.location.reload();
          return;
        }
        jQuery('#confirmacao-exclusao').modal('hide');
        window.alert(resultado.mensagem);
      })
      .catch(function () {
        jQuery('#confirmacao-exclusao').modal('hide');
        window.alert('Não foi possível excluir o comprador. Tente novamente.');
      });
    }
  });
}
</script>

<section class="content-header">
  <h1>Compradores</h1>
  <ol class="breadcrumb">
    <li><a href="geral.php?pg=relatorio_venda"><i class="fa fa-shopping-cart"></i> Vendas</a></li>
    <li class="active">Compradores</li>
  </ol>
</section>

<style>
  #pagina-compradores .box { border-top: 0; }
  #pagina-compradores .box-header { padding: 15px; }
  #pagina-compradores .box-title { font-size: 16px; }
  #pagina-compradores .acoes-filtro { padding-top: 24px; white-space: nowrap; }
  #pagina-compradores .cabecalho-lista { display: flex; align-items: center; justify-content: space-between; gap: 15px; }
  #pagina-compradores .rodape-lista { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 16px; border-top: 1px solid #f4f4f4; padding: 12px 15px; }
  #pagina-compradores .rodape-lista .pagination { margin: 0; }
  #pagina-compradores .acao-comprador { width: 72px; text-align: center; white-space: nowrap; }
  #pagina-compradores .acao-comprador a,
  #pagina-compradores .acao-comprador button { display: inline-block; margin: 0 5px; vertical-align: middle; }
  #pagina-compradores .acao-comprador .excluir-comprador,
  #pagina-compradores .acao-comprador .excluir-comprador:hover,
  #pagina-compradores .acao-comprador .excluir-comprador:focus { color: #dd4b39 !important; }
  #pagina-compradores .acoes-cadastro { text-align: right; }
  #pagina-compradores .form-control,
  #pagina-compradores .form-control:hover,
  #pagina-compradores .form-control:focus {
    background-color: #fff !important;
    color: #555 !important;
  }
  #pagina-compradores input.form-control:-webkit-autofill,
  #pagina-compradores input.form-control:-webkit-autofill:hover,
  #pagina-compradores input.form-control:-webkit-autofill:focus {
    -webkit-text-fill-color: #555 !important;
    -webkit-box-shadow: 0 0 0 1000px #fff inset !important;
    box-shadow: 0 0 0 1000px #fff inset !important;
  }
  @media (max-width: 767px) {
    #pagina-compradores .acoes-filtro { padding-top: 0; }
    #pagina-compradores .acoes-filtro .btn { margin-bottom: 5px; }
    #pagina-compradores .cabecalho-lista { align-items: flex-start; flex-direction: column; }
    #pagina-compradores .cabecalho-lista .btn { width: 100%; }
    #pagina-compradores .acoes-cadastro .btn { width: 100%; margin-top: 5px; }
  }
</style>

<section class="content" id="pagina-compradores">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Cadastrar comprador</h3>
    </div>
    <div class="box-body">
      <form method="post" action="vendas/_comprador.php" autocomplete="off">
        <div class="row">
          <div class="col-md-4"><div class="form-group"><label for="nome">Nome completo<span class="text-danger">*</span></label><input type="text" class="form-control" id="nome" name="nome" required></div></div>
          <div class="col-md-4"><div class="form-group"><label for="email">E-mail</label><input type="email" class="form-control" id="email" name="email"></div></div>
          <div class="col-md-4"><div class="form-group"><label for="celular">Celular (WhatsApp)</label><input type="text" class="form-control" id="celular" name="celular"></div></div>
        </div>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label for="cpf">CPF</label><input type="text" class="form-control" id="cpf" name="cpf"></div></div>
          <div class="col-md-3"><div class="form-group"><label for="cod">Cód. criador</label><input type="text" class="form-control" id="cod" name="cod"></div></div>
          <div class="col-md-3"><div class="form-group"><label for="cidade">Cidade</label><input type="text" class="form-control" id="cidade" name="cidade"></div></div>
          <div class="col-md-3"><div class="form-group"><label for="estado">Estado</label><select class="form-control select" name="estado" id="estado"><option value="">Selecionar</option><?php $estados = DBRead('estado', 'ORDER BY estado ASC'); foreach ((array)$estados as $estado): ?><option value="<?=compradoresH($estado['sigla'])?>"><?=compradoresH($estado['estado'])?></option><?php endforeach; ?></select></div></div>
        </div>
        <div class="acoes-cadastro">
          <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Cadastrar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Compradores cadastrados</h3>
    </div>
    <div class="box-body">
      <form method="get" action="geral.php" id="form-pesquisa-compradores">
        <input type="hidden" name="pg" value="compradores">
        <div class="row">
          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <?php renderBuscaCompradores(array('id'=>'busca-comprador','name'=>'busca','name_id'=>'busca_id','label'=>'Pesquisar','value'=>$buscaComprador,'placeholder'=>'Digite para pesquisar','botao_busca'=>true)); ?>
            </div>
          </div>
          <?php if ($buscaComprador !== ''): ?><div class="col-md-2 acoes-filtro"><a href="geral.php?pg=compradores" class="btn btn-default">Limpar</a></div><?php endif; ?>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-striped" style="margin-bottom:0;">
          <thead><tr><th style="width:60px;">Nº</th><th>Nome</th><th>E-mail</th><th>Celular</th><th>CPF</th><th>Cidade</th><th>Estado</th><th class="acao-comprador"><span class="sr-only">Ações</span></th></tr></thead>
          <tbody>
          <?php if (!$compradores): ?>
            <tr><td colspan="8" class="text-center text-muted" style="padding:30px;">Nenhum comprador encontrado.</td></tr>
          <?php else: foreach ($compradores as $indice => $comprador): ?>
            <tr>
              <td><?=$inicioCompradores+$indice+1?></td>
              <td><a href="geral.php?pg=comprador&amp;id_comprador=<?=(int)$comprador['id']?>" style="color:inherit;"><?=compradoresH($comprador['nome'])?></a></td>
              <td><?=compradoresH($comprador['email'])?></td>
              <td><?=compradoresH($comprador['celular1'])?></td>
              <td><?=compradoresH($comprador['cpf'])?></td>
              <td><?=compradoresH($comprador['cidade'])?></td>
              <td><?=compradoresH($comprador['estado'])?></td>
              <td class="acao-comprador"><a href="geral.php?pg=comprador&amp;id_comprador=<?=(int)$comprador['id']?>" title="Abrir comprador"><i class="fa fa-search"></i></a><button type="button" class="btn btn-link excluir-comprador" style="padding:0;" title="Excluir comprador" onclick="excluirComprador(<?=(int)$comprador['id']?>, <?=compradoresH(json_encode($comprador['nome'], JSON_UNESCAPED_UNICODE))?>)"><i class="fa fa-trash-o"></i></button></td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="rodape-lista">
      <form method="get" action="geral.php" style="display:flex;align-items:center;gap:8px;margin:0;">
        <input type="hidden" name="pg" value="compradores">
        <?php if ($buscaComprador !== ''): ?><input type="hidden" name="busca" value="<?=compradoresH($buscaComprador)?>"><?php endif; ?>
        <label for="por-pagina-compradores" style="margin:0;font-weight:normal;">Por página</label>
        <select id="por-pagina-compradores" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
          <?php foreach (array(10,20,50,100) as $quantidade): ?><option value="<?=$quantidade?>" <?=$porPaginaComprador===$quantidade?'selected':''?>><?=$quantidade?></option><?php endforeach; ?>
        </select>
      </form>
      <span class="text-muted">Exibindo <?=$totalCompradores?$inicioCompradores+1:0?> a <?=min($inicioCompradores+$porPaginaComprador,$totalCompradores)?> de <?=$totalCompradores?> compradores</span>
      <?php if ($totalPaginasCompradores > 1): ?>
      <ul class="pagination pagination-sm">
        <li class="<?=$paginaComprador===1?'disabled':''?>"><a href="<?=$paginaComprador===1?'#':compradoresH(urlCompradores(array('pagina'=>$paginaComprador-1)))?>">«</a></li>
        <?php for ($numeroPagina=1; $numeroPagina<=$totalPaginasCompradores; $numeroPagina++):
          if ($numeroPagina!==1 && $numeroPagina!==$totalPaginasCompradores && abs($numeroPagina-$paginaComprador)>1) {
            if ($numeroPagina===2 || $numeroPagina===$totalPaginasCompradores-1) echo '<li class="disabled"><span>…</span></li>';
            continue;
          }
        ?>
          <li class="<?=$numeroPagina===$paginaComprador?'active':''?>"><a href="<?=compradoresH(urlCompradores(array('pagina'=>$numeroPagina)))?>"><?=$numeroPagina?></a></li>
        <?php endfor; ?>
        <li class="<?=$paginaComprador===$totalPaginasCompradores?'disabled':''?>"><a href="<?=$paginaComprador===$totalPaginasCompradores?'#':compradoresH(urlCompradores(array('pagina'=>$paginaComprador+1)))?>">»</a></li>
      </ul>
      <?php endif; ?>
    </div>
  </div>
</section>
