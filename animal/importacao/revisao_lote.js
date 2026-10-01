(function () {
  'use strict';
  var form = document.getElementById('form-atualizacao-todos');
  var corpo = document.getElementById('corpo-lote');
  var erro = document.getElementById('erro-lote');
  var confirmar = form.querySelector('[type="submit"]');
  var anterior = document.getElementById('anterior-lote');
  var proxima = document.getElementById('proxima-lote');
  var filtros = document.querySelectorAll('#campos-lote input');
  var globais = [], atualizarLinhas = [], total = 0;
  function atualizarConfirmacao() {
    confirmar.disabled = carregando || salvando || !total || globais.length === filtros.length;
  }
  filtros.forEach(function (filtro) {
    filtro.addEventListener('change', function () {
      globais = Array.prototype.filter.call(filtros, function (input) { return !input.checked; }).map(function (input) { return input.value; });
      atualizarLinhas.forEach(function (atualizar) { atualizar(); });
      atualizarConfirmacao();
    });
  });
  var exclusoes = {}, pagina = 1, geracao = 0, carregando = false, salvando = false;
  var rotulos = {nome:'Nome', fbb:'FBB', tatuagem:'Tatuagem', data_de_nascimento:'Nascimento', causa_da_perda:'Causa da morte', data_de_saida:'Data da morte'};
  function formatar(campo, valor) {
    valor = String(valor == null ? '' : valor);
    if (campo.indexOf('data_de_') === 0 && /^\d{4}-\d{2}-\d{2}$/.test(valor)) return valor.split('-').reverse().join('/');
    return valor || 'Não informado';
  }
  function carregar(numero) {
    var atual = ++geracao;
    carregando = true; confirmar.disabled = true; anterior.disabled = true; proxima.disabled = true;
    atualizarLinhas = []; corpo.textContent = ''; erro.textContent = 'Carregando…';
    var dados = new URLSearchParams();
    ['token_previa','envio'].forEach(function (nome) { dados.set(nome, form.elements[nome].value); });
    dados.set('pagina', numero);
    fetch('animal/_previa_lote.php', {method:'POST',body:dados,credentials:'same-origin'}).then(function (resposta) {
      return resposta.json().then(function (dados) { if (!resposta.ok || dados.erro) throw new Error(dados.erro || 'Falha ao carregar a revisão.'); return dados; });
    }).then(function (dados) {
      if (atual !== geracao) return;
      pagina = dados.pagina;
      dados.itens.forEach(function (item) {
        Object.keys(item.novos).forEach(function (campo) {
          var linha = document.createElement('tr'), celula = document.createElement('td'), check = document.createElement('input');
          var alterado = String(item.anterior[campo] == null ? '' : item.anterior[campo]) !== String(item.novos[campo]);
          check.type = 'checkbox'; check.checked = alterado && !(exclusoes[item.linha] || []).includes(campo);
          function atualizarCampo() {
            check.disabled = globais.includes(campo);
            check.checked = alterado && !check.disabled && !(exclusoes[item.linha] || []).includes(campo);
            destacar();
          }
          atualizarLinhas.push(atualizarCampo);
          check.setAttribute('aria-label', 'Atualizar ' + rotulos[campo] + ' de ' + item.anterior.nome);
          function destacar() { linha.classList.toggle('warning', check.checked && String(item.anterior[campo] || '') !== String(item.novos[campo])); linha.classList.toggle('text-muted', alterado && !check.checked); }
          check.addEventListener('change', function () {
            var campos = (exclusoes[item.linha] || []).filter(function (valor) { return valor !== campo; });
            if (!check.checked) campos.push(campo);
            if (campos.length) exclusoes[item.linha] = campos; else delete exclusoes[item.linha];
            destacar();
          });
          if (alterado) celula.appendChild(check); else celula.textContent = '—';
          linha.appendChild(celula);
          [item.anterior.nome,rotulos[campo],formatar(campo,item.anterior[campo]),formatar(campo,item.novos[campo])].forEach(function (valor) { var td = document.createElement('td'); td.textContent = valor; linha.appendChild(td); });
          atualizarCampo(); corpo.appendChild(linha);
        });
      });
      document.getElementById('pagina-lote').textContent = 'Página ' + pagina + ' de ' + dados.paginas + ' — ' + dados.total + ' animais';
      erro.textContent = ''; carregando = false; total = dados.total; atualizarConfirmacao();
      anterior.disabled = pagina === 1; proxima.disabled = pagina === dados.paginas;
    }).catch(function (falha) { if (atual === geracao) { erro.textContent = falha.message; carregando = false; } });
  }
  anterior.addEventListener('click', function () { carregar(pagina-1); });
  proxima.addEventListener('click', function () { carregar(pagina+1); });
  form.addEventListener('submit', function (evento) {
    if (carregando || salvando || confirmar.disabled) { evento.preventDefault(); return; }
    form.elements.exclusoes_campos.value = JSON.stringify(exclusoes);
    form.elements.exclusoes_globais.value = JSON.stringify(globais);
    salvando = true; confirmar.disabled = true; confirmar.textContent = 'Salvando…';
  });
  document.addEventListener('DOMContentLoaded', function () {
    $('#confirmar-atualizacao-todos').on('show.bs.modal', function () { exclusoes = {}; globais = []; filtros.forEach(function (filtro) { filtro.checked = true; }); carregar(1); })
      .on('hide.bs.modal', function (evento) { if (salvando) evento.preventDefault(); else geracao++; });
  });
})();
