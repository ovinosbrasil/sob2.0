<?php require __DIR__ . '/_lista.php'; ?>
<section class="content-header">
  <h1>Cadastrar chip</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-magic"></i> Chip</a></li>
    <li class="active">Cadastrar</li>
  </ol>
</section>
<section class="content">
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <form id="form-pesquisa-chip">
        <div class="row">
          <div class="form-group col-sm-6 col-md-4">
            <label for="animal-chip">Pesquisar animal</label>
            <div class="input-group">
              <input type="text" name="animal" class="form-control" id="animal-chip" value="<?=htmlspecialchars($nomeChip, ENT_QUOTES, 'UTF-8')?>" placeholder="Digite para pesquisar" autocomplete="off" aria-controls="tabela_animais">
              <span class="input-group-btn"><button type="submit" class="btn btn-primary" title="Pesquisar" aria-label="Pesquisar"><i class="fa fa-search" aria-hidden="true"></i></button></span>
            </div>
          </div>
          <div class="form-group col-sm-3">
            <label class="hidden-xs" aria-hidden="true">&nbsp;</label>
            <div><button type="button" class="btn btn-default" id="limpar-pesquisa-chip">Limpar</button></div>
          </div>
        </div>
      </form>
      <p class="help-block" style="margin-bottom:0;">Pesquise um animal para cadastrar seu chip. Sem pesquisa, são exibidos os animais com chip cadastrado.</p>
      <p id="pesquisa-chip-erro" class="text-danger" role="alert" hidden></p>
    </div>
  </div>
  <div class="box" style="border-top:0;">
    <div class="box-body" id="tabela_animais" aria-live="polite">
      <?php require __DIR__ . '/_tabela.php'; ?>
    </div>
  </div>
</section>
<div class="modal fade" id="cadastro-chip-modal" tabindex="-1" role="dialog" aria-labelledby="cadastro-chip-titulo">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:10px;">
      <form method="post" id="form-cadastro-chip" action="chip/animal/_cadastrar.php">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="cadastro-chip-titulo">Cadastrar chip</h4>
        </div>
        <div class="modal-body">
          <p><strong id="cadastro-chip-animal"></strong></p>
          <p class="help-block">Aproxime o leitor do chip do animal ou informe o código abaixo.</p>
          <div class="form-group">
            <label for="chip">Código do chip<span class="text-danger">*</span></label>
            <input type="text" name="chip" class="form-control" id="chip" autocomplete="off" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Cadastrar chip</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
function cadastrarChip(botao) {
  var formulario = document.getElementById('form-cadastro-chip');
  formulario.reset();
  formulario.action = 'chip/animal/_cadastrar.php?id_animal=' + encodeURIComponent(botao.getAttribute('data-id'));
  document.getElementById('cadastro-chip-animal').textContent = botao.getAttribute('data-nome');
  jQuery('#cadastro-chip-modal').modal('show');
}
function excluirChip(botao) {
  confirmarExclusao({
    titulo: 'Excluir chip?',
    nome: botao.getAttribute('data-nome'),
    descricao: 'Confirme se deseja remover o chip vinculado a este animal. O cadastro do animal será mantido.',
    aoConfirmar: function () {
      window.location.href = 'chip/animal/_excluir.php?id_animal=' + encodeURIComponent(botao.getAttribute('data-id'));
    }
  });
}
document.addEventListener('DOMContentLoaded', function () {
  var campo = document.getElementById('animal-chip');
  var temporizador, requisicao;
  var tabela = document.getElementById('tabela_animais');
  var erro = document.getElementById('pesquisa-chip-erro');
  var porPagina = <?=$porPaginaChip?>;
  function pesquisar(pagina) {
    pagina = Number.isInteger(pagina) && pagina > 0 ? pagina : 1;
    if (requisicao) requisicao.abort();
    erro.hidden = true;
    var atual = new XMLHttpRequest();
    requisicao = atual;
    tabela.setAttribute('aria-busy', 'true');
    atual.open('GET', 'chip/animal/tabela_animais.php?nome=' + encodeURIComponent(campo.value.trim()) + '&pagina=' + pagina + '&por_pagina=' + porPagina, true);
    atual.onload = function () {
      tabela.setAttribute('aria-busy', 'false');
      if (atual.status >= 200 && atual.status < 300) tabela.innerHTML = atual.responseText;
      else falha();
    };
    function falha() {
      tabela.setAttribute('aria-busy', 'false');
      erro.textContent = 'Não foi possível pesquisar os animais. Tente novamente.';
      erro.hidden = false;
    }
    atual.onerror = falha;
    atual.send();
  }
  tabela.addEventListener('click', function (evento) {
    var link = evento.target.closest('[data-pagina-chip]');
    if (!link) return;
    evento.preventDefault(); clearTimeout(temporizador);
    pesquisar(Number(link.getAttribute('data-pagina-chip')));
  });
  tabela.addEventListener('change', function (evento) {
    if (evento.target.id !== 'por-pagina-chip') return;
    porPagina = Number(evento.target.value);
    clearTimeout(temporizador); pesquisar(1);
  });
  campo.addEventListener('input', function () {
    clearTimeout(temporizador);
    if (requisicao) requisicao.abort();
    temporizador = setTimeout(pesquisar, 250);
  });
  document.getElementById('form-pesquisa-chip').addEventListener('submit', function (evento) {
    evento.preventDefault(); clearTimeout(temporizador); pesquisar();
  });
  document.getElementById('limpar-pesquisa-chip').addEventListener('click', function () {
    campo.value = ''; clearTimeout(temporizador); pesquisar(); campo.focus();
  });
  jQuery('#cadastro-chip-modal').on('shown.bs.modal', function () { document.getElementById('chip').focus(); });
});
</script>
