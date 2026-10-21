<?php
require_once __DIR__ . '/includes/sessao.php';
iniciarSessaoSob();
encerrarSessaoSob();
header('Cache-Control: no-store');
header('Location: ' . caminhoSessaoSob() . 'index.php', true, 303);
exit;
