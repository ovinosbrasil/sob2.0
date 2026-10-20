(function (window, document) {
  'use strict';
  var opcoes = {
    confirmar: {titulo: 'Confirmar lançamento?', mensagem: 'Deseja marcar este lançamento como confirmado?', botao: 'Confirmar', classe: 'btn-success', icone: 'check', tom: 'confirmar'},
    cancelar: {titulo: 'Voltar lançamento para pendente?', mensagem: 'Deseja desfazer a confirmação e voltar este lançamento para pendente?', botao: 'Voltar para pendente', classe: 'btn-warning', icone: 'undo', tom: 'voltar'},
    excluir: {titulo: 'Excluir lançamento?', mensagem: 'Deseja excluir este lançamento? Esta ação não pode ser desfeita.', botao: 'Excluir', classe: 'btn-danger', icone: 'trash-o', tom: 'excluir'}
  };
  var origem = null;
  window.pedirConfirmacaoFinanceiro = function (acao, id) {
    if (!Object.prototype.hasOwnProperty.call(opcoes, acao) || !/^[1-9]\d*$/.test(String(id))) return;
    var opcao = opcoes[acao];
    var botao = document.getElementById('financeiro-confirmacao-prosseguir');
    origem = document.activeElement;
    document.getElementById('financeiro-confirmacao-titulo').textContent = opcao.titulo;
    document.getElementById('financeiro-confirmacao-descricao').textContent = opcao.mensagem;
    var linha = document.querySelector('[data-financeiro-id="' + id + '"]');
    document.getElementById('financeiro-confirmacao-nome').textContent = linha ? linha.getAttribute('data-financeiro-nome') : '';
    document.getElementById('financeiro-confirmacao-icone').className = 'icone-confirmacao financeiro-icone-' + opcao.tom;
    document.getElementById('financeiro-confirmacao-simbolo').className = 'fa fa-' + opcao.icone;
    botao.textContent = opcao.botao;
    botao.className = 'btn ' + opcao.classe;
    botao.disabled = false;
    botao.onclick = function () {
      if (botao.disabled) return;
      botao.disabled = true;
      var parametros = new URLSearchParams({
        id_financeiro: id,
        data_inicial: document.getElementById('data_inicial').value,
        data_final: document.getElementById('data_final').value
      });
      window.location.href = 'financeiro/_' + acao + '.php?' + parametros.toString();
    };
    jQuery('#financeiro-confirmacao-modal').modal('show');
  };
  document.addEventListener('DOMContentLoaded', function () {
    jQuery('#financeiro-confirmacao-modal').on('shown.bs.modal', function () {
      document.getElementById('financeiro-confirmacao-cancelar').focus();
    }).on('hidden.bs.modal', function () {
      document.getElementById('financeiro-confirmacao-prosseguir').onclick = null;
      if (origem && document.documentElement.contains(origem)) origem.focus();
      origem = null;
    });
  });
}(window, document));
