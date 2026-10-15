<?php
$alertaChip = $_SESSION['alerta_chip'] ?? null;
unset($_SESSION['alerta_chip']);
if ($pg === 'cadastrar_chip' || $alertaChip): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if ($alertaChip): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaChip, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
