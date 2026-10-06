<?php
require_once __DIR__ . '/../../_config.php';
require_once __DIR__ . '/_lista_nascimentos.php';
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
renderListaNascimentos(consultarUltimosNascimentos($_GET));
