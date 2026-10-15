const fs=require('node:fs'),vm=require('node:vm'),assert=require('node:assert/strict');
const fonte=fs.readFileSync('relatorios/selecao_femeas.php','utf8').match(/<script>([\s\S]*?)<\/script>/)[1];
let pronto,envios=0,sequencia=0;const timers=new Map();
function campo(value){return {value,eventos:{},addEventListener(nome,callback){this.eventos[nome]=callback;}};}
const tipo=campo('sem_cria'),inicio=campo(''),fim=campo('');
const formulario={elements:{idade_inicio:inicio,idade_fim:fim},submit(){envios++;},querySelectorAll(){return [tipo,inicio,fim];}};
vm.runInNewContext(fonte,{document:{addEventListener(nome,fn){pronto=fn;},getElementById(){return formulario;}},window:{clearTimeout(id){timers.delete(id);},setTimeout(fn){timers.set(++sequencia,fn);return sequencia;}}});
pronto();fim.value='24';fim.eventos.input();for(const fn of [...timers.values()])fn();assert.equal(envios,0);
fim.value='';inicio.value='12';inicio.eventos.input();inicio.eventos.change();assert.equal(timers.size,1);for(const fn of [...timers.values()])fn();assert.equal(envios,1);
for(const fn of [...timers.values()])fn();assert.equal(envios,1);
console.log('Seleção de fêmeas: consulta automática com idade inicial e máximo vazio, sem envio duplicado: OK');
