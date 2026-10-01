<?php
// Erros PHP pertencem ao log, nunca ao corpo da resposta JSON.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
$nivelBufferCadastro = ob_get_level();
ob_start();
register_shutdown_function(function () use ($nivelBufferCadastro) {
    $erro = error_get_last();
    if (!$erro || !in_array($erro['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) return;
    while (ob_get_level() > $nivelBufferCadastro) ob_end_clean();
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('erro' => 'O servidor não conseguiu concluir o cadastro. Reabra a confirmação e tente novamente.'));
});
date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json; charset=utf-8');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(array('erro' => 'Utilize o formulário de cadastro.'));
    exit;
}
try {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['banco']) || empty($_SESSION['login'])) {
        http_response_code(401);
        echo json_encode(array('erro' => 'A sessão expirou. Recarregue a página e entre novamente.'));
        exit;
    }
    require_once __DIR__ . '/../_config.php';
    require_once __DIR__ . '/importacao/cadastrar_animal.php';
    $token = $_POST['token_previa'] ?? '';
    if (!is_string($token) || empty($_SESSION['token_previa_rebanho']) || !hash_equals($_SESSION['token_previa_rebanho'], $token)) {
        throw new RuntimeException('A sessão expirou. Recarregue a página.');
    }
    $previa = $_SESSION['previa_rebanho'] ?? array();
    validarContextoPrevia($previa, $_POST['envio'] ?? '', DB_DATABASE, $_SESSION['login']);
    $linha = ($_POST['linha'] ?? '') === 'todos' ? 'todos' : filter_var($_POST['linha'] ?? 0, FILTER_VALIDATE_INT);
    if (!$linha || isset($previa['cadastrados'][$linha])) { throw new RuntimeException('Esta linha não está disponível para cadastro.'); }
    $acao = $_POST['acao'] ?? '';
    if (!in_array($acao, array('preparar', 'confirmar'), true)) { throw new RuntimeException('Ação inválida.'); }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    try {
        if ($acao === 'preparar') {
            $listas = consultarCadastroPrevia($link);
            $plano = planejarCadastroPrevia($previa['registros'], $linha, $listas['animais'], $listas['terceiros'], consultarRacaCadastroPrevia($link), $ignorados);
            $confirmacao = bin2hex(random_bytes(32));
            $_SESSION['previa_rebanho']['cadastro_pendente'] = array('linha' => $linha, 'envio' => $previa['envio'], 'token' => $confirmacao, 'plano' => $plano, 'ignorados' => $ignorados);
            echo json_encode(array('plano' => $plano, 'ignorados' => $ignorados, 'confirmacao' => $plano ? $confirmacao : ''), JSON_INVALID_UTF8_SUBSTITUTE);
        } else {
            $pendente = $previa['cadastro_pendente'] ?? array();
            $confirmacao = $_POST['confirmacao'] ?? '';
            if (($pendente['linha'] ?? 0) !== $linha || ($pendente['envio'] ?? '') !== $previa['envio'] || !is_string($confirmacao) || !$confirmacao || !hash_equals($pendente['token'] ?? '', $confirmacao)) {
                throw new RuntimeException('A confirmação expirou. Abra o cadastro novamente.');
            }
            if (empty($pendente['plano'])) throw new RuntimeException('Nenhum animal válido disponível para cadastro.');
            $ids = confirmarCadastroPrevia($link, $previa['registros'], $linha, $pendente['plano']);
            foreach ($pendente['plano'] as $op) {
                if (isset($op['linha'])) { $_SESSION['previa_rebanho']['cadastrados'][$op['linha']] = $ids[$op['chave']]; }
            }
            unset($_SESSION['previa_rebanho']['cadastro_pendente']);
            $quantidadeIgnorados = count($pendente['ignorados'] ?? array());
            $_SESSION['mensagem_previa_rebanho'] = array('tipo' => $quantidadeIgnorados ? 'warning' : 'success', 'texto' => count($ids) . ' cadastro(s) criado(s), com os vínculos de parentesco. ' . $quantidadeIgnorados . ' animal(is) ignorado(s) por dados inconsistentes.');
            echo json_encode(array('sucesso' => true));
        }
    } finally { DBClose($link); }
} catch (Throwable $erro) {
    http_response_code(422);
    $conhecido = $erro instanceof RuntimeException && !($erro instanceof mysqli_sql_exception);
    if (!$conhecido) { error_log('Falha no cadastro por importação: ' . $erro->getMessage()); }
    echo json_encode(array('erro' => $conhecido ? $erro->getMessage() : 'Não foi possível cadastrar. Nenhuma alteração foi aplicada. Tente novamente.'), JSON_INVALID_UTF8_SUBSTITUTE);
}
