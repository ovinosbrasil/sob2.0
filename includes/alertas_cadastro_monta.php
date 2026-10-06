<?php
$alertaCadastroMonta = $_SESSION['alerta_cadastro_monta'] ?? null;
$camposCadastroMonta = in_array($pg, array('cadastrar_monta', 'monta'), true) ? ($_SESSION['campos_cadastro_monta'] ?? array()) : array();
unset($_SESSION['alerta_cadastro_monta'], $_SESSION['campos_cadastro_monta']);
if (in_array($pg, array('cadastrar_monta', 'monta'), true) || $alertaCadastroMonta): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var campos = <?=json_encode($camposCadastroMonta, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  Object.keys(campos).forEach(function (nome) {
    var campo = document.querySelector('form[onsubmit="return validar_montar()"] [name="' + nome + '"]');
    if (campo && typeof campos[nome] === 'string') { campo.value = campos[nome]; }
  });
  var alerta = <?=json_encode($alertaCadastroMonta, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  if (alerta) { SobAlertas.mostrar(alerta); }
});
</script>
<?php endif; ?>
