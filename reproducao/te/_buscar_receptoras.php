<?php
require __DIR__ . '/../../_config.php';
header('Content-Type: application/json; charset=UTF-8');
$termo = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
if ($termo === '' || strlen($termo) > 200) {
    echo '[]';
    exit;
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();
try {
    mysqli_set_charset($link, 'utf8mb4');
    $busca = '%' . strtr($termo, array('!' => '!!', '%' => '!%', '_' => '!_')) . '%';
    $stmt = mysqli_prepare($link, "SELECT nome FROM receptora WHERE ativo = 1 AND nome LIKE ? ESCAPE '!' ORDER BY nome LIMIT 20");
    mysqli_stmt_bind_param($stmt, 's', $busca);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nome);
    $nomes = array();
    while (mysqli_stmt_fetch($stmt)) {
        $nomes[] = $nome;
    }
    echo json_encode($nomes);
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('erro' => 'Não foi possível buscar receptoras.'));
}
DBClose($link);
