<?php
require_once __DIR__ . '/../_config.php';

header('Content-Type: application/json; charset=UTF-8');

$idComprador = isset($_POST['id_comprador']) ? (int)$_POST['id_comprador'] : 0;
if ($idComprador < 1) {
    http_response_code(400);
    echo json_encode(array('sucesso' => false, 'mensagem' => 'Comprador inválido.'));
    exit;
}

$comprador = DBRead('mercado', "WHERE id = '$idComprador'");
if (empty($comprador)) {
    http_response_code(404);
    echo json_encode(array('sucesso' => false, 'mensagem' => 'Comprador não encontrado.'));
    exit;
}

$vendasRelacionadas = DBRead('vendas', "WHERE comprador = '$idComprador' LIMIT 1");
if (!empty($vendasRelacionadas)) {
    echo json_encode(array(
        'sucesso' => false,
        'mensagem' => 'Este comprador não pode ser excluído porque possui venda relacionada.'
    ));
    exit;
}

DBDelete('mercado', "id = '$idComprador'");
echo json_encode(array('sucesso' => true, 'mensagem' => 'Comprador excluído com sucesso.'));
