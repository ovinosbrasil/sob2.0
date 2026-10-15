<?php
$alertaVendas = $_SESSION['alerta_vendas'] ?? null;
$camposVenda = $_SESSION['campos_venda'] ?? array();
unset($_SESSION['alerta_vendas'], $_SESSION['campos_venda']);
if (in_array($pg, array('vender', 'relatorio_venda', 'comprador'), true) || $alertaVendas): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if ($alertaVendas): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaVendas, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
