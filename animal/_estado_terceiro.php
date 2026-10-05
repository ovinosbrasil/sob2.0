<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/estado_terceiro.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$id = filter_var($_POST['id_animal'] ?? null, FILTER_VALIDATE_INT);
$acao = $_POST['acao'] ?? '';
$token = $_POST['csrf_token'] ?? '';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405); header('Allow: POST');
    echo json_encode(array('erro' => 'Utilize a confirmação da ação.')); exit;
}
if (!is_string($token) || empty($_SESSION['exclusao_terceiro_csrf']) || !hash_equals($_SESSION['exclusao_terceiro_csrf'], $token)) {
    http_response_code(403); echo json_encode(array('erro' => 'Sua sessão expirou. Atualize a página.')); exit;
}
if (!$id || $id < 1 || !in_array($acao, array('ativar', 'inativar'), true)) {
    http_response_code(400); echo json_encode(array('erro' => 'Ação ou animal inválido.')); exit;
}
$link = DBConnect();
try {
    $ativo = $acao === 'ativar' ? 1 : 0;
    if (!definirEstadoTerceiro($link, $id, $ativo)) {
        http_response_code(404); echo json_encode(array('erro' => 'Animal não encontrado.'));
    } else { echo json_encode(array('ativo' => (bool)$ativo)); }
} catch (Throwable $erro) {
    http_response_code(503); echo json_encode(array('erro' => 'Não foi possível atualizar o cadastro. Tente novamente.'));
} finally { DBClose($link); }
