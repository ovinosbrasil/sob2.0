<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/importacao/atualizar_animal.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') throw new RuntimeException('Requisição inválida.');
    $token = $_POST['token_previa'] ?? '';
    if (!is_string($token) || empty($_SESSION['token_previa_rebanho']) || !hash_equals($_SESSION['token_previa_rebanho'], $token)) throw new RuntimeException('Sessão expirada. Recarregue a página.');
    $previa = $_SESSION['previa_rebanho'] ?? array();
    validarContextoPrevia($previa, $_POST['envio'] ?? '', DB_DATABASE, $_SESSION['login']);
    $lote = atualizacoesEmLotePrevia($previa);
    $total = count($lote);
    $paginas = max(1, (int)ceil($total/10));
    $pagina = max(1,min($paginas,(int)($_POST['pagina'] ?? 1)));
    echo json_encode(array('itens'=>array_slice($lote,($pagina-1)*10,10),'total'=>$total,'pagina'=>$pagina,'paginas'=>$paginas), JSON_INVALID_UTF8_SUBSTITUTE);
} catch (RuntimeException $erro) {
    http_response_code(400);
    echo json_encode(array('erro'=>$erro->getMessage()));
}
