<?php
function caminhoSessaoSob()
{
    $raiz = str_replace('\\', '/', dirname(__DIR__));
    $arquivo = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($arquivo, $raiz . '/') === 0) {
        $relativo = substr($arquivo, strlen($raiz));
        if ($relativo !== '' && substr($script, -strlen($relativo)) === $relativo) {
            return rtrim(substr($script, 0, -strlen($relativo)), '/') . '/';
        }
    }
    return '/';
}

function iniciarSessaoSob()
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $seguro = getenv('SESSION_COOKIE_SECURE') === '1'
        || (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (int)($_SERVER['SERVER_PORT'] ?? 0) === 443;
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.gc_maxlifetime', '28800');
    session_set_cookie_params(array(
        'lifetime' => 0, 'path' => caminhoSessaoSob(),
        'secure' => $seguro, 'httponly' => true, 'samesite' => 'Lax',
    ));
    if (!session_start()) {
        throw new RuntimeException('Não foi possível iniciar a sessão.');
    }
}

function encerrarSessaoSob()
{
    $_SESSION = array();
    if (session_status() !== PHP_SESSION_ACTIVE) return;
    if (ini_get('session.use_cookies')) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', array(
            'expires' => time() - 42000, 'path' => $cookie['path'],
            'domain' => $cookie['domain'], 'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'], 'samesite' => $cookie['samesite'] ?? 'Lax',
        ));
    }
    session_destroy();
}

function sessaoSobValida(array $sessao, $agora)
{
    $inicio = $sessao['autenticado_em'] ?? null;
    $atividade = $sessao['ultima_atividade'] ?? null;
    return !empty($sessao['login']) && !empty($sessao['banco'])
        && is_int($inicio) && is_int($atividade)
        && $inicio <= $atividade && $atividade <= $agora
        && $agora - $inicio < 28800 && $agora - $atividade < 1800;
}

function exigirSessaoSob($json = false)
{
    iniciarSessaoSob();
    $agora = time();
    if (!sessaoSobValida($_SESSION, $agora)) {
        encerrarSessaoSob();
        header('Cache-Control: no-store');
        if ($json) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('erro' => 'A sessão expirou. Entre novamente.'));
        } else {
            header('Location: ' . caminhoSessaoSob() . 'index.php', true, 303);
        }
        exit;
    }
    $_SESSION['ultima_atividade'] = $agora;
    header('Cache-Control: no-store');
}
