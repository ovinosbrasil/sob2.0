<?php
$alertaPesagem = $_SESSION['alerta_pesagem'] ?? null;
unset($_SESSION['alerta_pesagem']);
if ($pg === 'pesagem' || $alertaPesagem): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if ($alertaPesagem): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaPesagem, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
