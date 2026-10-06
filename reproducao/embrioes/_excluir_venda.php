<?php
require_once __DIR__ . '/../../_config.php';
$idVenda = filter_input(INPUT_GET, 'id_venda', FILTER_VALIDATE_INT);
if (!$idVenda || $idVenda < 1) { header('Location: ../../geral.php?pg=embrioes'); exit; }
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();
try {
    mysqli_set_charset($link, 'utf8mb4'); mysqli_begin_transaction($link);
    $stmt = mysqli_prepare($link, 'DELETE FROM venda_embriao WHERE id = ?'); mysqli_stmt_bind_param($stmt, 'i', $idVenda); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($link, 'DELETE FROM controle_financeiro WHERE id_embriao = ?'); mysqli_stmt_bind_param($stmt, 'i', $idVenda); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    mysqli_commit($link);
    $_SESSION['alerta_embrioes'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Venda de embriões excluída com sucesso.');
} catch (Exception $e) {
    mysqli_rollback($link); $_SESSION['alerta_embrioes'] = array('tipo'=>'danger', 'titulo'=>'Erro!', 'mensagem'=>'Não foi possível excluir a venda. Tente novamente.');
}
DBClose($link);
header('Location: ../../geral.php?pg=embrioes', true, 303);
exit;
