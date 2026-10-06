<?php
$idSemen = filter_var($_GET['id_embriao'] ?? 0, FILTER_VALIDATE_INT);
$registrosSemen = $idSemen ? (DBRead('semen', "WHERE id = '" . (int)$idSemen . "' AND qtd > 0") ?: array()) : array();
$semenVenda = $registrosSemen[0] ?? null;
$vendaFlash = $_SESSION['semen_venda_flash'] ?? null;
unset($_SESSION['semen_venda_flash']);
if (empty($_SESSION['semen_venda_csrf'])) {
    $_SESSION['semen_venda_csrf'] = bin2hex(random_bytes(32));
}
$machoVenda = array();
if ($semenVenda) {
    $idAnimal = (int)$semenVenda['id_animal'];
    $animais = DBRead(!empty($semenVenda['terceiro']) ? 'terceiros' : 'animais', "WHERE id = '$idAnimal'") ?: array();
    $machoVenda = $animais[0] ?? array();
}
$dadosVenda = $vendaFlash['dados'] ?? array();
?>
<script>
function validarVendaSemen() {
  var nomes = {comprador:'Cliente', 'data-venda-semen':'Data', 'tipo-venda-semen':'Tipo de venda', 'valor-venda-semen':'Valor', 'qtd-venda-semen':'Quantidade'};
  var faltantes = [], primeiro;
  Object.keys(nomes).forEach(function (id) {
    var campo = document.getElementById(id), vazio = !campo.value.trim();
    campo.style.borderColor = vazio ? '#dd4b39' : '';
    campo.setAttribute('aria-invalid', String(vazio));
    if (vazio) { faltantes.push(nomes[id]); primeiro = primeiro || campo; }
  });
  if (faltantes.length) { SobAlertas.camposObrigatorios(faltantes); primeiro.focus(); return false; }
  var quantidade = document.getElementById('qtd-venda-semen');
  if (!/^\d+$/.test(quantidade.value) || Number(quantidade.value) < 1 || Number(quantidade.value) > Number(quantidade.max)) {
    quantidade.style.borderColor = '#dd4b39';
    SobAlertas.mostrar({tipo:'warning', titulo:'Atenção!', mensagem:'Informe uma quantidade inteira entre 1 e ' + quantidade.max + '.'});
    quantidade.focus(); return false;
  }
  return true;
}
</script>

<section class="content-header">
  <h1>Vender Sêmen</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li><a href="geral.php?pg=semen">Banco de Sêmen</a></li>
    <li class="active">Vender</li>
  </ol>
</section>

<section class="content">
  <?php if (!$semenVenda): ?>
  <div class="alert alert-warning">Registro de sêmen não encontrado ou sem doses disponíveis.</div>
  <a href="geral.php?pg=semen" class="btn btn-default">Voltar ao Banco de Sêmen</a>
  <?php else: ?>


  <div class="box" style="border-top:0;">
    <form action="reproducao/semen/_vender.php?id_embriao=<?=(int)$idSemen?>" method="post" onsubmit="return validarVendaSemen()" novalidate>
      <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['semen_venda_csrf'], ENT_QUOTES, 'UTF-8')?>">
      <div class="box-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="semen-venda">Sêmen<span class="text-danger">*</span></label>
              <select class="form-control" id="semen-venda" name="embriao">
                <option value="<?=(int)$semenVenda['id']?>"><?=htmlspecialchars($machoVenda['nome'] ?? 'Animal não encontrado', ENT_QUOTES, 'UTF-8')?> — <?=(int)$semenVenda['qtd']?> dose(s) disponível(is)</option>
              </select>
            </div>
            <div class="form-group" style="position:relative;">
              <label for="comprador">Comprador<span class="text-danger">*</span></label>
              <input type="hidden" id="comprador_id" name="comprador_id" value="<?=htmlspecialchars($dadosVenda['comprador_id'] ?? '', ENT_QUOTES, 'UTF-8')?>">
              <div class="input-group">
                <input type="text" class="form-control" id="comprador" name="comprador" value="<?=htmlspecialchars($dadosVenda['comprador'] ?? '', ENT_QUOTES, 'UTF-8')?>" autocomplete="off" oninput="document.getElementById('comprador_id').value=''; pesquisar_comprador(this.value)" onfocus="pesquisar_comprador(this.value)">
                <span class="input-group-btn"><a class="btn btn-success" href="geral.php?pg=compradores" target="_blank" rel="noopener" title="Cadastrar comprador em nova aba">Novo</a></span>
              </div>
              <div id="lista_comprador" style="border:1px solid #bab1b4;position:absolute;z-index:1050;background:#fff;width:100%;display:none;"></div>
            </div>
            <div class="row">
              <div class="col-xs-6"><div class="form-group">
                <label for="parcelas-venda-semen">Parcelas</label>
                <select class="form-control" id="parcelas-venda-semen" name="parcelas">
                  <?php for ($parcela = 1; $parcela <= 24; $parcela++): ?><option value="<?=$parcela?>"<?=(int)($dadosVenda['parcelas'] ?? 1) === $parcela ? ' selected' : ''?>><?=$parcela?>x</option><?php endfor; ?>
                </select>
              </div></div>
              <div class="col-xs-6"><div class="form-group">
                <label for="qtd-venda-semen">Quantidade de doses<span class="text-danger">*</span></label>
                <input type="number" min="1" max="<?=(int)$semenVenda['qtd']?>" class="form-control" id="qtd-venda-semen" name="qtd" value="<?=htmlspecialchars($dadosVenda['qtd'] ?? '', ENT_QUOTES, 'UTF-8')?>" required>
              </div></div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="data-venda-semen">Data da venda<span class="text-danger">*</span></label>
              <div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" class="form-control pull-right" id="data-venda-semen" name="data" placeholder="dd/mm/aaaa" value="<?=htmlspecialchars($dadosVenda['data'] ?? '', ENT_QUOTES, 'UTF-8')?>"></div>
            </div>
            <div class="form-group">
              <label for="tipo-venda-semen">Tipo de venda<span class="text-danger">*</span></label>
              <select class="form-control" id="tipo-venda-semen" name="tipo_venda" required>
                <option value="">Selecionar</option>
                <?php foreach (array('Fazenda', 'Leilão', 'Exposição', 'Virtual') as $tipo): ?><option value="<?=$tipo?>"<?=($dadosVenda['tipo_venda'] ?? '') === $tipo ? ' selected' : ''?>><?=$tipo?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="valor-venda-semen">Valor<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="valor-venda-semen" name="valor" inputmode="numeric" maxlength="13" placeholder="0,00" value="<?=htmlspecialchars($dadosVenda['valor'] ?? '', ENT_QUOTES, 'UTF-8')?>" required>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="forma-venda-semen">Forma de pagamento</label>
              <select class="form-control" id="forma-venda-semen" name="forma">
                <option value="">Selecionar</option>
                <?php foreach (array('Boleto', 'Cheque', 'Dinheiro', 'Depósito', 'Transferência', 'Troca') as $forma): ?><option value="<?=$forma?>"<?=($dadosVenda['forma'] ?? '') === $forma ? ' selected' : ''?>><?=$forma?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="observacoes-venda-semen">Observações</label>
              <textarea class="form-control" name="observacoes" id="observacoes-venda-semen" rows="5" maxlength="200"><?=htmlspecialchars($dadosVenda['observacoes'] ?? '', ENT_QUOTES, 'UTF-8')?></textarea>
            </div>
            <div class="form-group"><button type="submit" class="btn btn-success btn-block"><i class="fa fa-check" aria-hidden="true"></i> Finalizar venda</button></div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <?php endif; ?>
</section>

<script>
(function () {
  var campo = document.getElementById('valor-venda-semen');
  if (!campo) return;

  function formatarValor() {
    var digitos = campo.value.replace(/\D/g, '').slice(0, 10);
    if (!digitos) {
      campo.value = '';
      return;
    }
    digitos = digitos.padStart(3, '0');
    var inteiros = digitos.slice(0, -2).replace(/^0+(?=\d)/, '');
    var centavos = digitos.slice(-2);
    campo.value = inteiros.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',' + centavos;
  }

  campo.addEventListener('input', formatarValor);
  formatarValor();
}());
</script>
