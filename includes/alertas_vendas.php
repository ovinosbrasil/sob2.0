<?php
$alertaCompradores = $_SESSION['alerta_compradores'] ?? null;
$camposComprador = $_SESSION['campos_comprador'] ?? array();
unset($_SESSION['alerta_compradores'], $_SESSION['campos_comprador']);
$alertaVendas = $_SESSION['alerta_vendas'] ?? null;
$camposVenda = $_SESSION['campos_venda'] ?? array();
unset($_SESSION['alerta_vendas'], $_SESSION['campos_venda']);
if (in_array($pg, array('vender', 'relatorio_venda', 'comprador', 'compradores'), true) || $alertaVendas || $alertaCompradores): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if ($alertaVendas): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaVendas, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php if ($alertaCompradores): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaCompradores, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<script>
var avisoCompradorPendente = false;
document.addEventListener('invalid', function (evento) {
  if (!evento.target.form || !evento.target.form.hasAttribute('data-alertas-compradores')) return;
  evento.preventDefault();
  if (avisoCompradorPendente) return;
  avisoCompradorPendente = true;
  window.setTimeout(function () { avisoCompradorPendente = false; }, 0);
  SobAlertas.mostrar({tipo:'warning', titulo:'Atenção!', mensagem:evento.target.validity.valueMissing ? 'Informe o nome completo do comprador.' : 'Confira o formato dos campos informados.'});
  evento.target.focus();
}, true);
</script>
<?php endif; ?>
