(function () {
  'use strict';
  var id = 0, geracao = 0;
  var conteudo = document.getElementById('conteudo-modal-reprodutor');
  var indicador = '1';
  var abas = document.querySelectorAll('#abas-modal-reprodutor [data-indicador]');
  function selecionarAba(aba) {
    indicador = aba.getAttribute('data-indicador');
    abas.forEach(function (item) {
      var ativa = item === aba;
      item.parentNode.classList.toggle('active', ativa);
      item.setAttribute('aria-selected', ativa ? 'true' : 'false');
      item.tabIndex = ativa ? 0 : -1;
    });
    conteudo.setAttribute('aria-labelledby', aba.id);
  }
  function carregar() {
    var atual = ++geracao;
    conteudo.textContent = 'Carregando relatório…';
    fetch('relatorios/reprodutor/_detalhes.php?id_animal=' + encodeURIComponent(id) + '&filtro=' + encodeURIComponent(indicador), {credentials:'same-origin'})
      .then(function (resposta) {
        if (!resposta.ok || resposta.redirected) throw new Error('Não foi possível carregar o relatório. Recarregue a página e tente novamente.');
        return resposta.text();
      }).then(function (html) { if (atual === geracao) conteudo.innerHTML = html; })
      .catch(function (erro) { if (atual === geracao) conteudo.textContent = erro.message; });
  }
  document.querySelectorAll('[data-relatorio-reprodutor]').forEach(function (botao) {
    if (botao.tagName !== 'BUTTON') {
      botao.addEventListener('keydown', function (evento) {
        if (evento.key === 'Enter' || evento.key === ' ') { evento.preventDefault(); botao.click(); }
      });
    }
    botao.addEventListener('click', function () {
      id = botao.getAttribute('data-relatorio-reprodutor');
      document.getElementById('titulo-modal-reprodutor').textContent = 'Relatório — ' + botao.getAttribute('data-nome');
      selecionarAba(abas[0]);
      $('#modal-relatorio-reprodutor').modal('show');
      carregar();
    });
  });
  abas.forEach(function (aba, indice) {
    aba.addEventListener('click', function (evento) { evento.preventDefault(); selecionarAba(aba); carregar(); });
    aba.addEventListener('keydown', function (evento) {
      var destino;
      if (evento.key === 'ArrowRight') destino = (indice + 1) % abas.length;
      else if (evento.key === 'ArrowLeft') destino = (indice + abas.length - 1) % abas.length;
      else if (evento.key === 'Home') destino = 0;
      else if (evento.key === 'End') destino = abas.length - 1;
      else return;
      evento.preventDefault(); abas[destino].focus(); selecionarAba(abas[destino]); carregar();
    });
  });
  document.addEventListener('DOMContentLoaded', function () {
    $('#modal-relatorio-reprodutor').on('hidden.bs.modal', function () { geracao++; conteudo.textContent = ''; });
  });
})();
