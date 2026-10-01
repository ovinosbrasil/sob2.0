'use strict';
const fs = require('fs'), vm = require('vm'), assert = require('assert');
function campo() { return {files: [], erro: '', setCustomValidity(v) { this.erro = v; }, addEventListener(e, f) { this[e] = f; }}; }
const vivos = campo(), mortos = campo(), verificar = {};
const elementos = {'planilha-vivos': vivos, 'planilha-mortos': mortos, 'verificar-planilhas': verificar};
vm.runInNewContext(fs.readFileSync('animal/importacao/upload_planilhas.js', 'utf8'), {document: {getElementById: id => elementos[id]}, window: {addEventListener() {}}});
assert.strictEqual(verificar.disabled, true);
vivos.files = [{name: 'vivos.xls', size: 5 * 1024 * 1024}]; vivos.change();
assert.strictEqual(verificar.disabled, true);
mortos.files = [{name: 'mortos.xls', size: 5 * 1024 * 1024}]; mortos.change();
assert.strictEqual(verificar.disabled, false);
mortos.files[0].size++; mortos.change(); assert.strictEqual(verificar.disabled, true);
mortos.files[0] = {name: 'mortos.xlsx', size: 100}; mortos.change(); assert.strictEqual(verificar.disabled, true);
mortos.files[0].name = 'mortos.xls'; mortos.change(); assert.strictEqual(verificar.disabled, false);
vivos.files = []; vivos.change(); assert.strictEqual(verificar.disabled, true);
console.log('Verificação liberada somente com os dois arquivos válidos, até 5 MB cada: OK');
