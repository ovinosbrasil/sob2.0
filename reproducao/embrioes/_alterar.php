<?php
require_once __DIR__ . '/../../_config.php';
$idEmbriao = filter_input(INPUT_GET, 'id_embriao', FILTER_VALIDATE_INT);
$quantidade = filter_input(INPUT_POST, 'qtd', FILTER_VALIDATE_INT);
$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$idEmbriao) { $erro = 'Registro de embrião inválido.'; }
elseif (empty($_SESSION['embriao_csrf']) || !hash_equals($_SESSION['embriao_csrf'], $token)) { $erro = 'Sua sessão expirou. Atualize a página e tente novamente.'; }
elseif ($quantidade === false || $quantidade < 0) { $erro = 'Informe uma quantidade válida.'; }
else {
    $link = DBConnect();
    try {
        mysqli_set_charset($link, 'utf8mb4');
        $stmt = mysqli_prepare($link, 'UPDATE embriao SET qtd = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'ii', $quantidade, $idEmbriao); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    } catch (Exception $e) { $erro = 'Não foi possível alterar a quantidade. Tente novamente.'; }
    DBClose($link);
}
if ($erro !== '') { $_SESSION['embriao_flash'] = array('erro'=>$erro); }
header('Location: ../../geral.php?pg=embrioes', true, 303);
exit;
