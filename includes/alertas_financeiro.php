<?php if ($pg === 'financeiro'):
$alertaFinanceiro = $_SESSION['alerta_financeiro'] ?? null;
$camposFinanceiro = $_SESSION['campos_financeiro'] ?? null;
unset($_SESSION['alerta_financeiro'], $_SESSION['campos_financeiro']);
?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<script id="financeiro-retorno" type="application/json"><?=json_encode(array('alerta' => $alertaFinanceiro, 'formulario' => $camposFinanceiro), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?></script>
<script src="dist/js/alertas-financeiro.js?v=<?=filemtime(__DIR__ . '/../dist/js/alertas-financeiro.js')?>"></script>
<?php endif; ?>
