'use strict';
const fs = require('fs'), vm = require('vm'), assert = require('assert');
function el() {
  const e = {children: [], attrs: {}, checked: false, disabled: false, classList: {toggle() {}},
    appendChild(c) { c.parent = this; this.children.push(c); }, setAttribute(k,v) { this.attrs[k]=v; },
    addEventListener(k,v) { this[k]=v; }, focus() {}, remove() { this.parent.children=this.parent.children.filter(c=>c!==this); },
    querySelectorAll(selector) {
      const all = this.children.flatMap(c=>[c].concat(c.querySelectorAll('*')));
      if (selector==='input:checked') return all.filter(c=>c.type==='checkbox' && c.checked);
      if (selector==='[data-campo-selecionado]') return all.filter(c=>Object.hasOwn(c.attrs,'data-campo-selecionado'));
      return all;
    }};
  Object.defineProperty(e,'textContent',{get(){return this.text||'';},set(v){this.text=v;this.children=[];}});
  return e;
}
const ids = {}, get = id=>ids[id] || (ids[id]=el());
const events={};const modal={modal(){},on(k,v){events[k]=v;return this;}};
const context={document:{getElementById:get,createElement:el,querySelectorAll(){return get('mudancas-atualizacao-rebanho').querySelectorAll('input:checked');},addEventListener(k,f){f();}},window:{},$(){return modal;}};
const source=fs.readFileSync('animal/importacao/tabela_possiveis_atualizacoes.php','utf8').split('<script>')[1].split('</script>')[0];
vm.runInNewContext(source,context);
const form=el(), button=el();let envios=0;
form.querySelector=()=>button;form.submit=()=>envios++;
form.getAttribute=()=>JSON.stringify([{chave:'nome',campo:'Nome',atual:'ANTIGO',novo:'NOVO'},{chave:'fbb',campo:'FBB',atual:'1',novo:'2'},{chave:'tatuagem',campo:'Tatuagem',atual:'T1',novo:'T1'}]);
context.window.confirmarAtualizacaoRebanho(form);
const corpo=get('mudancas-atualizacao-rebanho'), confirmar=get('salvar-atualizacao-rebanho');
assert.equal(corpo.querySelectorAll('input:checked').length,2);
assert.equal(corpo.children.length,3);
assert.equal(corpo.children[2].children[0].textContent,'—');
corpo.querySelectorAll('input:checked').forEach(c=>{c.checked=false;c.change();});
assert.equal(confirmar.disabled,true);confirmar.click();assert.equal(envios,0);
events['hidden.bs.modal']();context.window.confirmarAtualizacaoRebanho(form);
assert.equal(corpo.querySelectorAll('input:checked').length,2);
assert.equal(corpo.children.length,3);
assert.equal(corpo.children[2].children[0].textContent,'—');
const status=corpo.querySelectorAll('input:checked').find(c=>c.value==='fbb');status.checked=false;status.change();
confirmar.click();confirmar.click();assert.equal(envios,1);
assert.deepStrictEqual(form.children.filter(c=>c.name==='campos[]').map(c=>c.value),['nome']);
console.log('Modal: todos selecionados ao abrir, reset ao reabrir, seleção vazia bloqueada e envio somente do campo marcado: OK');
