<?php require_once __DIR__ . '/../includes/csrf.php';

require_once __DIR__ . '/../includes/busca_compradores.php';
require_once __DIR__ . '/../includes/busca_animais.php';
$camposVenda = $camposVenda ?? array();
$hVenda = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
?>
<script type="text/javascript">
(function () {
  var limiteAnimais = 6;
  var camposAnteriores = <?=json_encode($camposVenda, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;

  function valorNumerico(valor) {
    valor = String(valor || '').replace(/\s/g, '').replace(/,/g, '');
    var numero = parseFloat(valor);
    return isNaN(numero) ? 0 : numero;
  }

  window.calcular_valor = function () {
    var total = 0;
    document.querySelectorAll('#lista-animais-venda .campo-valor-venda').forEach(function (campo) {
      total += valorNumerico(campo.value);
    });
    document.getElementById('valor_total').value = total.toFixed(2);
  };

  function atualizarBotoes() {
    var linhas = document.querySelectorAll('#lista-animais-venda .linha-animal-venda');
    linhas.forEach(function (linha, indice) {
      var botaoAdicionar = linha.querySelector('.adicionar-animal-venda');
      var botaoRemover = linha.querySelector('.remover-animal-venda');
      botaoAdicionar.style.display = indice === 0 && linhas.length < limiteAnimais ? 'inline-block' : 'none';
      botaoRemover.style.display = indice > 0 ? 'inline-block' : 'none';
    });
  }

  function criarLinha(numero) {
    var linha = document.createElement('div');
    linha.className = 'row linha-animal-venda';
    linha.setAttribute('data-numero', numero);
    linha.innerHTML =
      '<div class="col-md-7 col-sm-7">' +
        '<div class="form-group campo-busca-animal">' +
          document.getElementById('modelo-busca-animal-venda').innerHTML.replace(/LINHA/g, String(numero)) +
        '</div>' +
      '</div>' +
      '<div class="col-md-4 col-sm-4">' +
        '<div class="form-group">' +
          '<label for="preco_conjunto_' + numero + '">Valor<span class="text-danger">*</span></label>' +
          '<div class="input-group"><span class="input-group-addon">R$</span>' +
            '<input type="text" class="form-control campo-valor-venda" id="preco_conjunto_' + numero + '" name="preco_conjunto_' + numero + '" placeholder="0,00">' +
          '</div>' +
        '</div>' +
      '</div>' +
      '<div class="col-md-1 col-sm-1 coluna-acao-animal">' +
        '<button type="button" class="btn btn-success adicionar-animal-venda" title="Adicionar outro animal" aria-label="Adicionar outro animal"><i class="fa fa-plus"></i></button>' +
        '<button type="button" class="btn btn-danger remover-animal-venda" title="Remover animal" aria-label="Remover animal"><i class="fa fa-minus"></i></button>' +
      '</div>';

    var campoAnimal = linha.querySelector('#animal_' + numero);
    var campoValor = linha.querySelector('#preco_conjunto_' + numero);
    campoAnimal.value = camposAnteriores['animal_' + numero] || '';
    campoValor.value = camposAnteriores['preco_conjunto_' + numero] || '';
    linha.querySelector('[data-busca-animais-id]').value = camposAnteriores['animal_' + numero + '_id'] || '';
    linha.querySelector('[data-busca-animais-origem]').value = camposAnteriores['animal_' + numero + '_origem'] || '';
    if (window.BuscaAnimais) window.BuscaAnimais.iniciarTodos(linha);
    campoValor.addEventListener('keyup', calcular_valor);
    campoValor.addEventListener('blur', calcular_valor);
    linha.querySelector('.adicionar-animal-venda').addEventListener('click', adicionarAnimalVenda);
    linha.querySelector('.remover-animal-venda').addEventListener('click', function () {
      linha.remove();
      calcular_valor();
      atualizarBotoes();
    });
    return linha;
  }

  function adicionarAnimalVenda() {
    var usados = {};
    document.querySelectorAll('#lista-animais-venda .linha-animal-venda').forEach(function (linhaAtual) {
      usados[parseInt(linhaAtual.getAttribute('data-numero'), 10)] = true;
    });
    var numero = 2;
    while (numero <= limiteAnimais && usados[numero]) numero++;
    if (numero > limiteAnimais) return;
    var linha = criarLinha(numero);
    document.getElementById('lista-animais-venda').appendChild(linha);
    if (window.jQuery && jQuery.fn.priceFormat) jQuery('#preco_conjunto_' + numero).priceFormat();
    atualizarBotoes();
    linha.querySelector('input').focus();
  }

  window.ativar_venda = function () {
    var valido = true;
    var pendentes = [];
    var primeiroPendente = null;
    var campos = [document.getElementById('comprador'), document.getElementById('data'), document.getElementById('tipo_venda')];
    document.querySelectorAll('#lista-animais-venda .linha-animal-venda').forEach(function (linha) {
      campos.push(linha.querySelector('input[name^="animal_"]'));
      campos.push(linha.querySelector('.campo-valor-venda'));
    });
    campos.forEach(function (campo) {
      var preenchido = campo && campo.value.trim() !== '';
      if (campo && campo.classList.contains('campo-valor-venda')) preenchido = valorNumerico(campo.value) > 0;
      campo.style.borderColor = preenchido ? '#00a65a' : '#dd4b39';
      if (!preenchido) {
        valido = false;
        if (!primeiroPendente) primeiroPendente = campo;
        var label = campo.labels && campo.labels[0];
        pendentes.push(label ? label.textContent.replace(/\*/g, '').trim() : campo.name);
      }
    });
    calcular_valor();
    if (!valido) {
      SobAlertas.camposObrigatorios(pendentes);
      if (primeiroPendente) primeiroPendente.focus();
      return false;
    }
    var selecaoInvalida = null;
    document.querySelectorAll('#lista-animais-venda .linha-animal-venda').forEach(function (linha) {
      var id = linha.querySelector('[data-busca-animais-id]').value;
      var origem = linha.querySelector('[data-busca-animais-origem]').value;
      if ((!id || origem !== 'rebanho') && !selecaoInvalida) selecaoInvalida = linha.querySelector('.sob-busca-animais__input');
    });
    if (selecaoInvalida) {
      SobAlertas.mostrar({tipo:'warning', titulo:'Atenção!', mensagem:'Selecione os animais na lista de pesquisa.'});
      selecaoInvalida.focus();
      return false;
    }
    var campoData = document.getElementById('data');
    var partes = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(campoData.value);
    var data = partes ? new Date(Number(partes[3]), Number(partes[2]) - 1, Number(partes[1])) : null;
    if (!data || data.getFullYear() !== Number(partes[3]) || data.getMonth() !== Number(partes[2]) - 1 || data.getDate() !== Number(partes[1])) {
      SobAlertas.mostrar({tipo:'warning', titulo:'Atenção!', mensagem:'Informe uma data válida no formato dd/mm/aaaa.'});
      campoData.focus();
      return false;
    }
    return true;
  };

  document.addEventListener('DOMContentLoaded', function () {
    var lista = document.getElementById('lista-animais-venda');
    lista.appendChild(criarLinha(1));
    for (var numero = 2; numero <= limiteAnimais; numero++) {
      if (camposAnteriores['animal_' + numero] || camposAnteriores['preco_conjunto_' + numero]) lista.appendChild(criarLinha(numero));
    }
    document.getElementById('form-venda-conjunta').addEventListener('invalid', function (evento) {
      evento.preventDefault();
      var label = evento.target.labels && evento.target.labels[0];
      SobAlertas.camposObrigatorios([label ? label.textContent.replace(/\*/g, '').trim() : evento.target.name]);
      evento.target.focus();
    }, true);
    if (window.jQuery && jQuery.fn.priceFormat) jQuery('#lista-animais-venda .campo-valor-venda').priceFormat();
    calcular_valor();
    atualizarBotoes();
  });
})();
</script>

<style>
  #form-venda-conjunta .box { margin-bottom: 20px; }
  #lista-animais-venda .linha-animal-venda { position: relative; }
  #lista-animais-venda .linha-animal-venda + .linha-animal-venda { padding-top: 12px; }
  #lista-animais-venda .coluna-acao-animal { padding-top: 25px; white-space: nowrap; }
  #lista-animais-venda .coluna-acao-animal .btn { min-width: 38px; }
  #form-venda-conjunta textarea { resize: vertical; min-height: 118px; }
  #form-venda-conjunta .acoes-venda { text-align: right; padding-top: 24px; }
  @media (max-width: 767px) {
    #lista-animais-venda .coluna-acao-animal { padding-top: 0; margin-bottom: 15px; }
    #form-venda-conjunta .acoes-venda .btn { width: 100%; }
  }
</style>

<section class="content-header">
  <h1>Vender animais</h1>
  <ol class="breadcrumb">
    <li><a href="geral.php?pg=relatorio_venda"><i class="fa fa-shopping-cart"></i> Vendas</a></li>
    <li class="active">Vender animais</li>
  </ol>
</section>

<template id="modelo-busca-animal-venda">
<?php renderBuscaAnimais(array('id'=>'animal_LINHA', 'name'=>'animal_LINHA', 'label'=>'Animal', 'tipo'=>'rebanho_ativo', 'required'=>true)); ?>
</template>
<section class="content">
  <form id="form-venda-conjunta" role="form" action="vendas/_venda_conjunta.php" method="post" onsubmit="return ativar_venda();">
        <?=campoCsrfSob()?>
    <div class="box" style="border-top:0;">
      <div class="box-header with-border">
        <h3 class="box-title">Animais da venda</h3>
      </div>
      <div class="box-body" id="lista-animais-venda"></div>

      <div class="box-header with-border">
        <h3 class="box-title">Dados da venda</h3>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <?php renderBuscaCompradores(array('id'=>'comprador','name'=>'comprador','label'=>'Comprador','value'=>$camposVenda['comprador'] ?? ($comprador[0]['nome'] ?? ''),'required'=>true,'placeholder'=>'Digite para pesquisar','novo_modal'=>'novo-comprador-modal','novo_texto'=>'Novo')); ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="data">Data da venda<span class="text-danger">*</span></label>
              <div class="input-group date">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" class="form-control" id="data" name="data" value="<?=$hVenda($camposVenda['data'] ?? ($data_venda ?? ''))?>" placeholder="dd/mm/aaaa">
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="forma">Forma de pagamento</label>
              <select class="form-control select" id="forma" name="forma">
                <?php $formaVenda = $camposVenda['forma'] ?? ($venda[0]['forma_de_pagamento'] ?? ''); if ($formaVenda !== ''): ?><option value="<?=$hVenda($formaVenda)?>"><?=$hVenda($formaVenda)?></option><?php endif; ?>
                <option value="">Selecionar</option>
                <option value="Pix">Pix</option>
                <option value="Boleto">Boleto</option><option value="Cheque">Cheque</option><option value="Dinheiro">Dinheiro</option><option value="Depósito">Depósito</option><option value="Transferência">Transferência</option><option value="Troca">Troca</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="parcelas">Parcelas</label>
              <select class="form-control select" id="parcelas" name="parcelas">
                <? for($parcela=1; $parcela<=24; $parcela++){ ?><option value="<?=$parcela?>"<?=((int)($camposVenda['parcelas'] ?? ($venda[0]['parcelas'] ?? 1))===$parcela?' selected':'')?>><?=$parcela?>x</option><? } ?>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="tipo_venda">Tipo de venda<span class="text-danger">*</span></label>
              <select class="form-control select" id="tipo_venda" name="tipo_venda">
                <option value="">Selecionar</option>
                <? foreach(array('Fazenda','Leilão','Exposição','Virtual') as $tipoVenda){ ?><option value="<?=$tipoVenda?>"<?=(($camposVenda['tipo_venda'] ?? ($venda[0]['tipo_venda'] ?? ''))===$tipoVenda?' selected':'')?>><?=$tipoVenda?></option><? } ?>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="valor_total">Valor total<span class="text-danger">*</span></label>
              <div class="input-group"><span class="input-group-addon">R$</span><input type="text" class="form-control" id="valor_total" name="valor_total" value="0.00" readonly></div>
              <small class="help-block">O valor total será dividido entre as parcelas mensais.</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label for="observacoes">Observações</label>
              <textarea class="form-control" name="observacoes" id="observacoes" rows="4"><?=$hVenda($camposVenda['observacoes'] ?? ($venda[0]['observacoes'] ?? ''))?></textarea>
            </div>
          </div>
          <div class="col-md-4 acoes-venda">
            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Finalizar venda</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</section>

<?php require __DIR__ . '/../includes/modal_novo_comprador.php'; ?>
