<?php
require_once __DIR__ . '/../../_config.php';

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$idControle = filter_var($_GET['id_controle'] ?? 0, FILTER_VALIDATE_INT);

if (!$idLote) {
    header('Location: ../../geral.php?pg=lista_monta');
    exit;
}
$idLote = (int)$idLote;

if ($idControle) {
    DBDelete('monta_controle', "id = '" . (int)$idControle . "' AND id_monta = '$idLote'");
} else {
    // Compatibilidade com links antigos.
    $idMae = filter_var($_GET['id_mae'] ?? 0, FILTER_VALIDATE_INT);
    if ($idMae) {
        DBDelete('monta_controle', "id_animal = '" . (int)$idMae . "' AND id_monta = '$idLote'");
    }
}

$controles = DBRead('monta_controle', "WHERE id_monta = '$idLote'") ?: array();
$positivos = DBRead('monta_controle', "WHERE id_monta = '$idLote' AND ultrassom = '1'") ?: array();
$nascimentos = DBRead('monta_controle', "WHERE id_monta = '$idLote' AND status_nascimento = '1'") ?: array();
$total = count($controles);

DBUpdate('lotes_reproducao', array(
    'ultrassom' => $total ? count($positivos) * 100 / $total : 0,
    'crias' => $total ? count($nascimentos) * 100 / $total : 0,
    'femeas' => $total
), "id_lote = '$idLote' AND tipo = '0'");

header('Location: ../../geral.php?pg=monta&id_lote=' . $idLote);
exit;
