<?php
require_once __DIR__ . '/../../_config.php';

$idVenda = filter_input(INPUT_GET, 'id_venda', FILTER_VALIDATE_INT);
if (!$idVenda || $idVenda < 1) {
    header('Location: ../../geral.php?pg=semen');
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();
try {
    mysqli_set_charset($link, 'utf8mb4');
    mysqli_begin_transaction($link);
    $stmt = mysqli_prepare($link, 'DELETE FROM venda_semen WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $idVenda);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($link, 'DELETE FROM controle_financeiro WHERE id_semen = ?');
    mysqli_stmt_bind_param($stmt, 'i', $idVenda);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_commit($link);
} catch (Exception $e) {
    mysqli_rollback($link);
    $_SESSION['semen_flash'] = array('erro' => 'Não foi possível excluir a venda. Tente novamente.');
}
DBClose($link);
header('Location: ../../geral.php?pg=semen', true, 303);
exit;
