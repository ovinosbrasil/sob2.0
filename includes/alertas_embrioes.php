<?php
$alertaEmbrioes = $_SESSION['alerta_embrioes'] ?? null;
unset($_SESSION['alerta_embrioes']);
if (in_array($pg, array('embrioes', 'vender_embrioes'), true) || $alertaEmbrioes): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if ($alertaEmbrioes): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaEmbrioes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
