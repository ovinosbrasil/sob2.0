(function () {
  'use strict';
  var pesquisa = document.getElementById('pesquisa-previa-rebanho');
  if (!pesquisa) return;
  function normalizar(texto) {
    return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  }
  var tabelas = [];
  document.querySelectorAll('[data-tabela-previa]').forEach(function (tabela) {
    var grupo = tabela.id.replace('tabela-previa-', '');
    var controles = document.querySelector('[data-paginacao-previa="' + grupo + '"]');
    var estado = {
      tabela: tabela, grupo: grupo, controles: controles, pagina: 1,
      limite: controles.querySelector('[data-limite-previa]'),
      registros: Array.from(tabela.querySelectorAll('[data-registro-previa]')).map(function (linha) {
        return {linha: linha, pesquisa: normalizar(linha.getAttribute('data-pesquisa'))};
      }),
      filtrados: []
    };
    estado.limite.addEventListener('change', function () {
      estado.pagina = 1;
      renderizar(estado);
    });
    tabelas.push(estado);
  });
  function renderizar(estado) {
    var total = estado.filtrados.length;
    var limite = Number(estado.limite.value);
    var paginas = Math.max(1, Math.ceil(total / limite));
    estado.pagina = Math.max(1, Math.min(estado.pagina, paginas));
    var inicio = (estado.pagina - 1) * limite;
    estado.registros.forEach(function (registro) { registro.linha.hidden = true; });
    estado.filtrados.slice(inicio, inicio + limite).forEach(function (registro, indice) {
      registro.linha.hidden = false;
      registro.linha.classList.toggle('previa-faixa-escura', indice % 2 === 1);
    });
    estado.tabela.querySelector('[data-sem-resultados]').hidden = total !== 0;
    estado.controles.querySelector('[data-resumo-previa]').textContent =
      'Exibindo ' + (total ? inicio + 1 : 0) + ' a ' + Math.min(inicio + limite, total) + ' de ' + total + ' animais';
    var lista = estado.controles.querySelector('[data-paginas-previa]');
    lista.textContent = '';
    lista.parentNode.hidden = paginas <= 1;
    function adicionar(texto, pagina, rotulo, desabilitado, ativo) {
      var item = document.createElement('li');
      var link = document.createElement(desabilitado || ativo ? 'span' : 'a');
      link.textContent = texto;
      link.setAttribute('aria-label', rotulo);
      if (desabilitado) {
        item.className = 'disabled';
        link.setAttribute('aria-disabled', 'true');
      } else if (ativo) {
        item.className = 'active';
        link.setAttribute('aria-current', 'page');
      } else {
        link.href = '#' + estado.tabela.id;
        link.addEventListener('click', function (evento) {
          evento.preventDefault();
          estado.pagina = pagina;
          renderizar(estado);
          var atual = lista.querySelector('[aria-current="page"]');
          if (atual) { atual.tabIndex = -1; atual.focus(); }
        });
      }
      item.appendChild(link);
      lista.appendChild(item);
    }
    adicionar('«', estado.pagina - 1, 'Página anterior', estado.pagina === 1, false);
    var visiveis = [1, paginas];
    var primeiro = Math.max(1, estado.pagina - 1);
    var ultimo = Math.min(paginas, estado.pagina + 1);
    for (var numero = primeiro; numero <= ultimo; numero++) visiveis.push(numero);
    visiveis = visiveis.filter(function (valor, indice, todos) { return todos.indexOf(valor) === indice; });
    visiveis.sort(function (a, b) { return a - b; });
    var anterior = 0;
    visiveis.forEach(function (numero) {
      if (anterior && numero > anterior + 1) adicionar('…', 0, 'Mais páginas', true, false);
      adicionar(String(numero), numero, 'Página ' + numero, false, numero === estado.pagina);
      anterior = numero;
    });
    adicionar('»', estado.pagina + 1, 'Próxima página', estado.pagina === paginas, false);
  }

  function filtrar() {
    var termo = normalizar(pesquisa.value.trim());
    var total = 0;
    tabelas.forEach(function (estado) {
      estado.filtrados = estado.registros.filter(function (registro) { return registro.pesquisa.indexOf(termo) !== -1; });
      estado.pagina = 1;
      total += estado.filtrados.length;
      document.getElementById('contagem-aba-' + estado.grupo).textContent = estado.filtrados.length;
      renderizar(estado);
    });
    document.getElementById('contagem-previa-rebanho').textContent = total + ' animais encontrados nas duas abas';
  }
  pesquisa.addEventListener('input', filtrar);
  filtrar();
})();
