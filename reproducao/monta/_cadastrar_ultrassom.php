<?php
require_once __DIR__ . '/../../_config.php';

$idControle = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$status = filter_var($_GET['status'] ?? 0, FILTER_VALIDATE_INT);

if (!$idControle || !in_array($status, array(1, 2), true)) {
    header('Location: ../../geral.php?pg=lista_monta');
    exit;
}
$idControle = (int)$idControle;
$controle = DBRead('monta_controle', "WHERE id = '$idControle'") ?: array();
if (!$controle) {
    header('Location: ../../geral.php?pg=lista_monta');
    exit;
}

$idLote = (int)$controle[0]['id_monta'];
DBUpdate('monta_controle', array('ultrassom' => $status, 'data_ultrassom' => (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d')), "id = '$idControle'");

$controles = DBRead('monta_controle', "WHERE id_monta = '$idLote'") ?: array();
$positivos = DBRead('monta_controle', "WHERE id_monta = '$idLote' AND ultrassom = '1'") ?: array();
$total = count($controles);
DBUpdate('lotes_reproducao', array(
    'ultrassom' => $total ? count($positivos) * 100 / $total : 0
), "id_lote = '$idLote' AND tipo = '0'");

header('Location: ../../geral.php?pg=monta&id_lote=' . $idLote);
exit;
