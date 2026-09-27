(function () {
  'use strict';

  var endpoint = 'animal/_buscar_animais.php';

  function textoSeguro(valor) {
    return valor == null ? '' : String(valor);
  }

  function classeSituacao(situacao) {
    var valor = textoSeguro(situacao).toLowerCase();
    if (valor === 'rebanho' || valor === 'vendido' || valor === 'ativa') return 'is-success';
    if (valor === 'terceiros') return 'is-muted';
    return 'is-danger';
  }

  function iniciar(componente) {
    if (componente.dataset.iniciado === '1') return;
    componente.dataset.iniciado = '1';

    var input = componente.querySelector('.sob-busca-animais__input');
    var painel = componente.querySelector('.sob-busca-animais__resultados');
    var campoId = componente.querySelector('[data-busca-animais-id]');
    var campoOrigem = componente.querySelector('[data-busca-animais-origem]');
    var temporizador = null;
    var requisicao = null;
    var indiceAtivo = -1;

    function fechar() {
      painel.hidden = true;
      painel.innerHTML = '';
      input.setAttribute('aria-expanded', 'false');
      indiceAtivo = -1;
    }

    function selecionar(item) {
      input.value = textoSeguro(item.nome);
      campoId.value = textoSeguro(item.id);
      campoOrigem.value = textoSeguro(item.origem);
      fechar();
      componente.dispatchEvent(new CustomEvent('buscaanimais:selecionado', {
        bubbles: true,
        detail: item
      }));
    }

    function criarOpcao(item, indice) {
      var botao = document.createElement('button');
      botao.type = 'button';
      botao.className = 'sob-busca-animais__opcao';
      botao.setAttribute('role', 'option');
      botao.id = painel.id + '-opcao-' + indice;

      var nome = document.createElement('strong');
      nome.textContent = textoSeguro(item.nome);
      var detalhes = document.createElement('span');
      detalhes.className = 'sob-busca-animais__detalhes';
      if (item.origem === 'receptora') {
        detalhes.textContent = 'Receptora ';
      } else {
        detalhes.textContent = 'Nascimento: ' + textoSeguro(item.nascimento);
        if (item.origem === 'terceiros') {
          detalhes.textContent += ' · Sexo: ' + textoSeguro(item.sexo);
        }
        detalhes.textContent += ' ';
      }
      var situacao = document.createElement('span');
      situacao.className = 'sob-busca-animais__situacao ' + classeSituacao(item.situacao);
      situacao.textContent = '(' + textoSeguro(item.situacao) + ')';
      detalhes.appendChild(situacao);
      botao.appendChild(nome);
      botao.appendChild(detalhes);
      botao.addEventListener('mousedown', function (evento) {
        evento.preventDefault();
        selecionar(item);
      });
      return botao;
    }

    function criarFecharPesquisa() {
      var fecharPesquisa = document.createElement('button');
      fecharPesquisa.type = 'button';
      fecharPesquisa.className = 'sob-busca-animais__fechar';
      fecharPesquisa.textContent = 'Fechar Pesquisa';
      fecharPesquisa.addEventListener('mousedown', function (evento) {
        evento.preventDefault();
        fechar();
      });
      return fecharPesquisa;
    }

    function mostrarMensagem(mensagem) {
      painel.innerHTML = '';
      var aviso = document.createElement('div');
      aviso.className = 'sob-busca-animais__mensagem';
      aviso.textContent = mensagem;
      painel.appendChild(aviso);
      painel.appendChild(criarFecharPesquisa());
      painel.hidden = false;
      input.setAttribute('aria-expanded', 'true');
    }

    function renderizar(dados) {
      painel.innerHTML = '';
      indiceAtivo = -1;
      var resultados = Array.isArray(dados.resultados) ? dados.resultados : [];
      if (!resultados.length) {
        mostrarMensagem('Nenhum resultado encontrado.');
        return;
      }
      resultados.forEach(function (item, indice) {
        painel.appendChild(criarOpcao(item, indice));
      });
      painel.appendChild(criarFecharPesquisa());
      painel.hidden = false;
      input.setAttribute('aria-expanded', 'true');
    }

    function buscar() {
      var termo = input.value.trim();
      if (!termo) {
        fechar();
        return;
      }
      if (requisicao) requisicao.abort();
      requisicao = new XMLHttpRequest();
      requisicao.open('GET', endpoint + '?tipo=' + encodeURIComponent(componente.dataset.tipo) + '&q=' + encodeURIComponent(termo) + '&limite_origem=' + encodeURIComponent(componente.dataset.limiteOrigem || '0'), true);
      requisicao.onreadystatechange = function () {
        if (requisicao.readyState !== 4) return;
        if (requisicao.status >= 200 && requisicao.status < 300) {
          try { renderizar(JSON.parse(requisicao.responseText)); }
          catch (erro) { mostrarMensagem('Não foi possível carregar os resultados.'); }
        } else if (requisicao.status !== 0) {
          mostrarMensagem('Não foi possível carregar os resultados.');
        }
      };
      requisicao.send(null);
    }

    input.addEventListener('input', function () {
      campoId.value = '';
      campoOrigem.value = '';
      window.clearTimeout(temporizador);
      temporizador = window.setTimeout(buscar, 250);
    });

    input.addEventListener('keydown', function (evento) {
      var opcoes = painel.querySelectorAll('.sob-busca-animais__opcao');
      if (painel.hidden || !opcoes.length) {
        if (evento.key === 'Escape') fechar();
        return;
      }
      if (evento.key === 'ArrowDown') {
        evento.preventDefault();
        indiceAtivo = Math.min(indiceAtivo + 1, opcoes.length - 1);
      } else if (evento.key === 'ArrowUp') {
        evento.preventDefault();
        indiceAtivo = Math.max(indiceAtivo - 1, 0);
      } else if (evento.key === 'Enter' && indiceAtivo >= 0) {
        evento.preventDefault();
        opcoes[indiceAtivo].dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
        return;
      } else if (evento.key === 'Escape') {
        fechar();
        return;
      } else {
        return;
      }
      opcoes.forEach(function (opcao, indice) {
        opcao.classList.toggle('is-active', indice === indiceAtivo);
      });
      input.setAttribute('aria-activedescendant', opcoes[indiceAtivo].id);
      opcoes[indiceAtivo].scrollIntoView({ block: 'nearest' });
    });

    input.addEventListener('focus', function () {
      if (input.value.trim() && painel.childNodes.length) {
        painel.hidden = false;
        input.setAttribute('aria-expanded', 'true');
      }
    });

    document.addEventListener('mousedown', function (evento) {
      if (!componente.contains(evento.target)) fechar();
    });
  }

  function iniciarTodos(raiz) {
    (raiz || document).querySelectorAll('[data-busca-animais]').forEach(iniciar);
  }

  window.BuscaAnimais = { iniciar: iniciar, iniciarTodos: iniciarTodos };
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { iniciarTodos(document); });
  } else {
    iniciarTodos(document);
  }
}());
