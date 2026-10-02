(function () {
  'use strict';
  function iniciar(componente) {
    if (componente.dataset.iniciado === '1') return;
    componente.dataset.iniciado = '1';
    var input = componente.querySelector('.sob-busca-animais__input');
    var painel = componente.querySelector('.sob-busca-animais__resultados');
    var campoId = componente.querySelector('[data-busca-compradores-id]');
    var temporizador = null, requisicao = null, indiceAtivo = -1;
    function fechar() { painel.hidden=true; painel.innerHTML=''; input.setAttribute('aria-expanded','false'); indiceAtivo=-1; }
    function selecionar(item) { input.value=item.nome || ''; campoId.value=item.id || ''; fechar(); componente.dispatchEvent(new CustomEvent('buscacompradores:selecionado',{bubbles:true,detail:item})); }
    function opcao(item, indice) {
      var botao=document.createElement('button'); botao.type='button'; botao.className='sob-busca-animais__opcao'; botao.id=painel.id+'-opcao-'+indice; botao.setAttribute('role','option');
      var nome=document.createElement('strong'); nome.textContent=item.nome || ''; botao.appendChild(nome);
      var detalhe=document.createElement('span'); detalhe.className='sob-busca-animais__detalhes'; detalhe.textContent='Cidade: '+(item.cidade || '--')+(item.estado ? ' / '+item.estado : ''); botao.appendChild(detalhe);
      botao.addEventListener('mousedown',function(e){e.preventDefault();selecionar(item);}); return botao;
    }
    function fecharOpcao(){var b=document.createElement('button');b.type='button';b.className='sob-busca-animais__fechar';b.textContent='Fechar Pesquisa';b.addEventListener('mousedown',function(e){e.preventDefault();fechar();});return b;}
    function mensagem(texto){painel.innerHTML='';var a=document.createElement('div');a.className='sob-busca-animais__mensagem';a.textContent=texto; painel.appendChild(a);painel.appendChild(fecharOpcao());painel.hidden=false;input.setAttribute('aria-expanded','true');}
    function renderizar(dados){painel.innerHTML='';indiceAtivo=-1;var itens=Array.isArray(dados.resultados)?dados.resultados:[];if(!itens.length){mensagem('Nenhum comprador encontrado.');return;}itens.forEach(function(item,i){painel.appendChild(opcao(item,i));});painel.appendChild(fecharOpcao());painel.hidden=false;input.setAttribute('aria-expanded','true');}
    function buscar(){var q=input.value.trim();if(!q){fechar();return;}if(requisicao)requisicao.abort();requisicao=new XMLHttpRequest();requisicao.open('GET','vendas/_buscar_compradores.php?q='+encodeURIComponent(q),true);requisicao.onreadystatechange=function(){if(requisicao.readyState!==4)return;if(requisicao.status>=200&&requisicao.status<300){try{renderizar(JSON.parse(requisicao.responseText));}catch(e){mensagem('Não foi possível carregar os resultados.');}}else if(requisicao.status!==0)mensagem('Não foi possível carregar os resultados.');};requisicao.send(null);}
    input.addEventListener('input',function(){campoId.value='';clearTimeout(temporizador);temporizador=setTimeout(buscar,250);});
    input.addEventListener('keydown',function(e){var opcoes=painel.querySelectorAll('.sob-busca-animais__opcao');if(painel.hidden||!opcoes.length){if(e.key==='Escape')fechar();return;}if(e.key==='ArrowDown'){e.preventDefault();indiceAtivo=Math.min(indiceAtivo+1,opcoes.length-1);}else if(e.key==='ArrowUp'){e.preventDefault();indiceAtivo=Math.max(indiceAtivo-1,0);}else if(e.key==='Enter'&&indiceAtivo>=0){e.preventDefault();opcoes[indiceAtivo].dispatchEvent(new MouseEvent('mousedown',{bubbles:true}));return;}else if(e.key==='Escape'){fechar();return;}else return;opcoes.forEach(function(o,i){o.classList.toggle('is-active',i===indiceAtivo);});opcoes[indiceAtivo].scrollIntoView({block:'nearest'});});
    document.addEventListener('mousedown',function(e){if(!componente.contains(e.target))fechar();});
  }
  function iniciarTodos(raiz){(raiz||document).querySelectorAll('[data-busca-compradores]').forEach(iniciar);}
  window.BuscaCompradores={iniciar:iniciar,iniciarTodos:iniciarTodos};
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',function(){iniciarTodos(document);});else iniciarTodos(document);
}());
