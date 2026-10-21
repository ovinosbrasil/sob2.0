<?php
function tokenCsrfSob()
{
    if (empty($_SESSION['_csrf_sob']) || !is_string($_SESSION['_csrf_sob'])) {
        $_SESSION['_csrf_sob'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_sob'];
}
function campoCsrfSob()
{
    return '<input type="hidden" name="_csrf_sob" value="'
        . htmlspecialchars(tokenCsrfSob(), ENT_QUOTES, 'UTF-8') . '">';
}
function statusPostCsrfSob($metodo, array $post, array $sessao)
{
    if ($metodo !== 'POST') return 405;
    $token = $post['_csrf_sob'] ?? null;
    $esperado = $sessao['_csrf_sob'] ?? null;
    if (!is_string($token) || !is_string($esperado) || $esperado === '' || !hash_equals($esperado, $token)) return 403;
    return 200;
}
function exigirPostCsrfSob($json = false)
{
    $status = statusPostCsrfSob($_SERVER['REQUEST_METHOD'] ?? '', $_POST, $_SESSION ?? array());
    if ($status === 200) return;
    http_response_code($status);
    header('Cache-Control: no-store');
    if ($status === 405) header('Allow: POST');
    $mensagem = $status === 405 ? 'Utilize o formulário para realizar esta operação.'
        : 'Não foi possível validar a solicitação. Recarregue a página e tente novamente.';
    if ($json) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('sucesso'=>false, 'tipo'=>'warning', 'mensagem'=>$mensagem), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="pt-BR"><meta charset="utf-8"><title>Solicitação não validada</title><p>'
            . htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') . '</p><p>Volte à página anterior e recarregue o formulário.</p></html>';
    }
    exit;
}
