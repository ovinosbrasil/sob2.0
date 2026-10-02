<?php
$hDetalheExposicao = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$id_evento = filter_var($_GET['id_exposicao'] ?? 0, FILTER_VALIDATE_INT);
$id_evento = $id_evento && $id_evento > 0 ? (int)$id_evento : 0;
$eventosExposicao = $id_evento ? (DBRead('julgamento', "WHERE id = '$id_evento' LIMIT 1") ?: array()) : array();
$eventoExposicao = $eventosExposicao[0] ?? null;
if (!$eventoExposicao):
?>
<section class="content-header"><h1>Exposição</h1></section>
<section class="content"><div class="alert alert-warning">Exposição não encontrada.</div><a class="btn btn-default" href="geral.php?pg=cadastrar_exposicao">Voltar</a></section>
<?php return; endif;
$dataEvento = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$eventoExposicao['data'], 0, 10));
$animaisExposicao = DBRead(
    'animais_evento ae INNER JOIN animais a ON a.id = ae.id_animal',
    "WHERE ae.id_julgamento = '$id_evento' ORDER BY ae.id DESC",
    "ae.*, a.nome, a.tipo, a.sexo,
     CASE WHEN a.sexo = 'Macho' THEN (SELECT COUNT(*) FROM animais cria WHERE cria.pai = a.id AND COALESCE(cria.terceiro_pai, 0) = 0)
          WHEN a.sexo = 'Fêmea' THEN (SELECT COUNT(*) FROM animais cria WHERE cria.mae = a.id AND COALESCE(cria.terceiro_mae, 0) = 0)
          ELSE 0 END AS quantidade_crias,
     (SELECT COUNT(*) FROM vendas v WHERE v.id_animal = a.id) AS quantidade_vendas"
) ?: array();
$porPaginaAnimaisExposicao = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPaginaAnimaisExposicao, array(10, 20, 50, 100), true)) $porPaginaAnimaisExposicao = 10;
$totalAnimaisExposicao = count($animaisExposicao);
$paginasAnimaisExposicao = max(1, (int)ceil($totalAnimaisExposicao / $porPaginaAnimaisExposicao));
$paginaAnimaisExposicao = filter_var($_GET['pag'] ?? 1, FILTER_VALIDATE_INT);
$paginaAnimaisExposicao = min($paginasAnimaisExposicao, max(1, $paginaAnimaisExposicao ? (int)$paginaAnimaisExposicao : 1));
$offsetAnimaisExposicao = ($paginaAnimaisExposicao - 1) * $porPaginaAnimaisExposicao;
$animaisPaginaExposicao = array_slice($animaisExposicao, $offsetAnimaisExposicao, $porPaginaAnimaisExposicao);
$urlPaginaAnimaisExposicao = function ($pagina) use ($id_evento, $porPaginaAnimaisExposicao, $hDetalheExposicao) {
    return $hDetalheExposicao('geral.php?' . http_build_query(array('pg' => 'exposicao', 'id_exposicao' => $id_evento, 'por_pagina' => $porPaginaAnimaisExposicao, 'pag' => $pagina)));
};
?>
<section class="content-header">
  <h1><?=$hDetalheExposicao($eventoExposicao['nome'])?></h1>
  <ol class="breadcrumb"><li><a href="geral.php?pg=cadastrar_exposicao"><i class="fa fa-trophy"></i> Exposição</a></li><li class="active">Detalhes</li></ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-header"><h2 class="box-title" style="font-size:16px;">Dados da exposição</h2></div>
    <form method="post" action="exposicao/_alterar.php?id_evento=<?=$id_evento?>">
      <div class="box-body"><div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group col-sm-6 col-md-3"><label for="evento">Evento<span class="text-danger">*</span></label><input type="text" class="form-control" id="evento" name="evento" value="<?=$hDetalheExposicao($eventoExposicao['nome'])?>" required></div>
        <div class="form-group col-sm-6 col-md-3"><label for="data">Data<span class="text-danger">*</span></label><div class="input-group date"><span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="data" name="data" maxlength="10" value="<?=$dataEvento ? $dataEvento->format('d/m/Y') : ''?>" required></div></div>
        <div class="form-group col-sm-6 col-md-2"><label for="cidade">Cidade</label><input type="text" class="form-control" id="cidade" name="cidade" value="<?=$hDetalheExposicao($eventoExposicao['cidade'])?>"></div>
        <div class="form-group col-sm-6 col-md-2"><label for="local">Local</label><input type="text" class="form-control" id="local" name="local" value="<?=$hDetalheExposicao($eventoExposicao['local'])?>"></div>
        <div class="form-group col-sm-6 col-md-2"><button type="submit" class="btn btn-warning btn-block"><i class="fa fa-save" aria-hidden="true"></i> Salvar alterações</button></div>
      </div></div>
    </form>
  </div>

  <div class="box" style="border-top:0;">
    <div class="box-body">
      <?php $abaInicialExposicao = in_array(($_GET['aba'] ?? ''), array('animais', 'julgamento', 'progenie_pai', 'progenie_mae', 'vendas'), true) ? $_GET['aba'] : 'animais'; $abaExposicao = $abaInicialExposicao; include __DIR__ . '/abas_exposicao.php'; ?>
      <div id="conteudo-aba-exposicao" aria-live="polite">
      <div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:15px; margin-bottom:15px;">
        <div style="width:100%; max-width:520px;"><?php renderBuscaAnimais(array('id' => 'animal-exposicao', 'name' => 'animal_exposicao', 'label' => 'Adicionar animal', 'tipo' => 'rebanho', 'placeholder' => 'Digite para pesquisar', 'classe' => 'busca-animal-exposicao')); ?></div>
      </div>
      <div class="table-responsive"><table class="table table-bordered table-striped">
        <thead><tr><th style="width:60px;">Nº</th><th>Animal</th><th>Tipo</th><th>Sexo</th><th>Crias</th><th>Pista de julgamento</th><th>Leilão</th><th>Venda</th><th style="width:1%;"><span class="sr-only">Excluir</span></th></tr></thead>
        <tbody>
        <?php if (!$animaisPaginaExposicao): ?><tr><td colspan="9" class="text-center text-muted" style="padding:30px;">Nenhum animal cadastrado nesta exposição.</td></tr><?php endif; ?>
        <?php foreach ($animaisPaginaExposicao as $indiceAnimal => $animalExposicao): $idAnimalExposicao = (int)$animalExposicao['id_animal']; ?>
          <tr>
            <td><?=$offsetAnimaisExposicao + $indiceAnimal + 1?></td>
            <td><a href="geral.php?pg=animal&amp;id_animal=<?=$idAnimalExposicao?>" style="color:inherit;"><?=$hDetalheExposicao($animalExposicao['nome'])?></a></td>
            <td><?=$hDetalheExposicao($animalExposicao['tipo'])?></td><td><?=$hDetalheExposicao($animalExposicao['sexo'])?></td>
            <td class="<?=($animalExposicao['sexo'] === 'Fêmea' && (int)$animalExposicao['quantidade_crias'] === 0) ? 'text-danger' : ''?>"><?=(int)$animalExposicao['quantidade_crias']?></td>
            <?php
              $naPistaExposicao = !empty($animalExposicao['julgamento']);
              $urlPistaExposicao = $naPistaExposicao ? 'exposicao/_excluir_julgamento.php' : 'exposicao/_animal_julgamento.php';
              $noLeilaoExposicao = !empty($animalExposicao['leilao']);
              $urlLeilaoExposicao = $noLeilaoExposicao ? 'exposicao/_excluir_leilao.php' : 'exposicao/_animal_leilao.php';
            ?>
            <td><div class="sob-controle-status"><button type="button" class="sob-interruptor" role="switch" aria-checked="<?=$naPistaExposicao ? 'true' : 'false'?>" aria-label="Pista de julgamento: <?=$naPistaExposicao ? 'Sim' : 'Não'?>" title="Alterar para <?=$naPistaExposicao ? 'Não' : 'Sim'?>" onclick="window.location.href='<?=$urlPistaExposicao?>?id_animal=<?=$idAnimalExposicao?>&amp;id_evento=<?=$id_evento?>'"><span class="sob-interruptor__indicador"><i class="fa <?=$naPistaExposicao ? 'fa-check' : 'fa-minus'?>" aria-hidden="true"></i></span></button><span class="sob-controle-status__texto" style="color:<?=$naPistaExposicao ? '#008d4c' : '#777'?>;"><?=$naPistaExposicao ? 'Sim' : 'Não'?></span></div></td>
            <td><div class="sob-controle-status"><button type="button" class="sob-interruptor" role="switch" aria-checked="<?=$noLeilaoExposicao ? 'true' : 'false'?>" aria-label="Leilão: <?=$noLeilaoExposicao ? 'Sim' : 'Não'?>" title="Alterar para <?=$noLeilaoExposicao ? 'Não' : 'Sim'?>" onclick="window.location.href='<?=$urlLeilaoExposicao?>?id_animal=<?=$idAnimalExposicao?>&amp;id_evento=<?=$id_evento?>'"><span class="sob-interruptor__indicador"><i class="fa <?=$noLeilaoExposicao ? 'fa-check' : 'fa-minus'?>" aria-hidden="true"></i></span></button><span class="sob-controle-status__texto" style="color:<?=$noLeilaoExposicao ? '#008d4c' : '#777'?>;"><?=$noLeilaoExposicao ? 'Sim' : 'Não'?></span></div></td>
            <td><?php if ((int)$animalExposicao['quantidade_vendas'] > 0): ?><span class="label label-success">Vendido</span><?php else: ?><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-venda-exposicao" data-vender-animal data-id-animal="<?=$idAnimalExposicao?>" data-nome-animal="<?=$hDetalheExposicao($animalExposicao['nome'])?>">Vender</button><?php endif; ?></td>
            <td><button type="button" class="text-danger" style="background:none; border:0; padding:0; cursor:pointer;" data-remover-animal-exposicao data-url="exposicao/_excluir_animal.php?id_animal=<?=$idAnimalExposicao?>&amp;id_evento=<?=$id_evento?>" data-nome="<?=$hDetalheExposicao($animalExposicao['nome'])?>" title="Remover animal" aria-label="Remover <?=$hDetalheExposicao($animalExposicao['nome'])?>"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
      <form id="paginacao-animais-exposicao" action="geral.php" method="get" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px; border-top:1px solid #f4f4f4; padding-top:10px;">
        <input type="hidden" name="pg" value="exposicao"><input type="hidden" name="id_exposicao" value="<?=$id_evento?>">
        <label for="por-pagina-animais-exposicao" style="margin:0; font-weight:normal;">Por página</label>
        <select id="por-pagina-animais-exposicao" name="por_pagina" class="form-control" style="width:70px;" onchange="this.form.submit();"><?php foreach (array(10, 20, 50, 100) as $limiteAnimalExposicao): ?><option value="<?=$limiteAnimalExposicao?>" <?=$limiteAnimalExposicao === $porPaginaAnimaisExposicao ? 'selected' : ''?>><?=$limiteAnimalExposicao?></option><?php endforeach; ?></select>
        <span class="text-muted">Exibindo <?=$totalAnimaisExposicao ? $offsetAnimaisExposicao + 1 : 0?> a <?=min($offsetAnimaisExposicao + $porPaginaAnimaisExposicao, $totalAnimaisExposicao)?> de <?=$totalAnimaisExposicao?> animais</span>
        <nav aria-label="Páginas dos animais da exposição"><ul class="pagination pagination-sm" style="margin:0;">
          <li class="<?=$paginaAnimaisExposicao === 1 ? 'disabled' : ''?>"><?php if ($paginaAnimaisExposicao > 1): ?><a href="<?=$urlPaginaAnimaisExposicao($paginaAnimaisExposicao - 1)?>">«</a><?php else: ?><span>«</span><?php endif; ?></li>
          <?php for ($p = 1; $p <= $paginasAnimaisExposicao; $p++): if ($p !== 1 && $p !== $paginasAnimaisExposicao && abs($p - $paginaAnimaisExposicao) > 1) { if ($p === 2 || $p === $paginasAnimaisExposicao - 1) echo '<li class="disabled"><span>…</span></li>'; continue; } ?><li class="<?=$p === $paginaAnimaisExposicao ? 'active' : ''?>"><a href="<?=$urlPaginaAnimaisExposicao($p)?>"><?=$p?></a></li><?php endfor; ?>
          <li class="<?=$paginaAnimaisExposicao === $paginasAnimaisExposicao ? 'disabled' : ''?>"><?php if ($paginaAnimaisExposicao < $paginasAnimaisExposicao): ?><a href="<?=$urlPaginaAnimaisExposicao($paginaAnimaisExposicao + 1)?>">»</a><?php else: ?><span>»</span><?php endif; ?></li>
        </ul></nav>
      </form>
      </div>
    </div>
  </div>
</section>
<div class="modal fade" id="modal-venda-exposicao" tabindex="-1" role="dialog" aria-labelledby="titulo-modal-venda-exposicao">
  <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
    <form id="form-venda-exposicao" method="post" action="exposicao/_vender_animal.php">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Fechar">&times;</button><h4 class="modal-title" id="titulo-modal-venda-exposicao">Cadastrar venda</h4></div>
      <div class="modal-body"><div class="row">
        <div class="form-group col-sm-6"><label for="animal-venda-exposicao">Animal</label><input type="text" class="form-control" id="animal-venda-exposicao" name="animal" readonly></div>
        <div class="form-group col-sm-6" style="position:relative;"><label for="comprador">Comprador<span class="text-danger">*</span></label><input type="text" class="form-control" id="comprador" name="comprador" autocomplete="off" onkeyup="pesquisar_comprador(this.value)" required><input type="hidden" id="comprador_id" name="comprador_id"><div id="lista_comprador" class="sob-busca-animais__resultados" style="position:absolute; z-index:1051; left:15px; right:15px; display:none;"></div></div>
        <div class="form-group col-sm-6 col-md-3"><label for="tipo-venda-exposicao">Tipo de venda<span class="text-danger">*</span></label><select class="form-control" id="tipo-venda-exposicao" name="tipo_venda" required><option value="Exposição">Exposição</option><option value="Leilão">Leilão</option></select></div>
        <div class="form-group col-sm-6 col-md-3"><label for="parcelas-venda-exposicao">Parcelas</label><select class="form-control" id="parcelas-venda-exposicao" name="parcelas"><?php for ($parcelaExposicao = 1; $parcelaExposicao <= 24; $parcelaExposicao++): ?><option value="<?=$parcelaExposicao?>"><?=$parcelaExposicao?>x</option><?php endfor; ?></select></div>
        <div class="form-group col-sm-6 col-md-3"><label for="data-venda-exposicao">Data<span class="text-danger">*</span></label><div class="input-group date"><span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="data-venda-exposicao" name="data" maxlength="10" placeholder="dd/mm/aaaa" required></div></div>
        <div class="form-group col-sm-6 col-md-3"><label for="valor">Valor<span class="text-danger">*</span></label><input type="text" class="form-control" id="valor" name="valor" required></div>
        <div class="form-group col-sm-6"><label for="forma-venda-exposicao">Forma de pagamento</label><select class="form-control" id="forma-venda-exposicao" name="forma"><option value="">Selecionar</option><?php foreach (array('Boleto', 'Cheque', 'Dinheiro', 'Depósito', 'Transferência', 'Troca') as $formaExposicao): ?><option value="<?=$hDetalheExposicao($formaExposicao)?>"><?=$hDetalheExposicao($formaExposicao)?></option><?php endforeach; ?></select></div>
        <div class="form-group col-sm-6"><label for="observacoes-venda-exposicao">Observações</label><textarea class="form-control" id="observacoes-venda-exposicao" name="observacoes" rows="3"></textarea></div>
      </div></div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Cadastrar venda</button></div>
    </form>
  </div></div>
</div>
<script>
document.addEventListener('buscaanimais:selecionado', function (evento) {
  var animal = evento.detail || {};
  if (!animal.id || animal.origem !== 'rebanho') return;
  if (evento.target.classList.contains('busca-animal-exposicao')) window.location.href = 'exposicao/_cadastrar_animal.php?id_animal=' + encodeURIComponent(animal.id) + '&id_evento=<?=$id_evento?>';
  if (evento.target.classList.contains('busca-animal-pista-exposicao')) window.location.href = 'exposicao/_cadastrar_animal_completo.php?id_animal=' + encodeURIComponent(animal.id) + '&id_evento=<?=$id_evento?>';
});
function prepararVendaExposicao(botao) {
  if (!botao) return;
  var idAnimal = botao.getAttribute('data-id-animal');
  var formulario = document.getElementById('form-venda-exposicao');
  formulario.reset();
  formulario.action = 'exposicao/_vender_animal.php?id_evento=<?=$id_evento?>&id_animal=' + encodeURIComponent(idAnimal);
  document.getElementById('animal-venda-exposicao').value = botao.getAttribute('data-nome-animal') || '';
  document.getElementById('comprador_id').value = '';
  var listaComprador = document.getElementById('lista_comprador');
  listaComprador.style.display = 'none';
}
document.querySelectorAll('[data-vender-animal]').forEach(function (botao) {
  botao.addEventListener('click', function () { prepararVendaExposicao(botao); });
});
(function () {
  'use strict';
  var conteudo = document.getElementById('conteudo-aba-exposicao');
  var conteudoAnimais = conteudo.innerHTML;
  var abaAtual = '<?=$hDetalheExposicao($abaInicialExposicao)?>';
  function marcarAba(aba) {
    document.querySelectorAll('[data-aba-exposicao]').forEach(function (link) {
      var ativa = link.getAttribute('data-aba-exposicao') === aba;
      link.parentNode.classList.toggle('active', ativa);
      if (ativa) link.setAttribute('aria-current', 'page'); else link.removeAttribute('aria-current');
    });
  }
  function prepararConteudoDinamico() {
    if (window.BuscaAnimais) window.BuscaAnimais.iniciarTodos(conteudo);
    var paginacao = conteudo.querySelector('[data-paginacao-aba-exposicao]');
    if (!paginacao) return;
    var seletor = paginacao.querySelector('[name="por_pagina"]');
    seletor.addEventListener('change', function () { carregarAba(abaAtual, 1, seletor.value); });
    paginacao.querySelectorAll('[data-pagina]').forEach(function (link) { link.addEventListener('click', function (evento) { evento.preventDefault(); if (!link.parentNode.classList.contains('disabled')) carregarAba(abaAtual, link.getAttribute('data-pagina'), seletor.value); }); });
  }
  function carregarAba(aba, pagina, porPagina) {
    abaAtual = aba;
    marcarAba(aba);
    if (aba === 'animais') { conteudo.innerHTML = conteudoAnimais; if (window.BuscaAnimais) window.BuscaAnimais.iniciarTodos(conteudo); history.replaceState(null, '', 'geral.php?pg=exposicao&id_exposicao=<?=$id_evento?>'); return; }
    conteudo.innerHTML = '<div class="text-center text-muted" style="padding:40px;"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Carregando...</div>';
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'exposicao/aba_exposicao.php?id_exposicao=<?=$id_evento?>&aba=' + encodeURIComponent(aba) + '&pag=' + encodeURIComponent(pagina || 1) + '&por_pagina=' + encodeURIComponent(porPagina || 10), true);
    xhr.onreadystatechange = function () { if (xhr.readyState !== 4) return; if (xhr.status >= 200 && xhr.status < 300) { conteudo.innerHTML = xhr.responseText; prepararConteudoDinamico(); history.replaceState(null, '', 'geral.php?pg=exposicao&id_exposicao=<?=$id_evento?>&aba=' + encodeURIComponent(aba)); } else conteudo.innerHTML = '<div class="alert alert-warning">Não foi possível carregar esta aba.</div>'; };
    xhr.send(null);
  }
  document.querySelectorAll('[data-aba-exposicao]').forEach(function (link) { link.addEventListener('click', function (evento) { evento.preventDefault(); carregarAba(link.getAttribute('data-aba-exposicao'), 1, 10); }); });
  document.addEventListener('click', function (evento) {
    var removerAnimal = evento.target.closest('[data-remover-animal-exposicao]');
    if (removerAnimal) { window.confirmarExclusao({titulo:'Remover animal da exposição?',nome:removerAnimal.getAttribute('data-nome'),descricao:'O cadastro do animal será mantido. Apenas o vínculo com esta exposição será removido.',aoConfirmar:function(){window.location.href=removerAnimal.getAttribute('data-url').replace('&amp;','&');}}); }
    var removerPista = evento.target.closest('[data-remover-pista]');
    if (removerPista) { window.confirmarExclusao({titulo:'Remover animal da pista?',nome:removerPista.getAttribute('data-nome'),descricao:'O animal continuará cadastrado no evento.',aoConfirmar:function(){window.location.href='exposicao/_excluir_julgamento.php?id_animal='+encodeURIComponent(removerPista.getAttribute('data-id'))+'&id_evento=<?=$id_evento?>&tipo=1';}}); }
    var excluirVenda = evento.target.closest('[data-excluir-venda-exposicao]');
    if (excluirVenda) { window.confirmarExclusao({titulo:'Excluir venda?',nome:excluirVenda.getAttribute('data-nome'),descricao:'A venda e os lançamentos financeiros relacionados serão removidos.',aoConfirmar:function(){window.location.href='exposicao/_excluir_venda.php?id_animal='+encodeURIComponent(excluirVenda.getAttribute('data-id'))+'&id_evento=<?=$id_evento?>';}}); }
  });
  if (abaAtual !== 'animais') carregarAba(abaAtual, 1, 10);
})();
</script>
