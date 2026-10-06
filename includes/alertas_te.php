<?php
$alertaTe = $_SESSION['alerta_te'] ?? null;
$camposTe = in_array($pg, array('cadastrar_te', 'te'), true) ? ($_SESSION['campos_te'] ?? array()) : array();
unset($_SESSION['alerta_te'], $_SESSION['campos_te']);
if (in_array($pg, array('cadastrar_te', 'te'), true) || $alertaTe): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var campos = <?=json_encode($camposTe, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  Object.keys(campos).forEach(function (nome) {
    var formulario = document.querySelector('form[action^="reproducao/te/_cadastrar.php"], form[action^="reproducao/te/_alterar.php"]');
    var campo = formulario ? formulario.querySelector('[name="' + nome + '"]') : null;
    if (campo && typeof campos[nome] === 'string') { campo.value = campos[nome]; }
  });
  var alerta = <?=json_encode($alertaTe, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  if (alerta) { SobAlertas.mostrar(alerta); }
});
</script>
<?php endif; ?>
