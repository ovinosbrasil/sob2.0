<?php
function definirEstadoTerceiro($link, $id, $ativo) {
    $stmt = mysqli_prepare($link, 'SELECT id FROM terceiros WHERE id = ?');
    if (!$stmt) { throw new RuntimeException('Falha ao consultar o cadastro.'); }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) { throw new RuntimeException('Falha ao consultar o cadastro.'); }
    $existe = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$existe) { return false; }
    $stmt = mysqli_prepare($link, 'UPDATE terceiros SET ativo = ? WHERE id = ?');
    if (!$stmt) { throw new RuntimeException('Falha ao atualizar.'); }
    mysqli_stmt_bind_param($stmt, 'ii', $ativo, $id);
    if (!mysqli_stmt_execute($stmt)) { throw new RuntimeException('Falha ao atualizar.'); }
    mysqli_stmt_close($stmt);
    return true;
}
