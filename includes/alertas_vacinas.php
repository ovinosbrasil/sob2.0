<?php
$alertaVacina = $_SESSION['alerta_vacina'] ?? null;
unset($_SESSION['alerta_vacina']);
if ($pg === 'cadastrar_vacina' || $alertaVacina): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if ($alertaVacina): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaVacina, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
