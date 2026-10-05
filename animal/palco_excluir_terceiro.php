<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_dependencias_terceiro.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$id = filter_var($_GET['id_animal'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit; }
$link = DBConnect();
try {
    $esquema = esquemaExclusaoTerceiro($link);
    $resultado = consultarExclusaoTerceiro($link, 'SELECT nome, ativo FROM terceiros WHERE id = ' . (int)$id);
    $animal = mysqli_fetch_assoc($resultado);
    if (!$animal) { http_response_code(404); exit; }
    $vinculos = dependenciasExclusaoTerceiro($link, $esquema, $id, $animal['nome']);
    if (empty($_SESSION['exclusao_terceiro_csrf'])) { $_SESSION['exclusao_terceiro_csrf'] = bin2hex(random_bytes(32)); }
    echo json_encode(array('nome' => $animal['nome'], 'ativo' => (bool)$animal['ativo'], 'vinculos' => $vinculos, 'pode_excluir' => !$vinculos, 'csrf_token' => $_SESSION['exclusao_terceiro_csrf']), JSON_UNESCAPED_UNICODE);
} catch (Throwable $erro) {
    http_response_code(503);
    echo json_encode(array('erro' => 'Não foi possível verificar os vínculos.'));
} finally { DBClose($link); }
