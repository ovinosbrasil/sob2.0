const assert = require('assert');
const fs = require('fs');
const vm = require('vm');
const elementos = {};
const eventos = {};
const origem = {focus() { this.focado = true; }};
const document = {
  activeElement: origem,
  getElementById(id) { return elementos[id] || (elementos[id] = {value: '20/10/2026', focus() { this.focado = true; }}); },
  querySelector() { return {getAttribute() { return '<Conta de luz>'; }}; },
  addEventListener(nome, fn) { eventos[nome] = fn; },
  documentElement: {contains() { return true; }}
};
const modal = {modal(acao) { this.aberto = acao === 'show'; }, on(nome, fn) { eventos[nome] = fn; return this; }};
const window = {location: {href: ''}};
vm.runInNewContext(fs.readFileSync('dist/js/confirmacao-financeiro.js', 'utf8'), {window, document, jQuery: () => modal, URLSearchParams});
eventos.DOMContentLoaded();
for (const acao of ['confirmar', 'cancelar', 'excluir']) {
  window.location.href = '';
  window.pedirConfirmacaoFinanceiro(acao, 7);
  assert(modal.aberto);
  assert.strictEqual(window.location.href, '');
  assert.strictEqual(elementos['financeiro-confirmacao-nome'].textContent, '<Conta de luz>');
  eventos['shown.bs.modal']();
  assert(elementos['financeiro-confirmacao-cancelar'].focado);
  eventos['hidden.bs.modal']();
  assert.strictEqual(elementos['financeiro-confirmacao-prosseguir'].onclick, null);
  assert.strictEqual(window.location.href, '');
  window.pedirConfirmacaoFinanceiro(acao, 7);
  elementos['financeiro-confirmacao-prosseguir'].onclick();
  assert(window.location.href.startsWith('financeiro/_' + acao + '.php?'));
  assert(window.location.href.includes('id_financeiro=7'));
  assert(elementos['financeiro-confirmacao-prosseguir'].disabled);
}
console.log('Financeiro: três confirmações, cancelamento e execução somente após aceite OK');
