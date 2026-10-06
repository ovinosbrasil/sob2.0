<?php
$alertaCadastroInseminacao = $_SESSION['alerta_cadastro_inseminacao'] ?? null;
$camposCadastroInseminacao = in_array($pg, array('cadastrar_inseminacao', 'inseminacao'), true) ? ($_SESSION['campos_cadastro_inseminacao'] ?? array()) : array();
unset($_SESSION['alerta_cadastro_inseminacao'], $_SESSION['campos_cadastro_inseminacao']);
if (in_array($pg, array('cadastrar_inseminacao', 'inseminacao'), true) || $alertaCadastroInseminacao): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var campos = <?=json_encode($camposCadastroInseminacao, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  Object.keys(campos).forEach(function (nome) {
    var formulario = document.querySelector('form[action^="reproducao/inseminacao/_cadastrar.php"], form[action^="reproducao/inseminacao/_alterar.php"]');
    var campo = formulario ? formulario.querySelector('[name="' + nome + '"]') : null;
    if (campo && typeof campos[nome] === 'string') { campo.value = campos[nome]; }
  });
  var alerta = <?=json_encode($alertaCadastroInseminacao, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  if (alerta) { SobAlertas.mostrar(alerta); }
});
</script>
<?php endif; ?>
