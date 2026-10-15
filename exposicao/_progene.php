<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/participantes.php';
header('Content-Type: application/json; charset=UTF-8');
function responderProgene($status, array $dados) {
    http_response_code($status);
    echo json_encode($dados, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    responderProgene(405, array('sucesso'=>false, 'mensagem'=>'Método inválido.'));
}
$token = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : '';
if (empty($_SESSION['exposicao_progene_csrf']) || !hash_equals($_SESSION['exposicao_progene_csrf'], $token)) {
    responderProgene(403, array('sucesso'=>false, 'mensagem'=>'Sua sessão expirou. Atualize a página e tente novamente.'));
}
$idEvento = filter_var($_POST['id_evento'] ?? 0, FILTER_VALIDATE_INT);
$idAnimal = filter_var($_POST['id_animal'] ?? 0, FILTER_VALIDATE_INT);
$ativo = $_POST['progene'] ?? null;
if (!$idEvento || $idEvento < 1 || !$idAnimal || $idAnimal < 1 || !in_array($ativo, array('0','1'), true)) {
    responderProgene(400, array('sucesso'=>false, 'mensagem'=>'Seleção de progênie inválida.'));
}
try {
    $estado = definirProgeneExposicao($idEvento, $idAnimal, (int)$ativo);
    responderProgene(200, array('sucesso'=>true, 'progene'=>$estado, 'mensagem'=>$estado ? 'Animal selecionado para a progênie.' : 'Animal desmarcado da progênie.'));
} catch (DomainException $erro) {
    responderProgene(404, array('sucesso'=>false, 'mensagem'=>$erro->getMessage()));
} catch (Throwable $erro) {
    error_log('Falha ao selecionar animal para progênie: ' . $erro->getMessage());
    responderProgene(500, array('sucesso'=>false, 'mensagem'=>'Não foi possível atualizar a seleção. Tente novamente.'));
}
