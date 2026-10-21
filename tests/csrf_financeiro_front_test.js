'use strict';
const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
let envios = 0;
const elementos = {};
const formulario = {
  elements: {
    id_financeiro: {value: ''}, data_inicial: {value: ''}, data_final: {value: ''},
    _csrf_sob: {value: 'token-da-sessao'}
  },
  submit() { envios++; }
};
const document = {
  activeElement: null,
  getElementById(id) {
    if (id === 'financeiro-acao-form') return formulario;
    if (!elementos[id]) elementos[id] = {value: id === 'data_inicial' ? '01/01/2026' : '31/01/2026'};
    return elementos[id];
  },
  querySelector() { return {getAttribute() { return 'Lançamento'; }}; },
  addEventListener() {}
};
const window = {};
vm.runInNewContext(fs.readFileSync('dist/js/confirmacao-financeiro.js', 'utf8'), {
  window, document, jQuery() { return {modal() {}}; }
});
for (const acao of ['confirmar', 'cancelar', 'excluir']) {
  window.pedirConfirmacaoFinanceiro(acao, 7);
  document.getElementById('financeiro-confirmacao-prosseguir').onclick();
  assert.strictEqual(formulario.action, 'financeiro/_' + acao + '.php');
  assert.strictEqual(formulario.elements.id_financeiro.value, 7);
  assert.strictEqual(formulario.elements.data_inicial.value, '01/01/2026');
  assert.strictEqual(formulario.elements.data_final.value, '31/01/2026');
  assert.strictEqual(formulario.elements._csrf_sob.value, 'token-da-sessao');
  document.getElementById('financeiro-confirmacao-prosseguir').onclick();
}
assert.strictEqual(envios, 3, 'Deve impedir duplo envio pela confirmação');
console.log('Financeiro: confirmação envia formulário com token, ID e filtros: OK');
