<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/../includes/csrf.php';
exigirPostCsrfSob(false);
require_once __DIR__ . '/_alertas.php';
$registro = registroFinanceiro();
$id = (int)$registro['id'];
gravarFinanceiro('DBUpdate', 'controle_financeiro', array('status' => 1), "id = '$id'");
avisarFinanceiro('success', 'Lançamento confirmado com sucesso.');
