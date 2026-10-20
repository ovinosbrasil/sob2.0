<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
$registro = registroFinanceiro();
$id = (int)$registro['id'];
gravarFinanceiro('DBUpdate', 'controle_financeiro', array('status' => 0), "id = '$id'");
avisarFinanceiro('success', 'Confirmação cancelada. O lançamento está pendente.');
