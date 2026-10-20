const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const listeners = {};
const avisos = [];
const contexto = {document: {addEventListener: (nome, fn) => { listeners[nome] = fn; }}, SobAlertas: {
  camposObrigatorios: () => avisos.push('obrigatorios'), mostrar: aviso => avisos.push(aviso.mensagem)
}};
vm.runInNewContext(fs.readFileSync('dist/js/alertas-financeiro.js', 'utf8'), contexto);
function enviar(mudancas) {
  const attrs = {'data-alertas-financeiro': 'debito'};
  const elementos = {};
  Object.entries(Object.assign({descricao: 'Internet', data: '20/10/2026', valor: '1,200.50', forma: 'Dinheiro', categoria: 'Outros', parcelas: '1'}, mudancas)).forEach(([nome, valor]) => {
    elementos[nome + '_debito'] = {value: valor, focus() { this.focado = true; }};
  });
  const botao = {disabled: false};
  const form = {elements: elementos, hasAttribute: nome => nome in attrs, getAttribute: nome => attrs[nome], setAttribute: (nome, valor) => { attrs[nome] = valor; }, querySelector: () => botao};
  const evento = {target: form, impedido: false, preventDefault() { this.impedido = true; }};
  listeners.submit(evento);
  return {evento, botao, form};
}
for (const campos of [{descricao: ''}, {data: '31/02/2026'}, {valor: '0.00'}, {valor: 'abc'}, {categoria: ''}, {parcelas: '0'}]) {
  const antes = avisos.length;
  const resultado = enviar(campos);
  assert(resultado.evento.impedido);
  assert.strictEqual(avisos.length, antes + 1);
  assert(!resultado.botao.disabled);
}
const valido = enviar({});
assert(!valido.evento.impedido);
assert(valido.botao.disabled);
const duplicado = {target: valido.form, preventDefault() { this.impedido = true; }};
listeners.submit(duplicado);
assert(duplicado.impedido);
console.log('Financeiro: validação no navegador e bloqueio de envio duplicado OK');
