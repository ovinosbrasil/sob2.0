'use strict';
const assert = require('assert');
const fs = require('fs');
const vm = require('vm');
function element() {
  return {children: [], value: '', textContent: '', disabled: false, hidden: false,
    appendChild(child) { this.children.push(child); },
    addEventListener(event, handler) { this[event] = handler; }, focus() {}};
}
async function testar() {
  const ids = {};
  const get = id => ids[id] || (ids[id] = element());
  const form = get('form-cadastro-previa');
  form.elements = {linha: element(), confirmacao: element()};
  form.action = 'animal/_cadastrar_importacao.php';
  const button = element(); button.getAttribute = () => '2';
  const events = {};
  const modal = {modal() {}, on(event, handler) { events[event] = handler; return this; }};
  const requests = [];
  let semValidos = false;
  const context = {document: {
    getElementById: get, createElement: element,
    querySelectorAll() { return [button]; },
    addEventListener(event, handler) { handler(); }
  }, $() { return modal; }, window: {location: {}},
  FormData: class {append(key, value) { this[key] = value; }},
  fetch(url, options) {
    requests.push(options.body.acao);
    const dados = options.body.acao === 'preparar'
      ? {confirmacao: 'token-confirmado', plano: [
        {origem: 'terceiros', dados: {nome: 'PAI P001', sexo: 'Macho'}},
        {origem: 'animais', dados: {nome: '<script> FILHO 001', sexo: 'Macho', tatuagem: '001'}, pai: {nome: 'PAI P001', origem: 'terceiros', chave: 'pai'}, mae: {nome: 'MÃE M001', origem: 'animais', id: 15}}
      ]} : {sucesso: true};
    if (options.body.acao === 'preparar') {
      dados.ignorados = [{linha: 22, nome: '<b>INVALIDO</b>', motivo: 'Pai e mãe iguais'}];
      if (semValidos) { dados.plano = []; dados.confirmacao = ''; }
    }
    return Promise.resolve({ok: true, json: () => Promise.resolve(dados)});
  }};
  vm.runInNewContext(fs.readFileSync('animal/importacao/cadastro_previa.js', 'utf8'), context);
  const esperar = () => new Promise(resolve => setImmediate(resolve));
  events['shown.bs.tab']({target: {getAttribute: () => '#aba-previa-nao_cadastrados'}});
  assert.strictEqual(get('cadastrar-todos-previa').hidden, false);
  assert.strictEqual(get('atualizar-todos-previa').hidden, true);
  events['shown.bs.tab']({target: {getAttribute: () => '#aba-previa-possiveis_atualizacoes'}});
  assert.strictEqual(get('cadastrar-todos-previa').hidden, true);
  assert.strictEqual(get('atualizar-todos-previa').hidden, false);
  events['shown.bs.tab']({target: {getAttribute: () => '#aba-previa-cadastrados'}});
  assert.strictEqual(get('cadastrar-todos-previa').hidden, true);
  assert.strictEqual(get('atualizar-todos-previa').hidden, true);
  button.click(); await esperar();
  assert.deepStrictEqual(requests, ['preparar']);
  assert.strictEqual(form.elements.confirmacao.value, 'token-confirmado');
  assert.strictEqual(get('confirmar-cadastro-previa').disabled, false);
  const textos = function (node) { return [node.textContent].concat(...node.children.map(textos)); };
  const exibidos = textos(get('dados-cadastro-previa')).join(' ');
  assert(exibidos.includes('<script> FILHO 001')); // Inserido como texto, nunca HTML.
  assert(exibidos.includes('ID 15'));
  assert(exibidos.includes('Linha 22 — <b>INVALIDO</b>: Pai e mãe iguais'));
  assert(exibidos.includes('será cadastrado antes'));
  events['hidden.bs.modal']();
  assert.deepStrictEqual(requests, ['preparar']);
  button.click(); await esperar();
  button.getAttribute = () => 'todos';
  // Reabrir em modo coletivo não grava até a confirmação.
  button.click(); await esperar();
  assert.strictEqual(form.elements.linha.value, 'todos');
  assert.strictEqual(get('aviso-cadastro-todos').hidden, false);
  semValidos = true;
  button.click(); await esperar();
  assert.strictEqual(get('confirmar-cadastro-previa').disabled, true);
  form.submit({preventDefault() {}});
  semValidos = false;
  button.click(); await esperar();
  form.submit({preventDefault() {}});
  form.submit({preventDefault() {}});
  await esperar();
  assert.deepStrictEqual(requests, ['preparar', 'preparar', 'preparar', 'preparar', 'preparar', 'confirmar']);
  assert.strictEqual(context.window.location.href, 'geral.php?pg=atualizar_rebanho');
  console.log('Modal: plano completo, IDs, conteúdo seguro, cancelamento sem escrita e confirmação única: OK');
}
testar().catch(erro => { console.error(erro); process.exitCode = 1; });
