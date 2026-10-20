<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
$registro = registroFinanceiro();
$id = (int)$registro['id'];
gravarFinanceiro('DBDelete', 'controle_financeiro', "id = '$id'");
avisarFinanceiro('success', 'Lançamento excluído com sucesso.');
