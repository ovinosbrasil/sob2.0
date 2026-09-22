<?php
require __DIR__ . '/../_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../geral.php?pg=lista_receptoras');
    exit;
}

$nome = isset($_POST['nome']) && is_string($_POST['nome']) ? trim($_POST['nome']) : '';
$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$erro = '';
if (empty($_SESSION['receptora_csrf']) || !hash_equals($_SESSION['receptora_csrf'], $token)) {
    $erro = 'Sua sessão expirou. Atualize a página e tente novamente.';
} elseif ($nome === '') {
    $erro = 'Informe o nome da receptora.';
} elseif (!preg_match('//u', $nome) || preg_match_all('/./us', $nome) > 50) {
    $erro = 'O nome deve conter até 50 caracteres válidos.';
} else {
    $link = DBConnect();
    try {
        if (!mysqli_set_charset($link, 'utf8mb4')) {
            throw new RuntimeException('Falha ao configurar conexão.');
        }
        $stmt = mysqli_prepare($link, 'INSERT INTO receptora (nome, ativo) VALUES (?, 1)');
        if (!$stmt) {
            throw new RuntimeException('Falha ao preparar cadastro.');
        }
        mysqli_stmt_bind_param($stmt, 's', $nome);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Falha ao cadastrar.', mysqli_stmt_errno($stmt));
        }
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        $erro = (int) $e->getCode() === 1062
            ? 'Já existe uma receptora com esse nome.'
            : 'Não foi possível cadastrar a receptora. Verifique se a tabela receptora foi criada e tente novamente.';
    }
    DBClose($link);
}

$_SESSION['receptora_flash'] = array(
    'erro' => $erro,
    'nome' => $erro !== '' ? $nome : '',
);
header('Location: ../geral.php?pg=lista_receptoras', true, 303);
exit;
