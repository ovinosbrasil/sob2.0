(function () {
  'use strict';
  var modal = document.getElementById('cadastro-previa-rebanho');
  var form = document.getElementById('form-cadastro-previa');
  var corpo = document.getElementById('dados-cadastro-previa');
  var erro = document.getElementById('erro-cadastro-previa');
  var confirmar = document.getElementById('confirmar-cadastro-previa');
  var geracao = 0, salvando = false, origem = null;
  var rotulos = {
    nome: 'Nome', fbb: 'FBB', tatuagem: 'Tatuagem', data_de_nascimento: 'Nascimento',
    causa_da_perda: 'Causa da morte', data_de_saida: 'Data da morte',
    sexo: 'Sexo', raca: 'Raça', data_de_entrada: 'Data de entrada', observacoes: 'Observações',
    entrada: 'Forma de entrada', tipo_reproducao: 'Tipo de reprodução', pai: 'Pai', mae: 'Mãe',
    link_fbb: 'Link FBB', fbb_img: 'Imagem FBB', peso2: 'Segunda pesagem', parcelas: 'Parcelas',
    tipo_venda: 'Tipo de venda', tipo: 'Tipo', confirmacao: 'Confirmação', prolapso: 'Prolapso',
    criador: 'Criador', status: 'Situação', receptora: 'Receptora', chip: 'Chip'
  };
  function texto(tag, conteudo) {
    var elemento = document.createElement(tag);
    elemento.textContent = conteudo;
    return elemento;
  }
  function mostrarErro(mensagem) { erro.textContent = mensagem; erro.hidden = false; }
  function enviar(acao) {
    var dados = new FormData(form);
    dados.append('acao', acao);
    return fetch(form.action, {method: 'POST', body: dados, credentials: 'same-origin'})
      .then(function (resposta) {
        return resposta.json().catch(function () {
          throw new Error('O servidor retornou uma resposta inválida. Recarregue a página e tente novamente.');
        }).then(function (dados) {
          if (!resposta.ok || dados.erro) throw new Error(dados.erro || 'Não foi possível concluir o cadastro.');
          return dados;
        });
      });
  }
  function referencia(ref) {
    if (!ref) return 'Não informado — sem vínculo';
    return ref.nome + ' — ' + (ref.origem === 'animais' ? 'Rebanho' : 'Terceiro') +
      (ref.id ? ' (ID ' + ref.id + ')' : ' (será cadastrado antes; novo ID)');
  }
  function montar(plano, ignorados) {
    corpo.textContent = '';
    if (ignorados && ignorados.length) {
      var aviso = document.createElement('div');
      aviso.className = 'alert alert-warning';
      aviso.appendChild(texto('p', ignorados.length + ' animal(is) serão ignorados por dados inconsistentes:'));
      var lista = document.createElement('ul');
      ignorados.forEach(function (item) {
        lista.appendChild(texto('li', 'Linha ' + item.linha + ' — ' + item.nome + ': ' + item.motivo));
      });
      aviso.appendChild(lista); corpo.appendChild(aviso);
    }
    corpo.appendChild(texto('p', plano.length + ' animal(is) serão cadastrados, na ordem abaixo:'));
    plano.forEach(function (op, indice) {
      var box = document.createElement('section');
      box.appendChild(texto('h4', (indice + 1) + '. ' + op.dados.nome + ' — ' + (op.origem === 'animais' ? 'Rebanho' : 'Terceiro')));
      var tabela = document.createElement('table');
      tabela.className = 'table table-bordered table-condensed';
      var tbody = document.createElement('tbody');
      function linha(campo, valor) {
        var tr = document.createElement('tr'), th = texto('th', campo);
        th.scope = 'row'; tr.appendChild(th); tr.appendChild(texto('td', valor)); tbody.appendChild(tr);
      }
      Object.keys(op.dados).forEach(function (campo) {
        var valor = op.dados[campo];
        if (valor === '' || valor === null) valor = 'Não informado';
        if (/^data_/.test(campo) && /^\d{4}-\d{2}-\d{2}$/.test(valor)) valor = valor.split('-').reverse().join('/');
        if (campo === 'status') valor = Number(valor) === 1 ? 'Morto' : 'Vivo';
        if (campo === 'entrada') valor = 'Cadastro de rebanho (2)';
        linha(rotulos[campo] || campo, String(valor));
      });
      if (op.origem === 'animais') {
        linha('Pai', referencia(op.pai));
        linha('Mãe', referencia(op.mae));
      }
      tabela.appendChild(tbody); box.appendChild(tabela); corpo.appendChild(box);
    });
  }
  document.querySelectorAll('[data-cadastrar-previa]').forEach(function (botao) {
    botao.addEventListener('click', function () {
      if (salvando) return;
      origem = botao;
      var atual = ++geracao;
      form.elements.linha.value = botao.getAttribute('data-cadastrar-previa');
      document.getElementById('aviso-cadastro-todos').hidden = form.elements.linha.value !== 'todos';
      form.elements.confirmacao.value = '';
      confirmar.disabled = true; confirmar.textContent = 'Confirmar cadastro'; erro.hidden = true;
      corpo.textContent = 'Verificando animais e parentesco…';
      $(modal).modal('show');
      enviar('preparar').then(function (dados) {
        if (atual !== geracao) return;
        montar(dados.plano, dados.ignorados);
        form.elements.confirmacao.value = dados.confirmacao;
        confirmar.disabled = !dados.plano.length;
      }).catch(function (falha) {
        if (atual !== geracao) return;
        corpo.textContent = '';
        mostrarErro(falha.message || 'Falha ao consultar os cadastros. Tente novamente.');
      });
    });
  });
  form.addEventListener('submit', function (evento) {
    evento.preventDefault();
    if (salvando || confirmar.disabled || !form.elements.confirmacao.value) return;
    salvando = true; confirmar.disabled = true; confirmar.textContent = 'Cadastrando…'; erro.hidden = true;
    enviar('confirmar').then(function () {
      window.location.href = 'geral.php?pg=atualizar_rebanho';
    }).catch(function (falha) {
      salvando = false;
      confirmar.textContent = 'Confirmar cadastro';
      mostrarErro(falha.message + ' Feche e abra o cadastro novamente para revisar.');
    });
  });
  document.addEventListener('DOMContentLoaded', function () {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (evento) {
      var cadastro = evento.target.getAttribute('href') === '#aba-previa-nao_cadastrados';
      document.getElementById('cadastrar-todos-previa').hidden = !cadastro;
      document.getElementById('atualizar-todos-previa').hidden = evento.target.getAttribute('href') !== '#aba-previa-possiveis_atualizacoes';
    });
    $(modal).on('hide.bs.modal', function (evento) { if (salvando) evento.preventDefault(); })
      .on('hidden.bs.modal', function () { geracao++; if (origem) origem.focus(); });
  });
})();
