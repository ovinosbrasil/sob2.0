<?php
require_once __DIR__ . '/includes/sessao.php';
iniciarSessaoSob();
header('Cache-Control: no-store');
require __DIR__ . '/mysqli/environment.php';
require __DIR__ . '/includes/autenticacao.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: index.php', true, 303);
    exit;
}
$login = $_POST['login'] ?? null;
$senha = $_POST['senha'] ?? null;
$usuario = false;
$link = null;
if (is_string($login) && is_string($senha) && $login !== '' && $senha !== '') {
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $link = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        mysqli_set_charset($link, DB_CHARSET);
        $usuario = autenticarUsuario($link, $login, $senha, getenv('AUTH_ALLOW_LEGACY_PASSWORDS') !== '0');
    } catch (Throwable $erro) {
        // Nunca registrar credenciais, SQL ou mensagens que possam contê-las.
        error_log('Falha interna na autenticação. Verifique a migração e o banco de login.');
    } finally {
        if ($link) mysqli_close($link);
    }
}
if (!$usuario) {
    encerrarSessaoSob();
    echo '<script>alert("Login ou senha inválidos. Tente novamente."); window.location.href="index.php";</script>';
    exit;
}
session_regenerate_id(true);
$_SESSION = array(
    'autenticado_em' => time(),
    'ultima_atividade' => time(),
    'user_id' => (int)$usuario['id'],
    'banco' => $usuario['cookie'],
    'login' => $usuario['login'],
    'data_expira' => $usuario['log_fim'],
);
header('Location: geral.php', true, 303);
exit;
