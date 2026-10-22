<?php
require_once __DIR__ . '/includes/sessao.php';
exigirSessaoSob(false, true);
require_once __DIR__ . '/includes/termos.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}
if (!aceiteTermosValidoSob($_POST, $_SESSION['termos_csrf'] ?? null)) {
    http_response_code(403);
    exit('Confirmação inválida. Volte à página inicial e confirme a leitura dos termos.');
}
$db = null;
try {
    $db = conexaoTermosSob();
    $id = (int)($_SESSION['user_id'] ?? 0);
    $stmt = mysqli_prepare($db, 'UPDATE user SET termos_aceitos = 1 WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if (!consultarAceiteSob($db, $id)) throw new RuntimeException('Aceite não persistido.');
} catch (Throwable $erro) {
    error_log('Falha ao persistir aceite dos termos de uso.');
    http_response_code(503);
    exit('Não foi possível registrar seu aceite. Tente novamente mais tarde.');
} finally {
    if ($db) mysqli_close($db);
}
unset($_SESSION['termos_csrf']);
header('Location: ' . caminhoSessaoSob() . 'geral.php', true, 303);
exit;
