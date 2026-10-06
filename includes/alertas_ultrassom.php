<?php
$alertaUltrassom = $_SESSION['alerta_ultrassom'] ?? null;
unset($_SESSION['alerta_ultrassom']);
if (in_array($pg, array('lista_ultrassom', 'cadastrar_ultrassom'), true) || $alertaUltrassom): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if ($alertaUltrassom): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaUltrassom, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
