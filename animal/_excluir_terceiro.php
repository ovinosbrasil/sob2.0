<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_dependencias_terceiro.php';
header('Content-Type: application/json; charset=utf-8');
function responderExclusaoTerceiro($status, $dados) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
}
$id = filter_var($_POST['id_animal'] ?? null, FILTER_VALIDATE_INT);
$token = $_POST['csrf_token'] ?? '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responderExclusaoTerceiro(405, array('erro' => 'Use a confirmação para excluir o cadastro.'));
    exit;
}
if (!is_string($token) || empty($_SESSION['exclusao_terceiro_csrf']) || !hash_equals($_SESSION['exclusao_terceiro_csrf'], $token)) {
    responderExclusaoTerceiro(403, array('erro' => 'Sua sessão expirou. Atualize a página.'));
    exit;
}
if (!$id || $id < 1) {
    responderExclusaoTerceiro(400, array('erro' => 'Animal inválido.'));
    exit;
}
$link = DBConnect();
$bloqueado = false;
try {
    $esquema = esquemaExclusaoTerceiro($link);
    $tabelas = array();
    foreach ($esquema as $tabela => $colunas) { $tabelas[] = "`$tabela` " . ($tabela === 'terceiros' ? 'WRITE' : 'READ'); }
    consultarExclusaoTerceiro($link, 'LOCK TABLES ' . implode(', ', $tabelas));
    $bloqueado = true;
    $resultado = consultarExclusaoTerceiro($link, 'SELECT nome FROM terceiros WHERE id = ' . (int)$id);
    $animal = mysqli_fetch_assoc($resultado);
    if (!$animal) {
        responderExclusaoTerceiro(404, array('erro' => 'Animal não encontrado.'));
    } else {
        $vinculos = dependenciasExclusaoTerceiro($link, $esquema, $id, $animal['nome']);
        if ($vinculos) {
            responderExclusaoTerceiro(409, array('erro' => 'Exclusão bloqueada: este animal possui vínculos. Preserve o cadastro para manter o histórico.', 'vinculos' => $vinculos));
        } else {
            consultarExclusaoTerceiro($link, 'DELETE FROM terceiros WHERE id = ' . (int)$id);
            responderExclusaoTerceiro(200, array('excluido' => true));
        }
    }
} catch (Throwable $erro) {
    responderExclusaoTerceiro(503, array('erro' => 'Não foi possível verificar e excluir o cadastro com segurança. Tente novamente.'));
} finally {
    if ($bloqueado) { mysqli_query($link, 'UNLOCK TABLES'); }
    DBClose($link);
}
