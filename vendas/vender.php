<?php require_once __DIR__ . '/../includes/busca_compradores.php'; ?>
<script type="text/javascript">
(function () {
  var limiteAnimais = 6;

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
          '<label for="animal_' + numero + '">' + (numero === 1 ? 'Animal' : 'Outro animal') + '<span class="text-danger">*</span></label>' +
          '<input type="text" name="animal_' + numero + '" id="animal_' + numero + '" class="form-control" autocomplete="off" placeholder="Digite para pesquisar">' +
          '<div id="lista_animal_' + numero + '" class="lista-resultado-animal"></div>' +
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
    campoAnimal.addEventListener('keyup', function () { pesquisar_animal_conjunta(this.value, numero); });
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
    var campos = [document.getElementById('comprador'), document.getElementById('data'), document.getElementById('tipo_venda')];
    document.querySelectorAll('#lista-animais-venda .linha-animal-venda').forEach(function (linha) {
      campos.push(linha.querySelector('input[name^="animal_"]'));
      campos.push(linha.querySelector('.campo-valor-venda'));
    });
    campos.forEach(function (campo) {
      var preenchido = campo && campo.value.trim() !== '';
      if (campo && campo.classList.contains('campo-valor-venda')) preenchido = valorNumerico(campo.value) > 0;
      campo.style.borderColor = preenchido ? '#00a65a' : '#dd4b39';
      if (!preenchido) valido = false;
    });
    calcular_valor();
    return valido;
  };

  document.addEventListener('DOMContentLoaded', function () {
    var lista = document.getElementById('lista-animais-venda');
    lista.appendChild(criarLinha(1));
    if (window.jQuery && jQuery.fn.priceFormat) jQuery('#preco_conjunto_1').priceFormat();
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
  #lista-animais-venda .lista-resultado-animal { border: 1px solid #d2d6de; background: #fff; display: none; position: absolute; left: 15px; right: 15px; z-index: 1050; max-height: 230px; overflow-y: auto; box-shadow: 0 4px 8px rgba(0,0,0,.12); }
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

<section class="content">
  <form id="form-venda-conjunta" role="form" action="vendas/_venda_conjunta.php" method="post" onsubmit="return ativar_venda();">
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
              <?php renderBuscaCompradores(array('id'=>'comprador','name'=>'comprador','label'=>'Comprador','value'=>$comprador[0]['nome'] ?? '','required'=>true,'placeholder'=>'Digite para pesquisar','novo_url'=>'geral.php?pg=compradores','novo_texto'=>'Novo')); ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="data">Data da venda<span class="text-danger">*</span></label>
              <div class="input-group date">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" class="form-control" id="data" name="data" value="<?=$data_venda?>" placeholder="dd/mm/aaaa">
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="forma">Forma de pagamento</label>
              <select class="form-control select" id="forma" name="forma">
                <? if($venda[0]['forma_de_pagamento']){ ?><option value="<?=$venda[0]['forma_de_pagamento']?>"><?=$venda[0]['forma_de_pagamento']?></option><? } ?>
                <option value="">Selecionar</option>
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
                <? for($parcela=1; $parcela<=24; $parcela++){ ?><option value="<?=$parcela?>"<?=((int)$venda[0]['parcelas']===$parcela?' selected':'')?>><?=$parcela?>x</option><? } ?>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="tipo_venda">Tipo de venda<span class="text-danger">*</span></label>
              <select class="form-control select" id="tipo_venda" name="tipo_venda">
                <option value="">Selecionar</option>
                <? foreach(array('Fazenda','Leilão','Exposição','Virtual') as $tipoVenda){ ?><option value="<?=$tipoVenda?>"<?=($venda[0]['tipo_venda']===$tipoVenda?' selected':'')?>><?=$tipoVenda?></option><? } ?>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="valor_total">Valor total<span class="text-danger">*</span></label>
              <div class="input-group"><span class="input-group-addon">R$</span><input type="text" class="form-control" id="valor_total" name="valor_total" value="0.00" readonly></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label for="observacoes">Observações</label>
              <textarea class="form-control" name="observacoes" id="observacoes" rows="4"><?=$venda[0]['observacoes']?></textarea>
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
