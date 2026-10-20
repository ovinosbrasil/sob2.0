(function () {
  'use strict';
  function validar(formulario) {
    var sufixo = formulario.getAttribute('data-alertas-financeiro');
    function campo(nome) { return formulario.elements[nome + '_' + sufixo]; }
    var nomes = ['descricao', 'data', 'valor', 'forma'];
    if (campo('categoria') && (sufixo !== 'alterar' || formulario.getAttribute('data-tipo') === '1')) nomes.push('categoria');
    for (var i = 0; i < nomes.length; i++) {
      var entrada = campo(nomes[i]);
      if (!entrada || !entrada.value.trim()) {
        SobAlertas.camposObrigatorios();
        if (entrada) entrada.focus();
        return false;
      }
    }
    var data = campo('data').value;
    var partes = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(data);
    var dia = partes && new Date(Number(partes[3]), Number(partes[2]) - 1, Number(partes[1]));
    if (!partes || dia.getFullYear() !== Number(partes[3]) || dia.getMonth() !== Number(partes[2]) - 1 || dia.getDate() !== Number(partes[1])) {
      SobAlertas.mostrar({tipo: 'warning', mensagem: 'Informe uma data de vencimento válida.'});
      campo('data').focus();
      return false;
    }
    var valor = campo('valor').value.trim();
    if (!/^(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d{1,2})?$/.test(valor) || !Number.isFinite(Number(valor.replace(/,/g, ''))) || Number(valor.replace(/,/g, '')) <= 0) {
      SobAlertas.mostrar({tipo: 'warning', mensagem: 'Informe um valor válido maior que zero.'});
      campo('valor').focus();
      return false;
    }
    var parcelas = campo('parcelas');
    if (parcelas && (!/^(?:[1-9]|10)$/.test(parcelas.value))) {
      SobAlertas.mostrar({tipo: 'warning', mensagem: 'Selecione de 1 a 10 parcelas.'});
      parcelas.focus();
      return false;
    }
    return true;
  }
  document.addEventListener('submit', function (evento) {
    var form = evento.target;
    if (!form.hasAttribute('data-alertas-financeiro')) return;
    if (form.getAttribute('data-enviando') === 'true' || !validar(form)) {
      evento.preventDefault();
      return;
    }
    form.setAttribute('data-enviando', 'true');
    var botao = form.querySelector('[type="submit"]');
    if (botao) { botao.disabled = true; botao.textContent = 'Salvando...'; }
  });
  document.addEventListener('DOMContentLoaded', function () {
    var retorno = JSON.parse(document.getElementById('financeiro-retorno').textContent);
    if (retorno.alerta) SobAlertas.mostrar(retorno.alerta);
    var salvo = retorno.formulario;
    if (!salvo) return;
    function preencher() {
      var form = document.querySelector('form[data-alertas-financeiro="' + salvo.sufixo + '"]');
      if (!form) return;
      Object.keys(salvo.campos).forEach(function (nome) {
        var entrada = form.elements[nome + '_' + salvo.sufixo];
        if (entrada) entrada.value = salvo.campos[nome];
      });
    }
    if (salvo.sufixo === 'alterar') {
      document.addEventListener('financeiro:edicao-carregada', preencher, {once: true});
      alterar(salvo.id);
    } else if (salvo.sufixo === 'debito' || salvo.sufixo === 'faturamento') {
      preencher();
      jQuery('#palco_' + salvo.sufixo).modal('show');
    }
  });
}());
