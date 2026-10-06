<?php
$alertaSemen = $_SESSION['alerta_semen'] ?? null;
unset($_SESSION['alerta_semen']);
if (in_array($pg, array('semen', 'vender_semen'), true) || $alertaSemen): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if ($alertaSemen): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaSemen, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
