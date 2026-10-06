const fs = require('node:fs'), vm = require('node:vm'), assert = require('node:assert/strict');
class Elemento {
 constructor(tag) {this.tag=tag;this.children=[];this.style={};this.attrs={};this.events={};}
 appendChild(el) {this.children.push(el);el.parent=this;}
 setAttribute(k,v) {this.attrs[k]=v;}
 addEventListener(k,v) {this.events[k]=v;}
 remove() {this.parent.children=this.parent.children.filter(el=>el!==this);}
 contains(el) {return this===el || this.children.some(c=>c.contains(el));}
}
let agora=0,seq=0;const timers=new Map(),body=new Elemento('body'),events={};
const document={body,hidden:false,getElementById(id){return body.children.find(e=>e.id===id);},createElement(tag){return new Elemento(tag);},addEventListener(k,f){(events[k]??=new Set()).add(f);},removeEventListener(k,f){events[k].delete(f);}};
const window={};
vm.runInNewContext(fs.readFileSync('dist/js/alertas.js','utf8'),{window,document,performance:{now:()=>agora},setTimeout(f,ms){timers.set(++seq,{f,ms});return seq;},clearTimeout(id){timers.delete(id);},Set,Number,TypeError});
const api=window.SobAlertas;
const ref=api.mostrar({tipo:'success',mensagem:'<script>alert(1)</script>'});
const container=body.children[0],alerta=container.children[0];
assert.equal(alerta.attrs.role,'status');
assert.equal(alerta.children[1].children[1].textContent,'<script>alert(1)</script>');
assert.equal(timers.values().next().value.ms,6000);
agora=1000;alerta.events.mouseenter();assert.equal(timers.size,0);
alerta.events.focusin();alerta.events.mouseleave();assert.equal(timers.size,0);
alerta.events.focusout({relatedTarget:null});assert.equal(timers.values().next().value.ms,5000);
ref.fechar();ref.fechar();assert.equal(container.children.length,0);assert.equal(timers.size,0);
for (const tipo of ['warning','danger']) {
 api.mostrar({tipo,mensagem:'Mensagem',duracao:0});
 const el=container.children.at(-1);assert.equal(el.attrs.role,'alert');assert.equal(el.children[1].children.length,2);
 el.children[2].events.click();
}
api.mostrar({tipo:'success'});const auto=container.children[0];
agora=2000;document.hidden=true;for(const f of events.visibilitychange) f();assert.equal(timers.size,0);
document.hidden=false;for(const f of events.visibilitychange) f();assert.equal(timers.values().next().value.ms,5000);
timers.values().next().value.f();assert.equal(container.children.length,0);assert.equal(events.visibilitychange.size,0);
assert.throws(()=>api.mostrar({tipo:'invalido'}));assert.throws(()=>api.mostrar({duracao:-1}));
for (const [metodo,tipo,titulo,mensagem] of [
 ['camposObrigatorios','warning','Atenção!','Preencha os campos obrigatórios.'],
 ['cadastroRealizado','success','Sucesso!','Cadastro realizado com sucesso.'],
 ['erro','danger','Erro!','Não foi possível concluir a operação. Tente novamente.']
]) {
 const refPadrao=api[metodo]();const el=container.children.at(-1);
 assert.equal(el.className,'sob-alerta sob-alerta--'+tipo);
 assert.equal(el.children[1].children[0].textContent,titulo);
 assert.equal(el.children[1].children[1].textContent,mensagem);
 refPadrao.fechar();
}
let padrao=api.camposObrigatorios(['Título','Data'],{duracao:0});
assert.equal(container.children.at(-1).children[1].children[1].textContent,'Preencha os campos obrigatórios: Título, Data.');
assert.equal(timers.size,0);padrao.fechar();
padrao=api.erro({tipo:'success',titulo:'Outro',mensagem:'Mensagem personalizada',duracao:0});
assert.equal(container.children.at(-1).className,'sob-alerta sob-alerta--danger');
assert.equal(container.children.at(-1).children[1].children[0].textContent,'Erro!');
assert.equal(container.children.at(-1).children[1].children[1].textContent,'Mensagem personalizada');
padrao.fechar();
console.log('Tipos, texto seguro, duração, pausas combinadas, aba oculta e fechamento: OK');
console.log('Mensagens padrão, campos opcionais e personalização: OK');
