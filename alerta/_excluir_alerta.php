<?php
require_once __DIR__ . '/../_config.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ../geral.php');
    exit;
}
$id = filter_var($_POST['id_alerta'] ?? null, FILTER_VALIDATE_INT);
$token = $_POST['csrf_token'] ?? '';
if (!is_string($token) || empty($_SESSION['tarefa_exclusao_csrf']) || !hash_equals($_SESSION['tarefa_exclusao_csrf'], $token)) {
    $_SESSION['tarefa_flash'] = array('tipo' => 'danger', 'mensagem' => 'Sua sessão expirou. Atualize a página e tente novamente.');
} elseif (!$id || $id < 1) {
    $_SESSION['tarefa_flash'] = array('tipo' => 'danger', 'mensagem' => 'Tarefa inválida para exclusão.');
} else {
    $link = null;
    $consulta = null;
    try {
        $link = DBConnect();
        $consulta = mysqli_prepare($link, 'DELETE FROM alerta WHERE id = ? LIMIT 1');
        if (!$consulta) { throw new RuntimeException('Falha ao preparar exclusão.'); }
        mysqli_stmt_bind_param($consulta, 'i', $id);
        if (!mysqli_stmt_execute($consulta)) { throw new RuntimeException('Falha ao excluir tarefa.'); }
        $_SESSION['tarefa_flash'] = mysqli_stmt_affected_rows($consulta) === 1
            ? array('tipo' => 'success', 'mensagem' => 'Tarefa excluída com sucesso.')
            : array('tipo' => 'warning', 'mensagem' => 'Esta tarefa não foi encontrada ou já foi excluída.');
    } catch (Throwable $erro) {
        error_log('Falha ao excluir tarefa de manejo: ' . $erro->getMessage());
        $_SESSION['tarefa_flash'] = array('tipo' => 'danger', 'mensagem' => 'Não foi possível excluir a tarefa. Tente novamente.');
    } finally {
        if ($consulta) { mysqli_stmt_close($consulta); }
        if ($link) { DBClose($link); }
    }
}
header('Location: ../geral.php', true, 303);
exit;
