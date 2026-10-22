<?php
require_once __DIR__ . '/../mysqli/dotenv.php';

function conexaoTermosSob()
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = mysqli_connect(valorEnvBancoSob('DB_HOST', 'localhost'), valorEnvBancoSob('DB_USER'),
        valorEnvBancoSob('DB_PASSWORD'), valorEnvBancoSob('DB_DATABASE'));
    mysqli_set_charset($db, valorEnvBancoSob('DB_CHARSET', 'latin1'));
    return $db;
}

function consultarAceiteSob($db, $id)
{
    $stmt = mysqli_prepare($db, 'SELECT termos_aceitos FROM user WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $usuario && (int)$usuario['termos_aceitos'] === 1;
}

function aceiteTermosValidoSob(array $dados, $token)
{
    return ($dados['termos_lidos'] ?? null) === '1'
        && is_string($token) && $token !== ''
        && is_string($dados['csrf_token'] ?? null)
        && hash_equals($token, $dados['csrf_token']);
}

function exigirTermosSob($json = false)
{
    $db = null;
    try {
        $db = conexaoTermosSob();
        $aceito = consultarAceiteSob($db, (int)($_SESSION['user_id'] ?? 0));
    } catch (Throwable $erro) {
        error_log('Falha ao consultar aceite dos termos. Verifique a migração 031 no banco central.');
        http_response_code(503);
        exit('Não foi possível verificar o aceite dos termos. Tente novamente mais tarde.');
    } finally {
        if ($db) mysqli_close($db);
    }
    if ($aceito) return;
    if ($json) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('erro' => 'É necessário aceitar os termos de uso.', 'url' => caminhoSessaoSob() . 'geral.php'));
        exit;
    }
    if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__DIR__ . '/../geral.php')) {
        require __DIR__ . '/termos_modal.php';
    } else {
        header('Location: ' . caminhoSessaoSob() . 'geral.php', true, 303);
    }
    exit;
}
