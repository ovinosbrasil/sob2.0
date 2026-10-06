<?php
require_once __DIR__ . '/../../_config.php';

$idSemen = filter_input(INPUT_GET, 'id_embriao', FILTER_VALIDATE_INT);
$quantidade = filter_input(INPUT_POST, 'qtd', FILTER_VALIDATE_INT);
$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$idSemen || $idSemen < 1) {
    $erro = 'Registro de sêmen inválido.';
} elseif (empty($_SESSION['semen_csrf']) || !hash_equals($_SESSION['semen_csrf'], $token)) {
    $erro = 'Sua sessão expirou. Atualize a página e tente novamente.';
} elseif ($quantidade === false || $quantidade < 0) {
    $erro = 'Informe uma quantidade válida.';
} else {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    try {
        mysqli_set_charset($link, 'utf8mb4');
        $stmt = mysqli_prepare($link, 'UPDATE semen SET qtd = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'ii', $quantidade, $idSemen);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) < 0) {
            throw new RuntimeException('Falha ao alterar quantidade.');
        }
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        $erro = 'Não foi possível alterar a quantidade. Tente novamente.';
    }
    DBClose($link);
}
if ($erro !== '') {
    $_SESSION['semen_flash'] = array('erro' => $erro);
}
$_SESSION['alerta_semen'] = $erro !== ''
    ? array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>$erro)
    : array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Quantidade de sêmen alterada com sucesso.');
header('Location: ../../geral.php?pg=semen', true, 303);
exit;
