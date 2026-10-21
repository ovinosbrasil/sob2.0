'use strict';
const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const envios = [];
const token = {name: '_csrf_sob', value: 'token-da-sessao', cloneNode() {return {...this};}};
const elementos = {};
const document = {
  body: {appendChild() {}}, activeElement: null,
  documentElement: {contains() {return false;}},
  getElementById(id) {
    if (id === 'sob-post-csrf') return {elements: {_csrf_sob: token}};
    return elementos[id] || (elementos[id] = {style: {}});
  },
  createElement(tipo) {
    if (tipo === 'form') return {
      campos: [], appendChild(campo) {this.campos.push(campo);},
      submit() {envios.push(this);}
    };
    return {};
  }
};
const window = {};
const $ = () => ({modal() {}, on() {return this;}});
const contexto = vm.createContext({window, document, jQuery: $});
vm.runInContext(fs.readFileSync('dist/js/csrf.js', 'utf8'), contexto);
for (const destino of ['animal/_excluir_animal.php','animal/saida/_desfazer_saida.php','animal/vender/_desfazer_venda.php']) {
  window.enviarPostCsrfSob(destino, {id_animal: 7, _csrf_sob: 'nao-substituir'});
  const form = envios[envios.length - 1];
  assert.strictEqual(form.method, 'post');
  assert.strictEqual(form.action, destino);
  assert.strictEqual(form.campos.length, 2);
  assert.strictEqual(form.campos[0].value, 'token-da-sessao');
  assert.strictEqual(form.campos[1].value, 7);
}
vm.runInContext(fs.readFileSync('dist/js/confirmacao-exclusao.js', 'utf8'), contexto);
window.confirmarExclusaoRebanho({getAttribute(nome) {return {'data-id': '8','data-nome': 'Animal','data-origem': 'Rebanho'}[nome];}});
assert.strictEqual(envios.length, 3, 'Excluir não deve enviar antes da confirmação');
elementos['confirmacao-exclusao-prosseguir'].onclick();
assert.strictEqual(envios.length, 4);
assert.strictEqual(envios[3].action, 'animal/_excluir_animal.php');
assert.strictEqual(envios[3].campos[1].value, '8');
elementos['confirmacao-exclusao-prosseguir'].onclick();
assert.strictEqual(envios.length, 4, 'Confirmação deve impedir duplo envio');
console.log('Animais: POST com token, ID, confirmação e prevenção de duplo envio: OK');
