<?php
require_once __DIR__ . '/includes/sessao.php';
iniciarSessaoSob();
header('Cache-Control: no-store');
require __DIR__ . '/mysqli/environment.php';
require __DIR__ . '/includes/autenticacao.php';
require __DIR__ . '/includes/limite_login.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: index.php', true, 303);
    exit;
}
$login = $_POST['login'] ?? null;
$senha = $_POST['senha'] ?? null;
$usuario = false;
$segundosBloqueio = 0;
$link = null;
if (is_string($login) && is_string($senha) && $login !== '' && $senha !== '') {
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $link = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        mysqli_set_charset($link, DB_CHARSET);
        // Não confiar em X-Forwarded-For enviado pelo cliente.
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
        if (filter_var($ip, FILTER_VALIDATE_IP)) $ip = bin2hex(inet_pton($ip));
        $usuario = autenticarComLimite($link, $login, $senha, $ip, function ($db, $conta, $segredo) {
            return autenticarUsuario($db, $conta, $segredo, getenv('AUTH_ALLOW_LEGACY_PASSWORDS') !== '0');
        }, $segundosBloqueio);
    } catch (Throwable $erro) {
        // Nunca registrar credenciais, SQL ou mensagens que possam contê-las.
        error_log('Falha interna na autenticação. Verifique as migrações e o banco de login.');
    } finally {
        if ($link) mysqli_close($link);
    }
}
if (!$usuario) {
    encerrarSessaoSob();
    $mensagem = 'Login ou senha inválidos. Tente novamente.';
    if ($segundosBloqueio > 0) {
        http_response_code(429);
        header('Retry-After: ' . (int)$segundosBloqueio);
        $minutos = (int)ceil($segundosBloqueio / 60);
        $mensagem = 'Acesso temporariamente bloqueado por excesso de tentativas. Tente novamente em '
            . $minutos . ($minutos === 1 ? ' minuto.' : ' minutos.');
    }
    echo '<script>alert(' . json_encode($mensagem, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
        . '); window.location.href="index.php";</script>';
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
