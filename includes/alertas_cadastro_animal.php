<?php
$alertaCadastroAnimal = $_SESSION['alerta_exclusao_animal'] ?? $_SESSION['alerta_cadastro_animal'] ?? null;
unset($_SESSION['alerta_cadastro_animal'], $_SESSION['alerta_exclusao_animal']);
if (in_array($pg, array('cadastrar_animal', 'cadastrar_nascimento'), true) || $alertaCadastroAnimal): ?>
<link rel="stylesheet" href="dist/css/alertas.css">
<script src="dist/js/alertas.js"></script>
<?php if (in_array($pg, array('cadastrar_animal', 'cadastrar_nascimento'), true)): ?>
<script src="animal/cadastrar/alertas_cadastro.js?v=<?=filemtime(__DIR__ . '/../animal/cadastrar/alertas_cadastro.js')?>"></script>
<?php endif; ?>
<?php if ($alertaCadastroAnimal): ?>
<script>SobAlertas.mostrar(<?=json_encode($alertaCadastroAnimal, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);</script>
<?php endif; ?>
<?php endif; ?>
