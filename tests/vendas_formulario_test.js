const fs=require('node:fs'), vm=require('node:vm'), assert=require('node:assert/strict');
const source=fs.readFileSync('vendas/vender.php','utf8').match(/<script type="text\/javascript">([\s\S]*?)<\/script>/)[1].replace(/<\?=json_encode\([\s\S]*?\)\?>/g,'{}');
const alertas=[];
function campo(name,value){return {name,value,style:{},labels:[{textContent:name+'*'}],classList:{contains(c){return c==='campo-valor-venda'&&name==='valor';}},focus(){this.focado=true;}};}
const fields={comprador:campo('Comprador','Cliente'),data:campo('Data','15/10/2026'),tipo_venda:campo('Tipo','Fazenda'),valor_total:campo('Total','')};
const animal=campo('Animal','Teste'), valor=campo('valor','100.00');
const id={value:'1'},origem={value:'rebanho'};
const linha={querySelector(selector){if(selector==='[data-busca-animais-id]')return id;if(selector==='[data-busca-animais-origem]')return origem;return selector.includes('animal_')||selector.includes('sob-busca-animais__input')?animal:valor;}};
const document={addEventListener(){},getElementById(id){return fields[id];},querySelectorAll(selector){return selector.includes('linha-animal')?[linha]:[valor];}};
const context={document,Date,SobAlertas:{camposObrigatorios(campos){alertas.push({campos});},mostrar(alerta){alertas.push(alerta);}}};context.window=context;
vm.runInNewContext(source,context);
assert.equal(context.ativar_venda(),true); assert.equal(fields.valor_total.value,'100.00');
animal.value='';assert.equal(context.ativar_venda(),false);assert.equal(alertas.at(-1).campos[0],'Animal');assert.equal(animal.focado,true);
animal.value='Teste';valor.value='0';assert.equal(context.ativar_venda(),false);
valor.value='100';fields.data.value='31/02/2026';assert.equal(context.ativar_venda(),false);assert.equal(alertas.at(-1).tipo,'warning');
fields.data.value='29/02/2024';assert.equal(context.ativar_venda(),true);
id.value='';assert.equal(context.ativar_venda(),false);assert.match(alertas.at(-1).mensagem,/lista de pesquisa/);
console.log('Vender: campos obrigatórios, valor positivo, total, foco e datas: OK');
