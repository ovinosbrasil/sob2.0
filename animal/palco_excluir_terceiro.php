<?php
require_once __DIR__ . '/../_config.php';
$id_animal = filter_var($_GET['id_animal'] ?? null, FILTER_VALIDATE_INT);
if (!$id_animal || $id_animal < 1) {
    http_response_code(400);
    exit;
}
$terceiro = DBRead('terceiros', "WHERE id = $id_animal", 'nome');
if (!$terceiro) {
    http_response_code(404);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(array('nome' => $terceiro[0]['nome']), JSON_UNESCAPED_UNICODE);
