const fs=require('node:fs'),vm=require('node:vm'),assert=require('node:assert/strict');
const fonte=fs.readFileSync('vendas/compradores.php','utf8').match(/<script>([\s\S]*?)<\/script>/)[1];
const alertas=[];let recargas=0,fechamentos=0,resposta={sucesso:true},falhar=false;
const contexto={document:{addEventListener(){}},confirmarExclusao(opcoes){opcoes.aoConfirmar();},FormData:class{append(){}},fetch(){return falhar?Promise.reject(new Error('Rede')):Promise.resolve({json(){return Promise.resolve(resposta);}});},window:{location:{reload(){recargas++;}},alert(){throw new Error('Alerta nativo indevido');}},jQuery(){return {modal(){fechamentos++;}};},SobAlertas:{mostrar(alerta){alertas.push(alerta);},erro(alerta){alertas.push({...alerta,tipo:'danger'});}}};
vm.runInNewContext(fonte,contexto);
(async()=>{
 contexto.excluirComprador(7,'Cliente');await new Promise(setImmediate);assert.equal(recargas,1);
 resposta={sucesso:false,tipo:'warning',mensagem:'Possui venda relacionada'};
 contexto.excluirComprador(7,'Cliente');await new Promise(setImmediate);
 assert.equal(recargas,1);assert.equal(alertas.at(-1).tipo,'warning');assert.equal(fechamentos,1);
 falhar=true;contexto.excluirComprador(7,'Cliente');await new Promise(setImmediate);
 assert.equal(alertas.at(-1).tipo,'danger');assert.equal(recargas,1);
 console.log('Compradores: sucesso, bloqueio e falha de exclusão usam os alertas padrão: OK');
})().catch(e=>{console.error(e);process.exitCode=1;});
