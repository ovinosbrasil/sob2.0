<?php
require __DIR__ . '/../_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../geral.php?pg=lista_receptoras');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$pagina = max(1, (int) filter_input(INPUT_POST, 'pag', FILTER_VALIDATE_INT));
$ativo = isset($_POST['ativo']) ? $_POST['ativo'] : null;
$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$busca = isset($_POST['busca']) && is_string($_POST['busca']) ? trim($_POST['busca']) : '';
$erro = '';
if (empty($_SESSION['receptora_csrf']) || !hash_equals($_SESSION['receptora_csrf'], $token)) {
    $erro = 'Sua sessão expirou. Atualize a página e tente novamente.';
} elseif (!$id || $id < 1 || !in_array($ativo, array('0', '1'), true)) {
    $erro = 'Receptora ou status inválido.';
} else {
    $link = DBConnect();
    try {
        $stmt = mysqli_prepare($link, 'UPDATE receptora SET ativo = ? WHERE id = ?');
        if (!$stmt) {
            throw new RuntimeException('Falha ao preparar atualização.');
        }
        $ativo = (int) $ativo;
        mysqli_stmt_bind_param($stmt, 'ii', $ativo, $id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Falha ao atualizar.');
        }
        $alteradas = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        if ($alteradas === 0) {
            $resultado = mysqli_query($link, 'SELECT id FROM receptora WHERE id = ' . (int) $id);
            if (!$resultado || !mysqli_num_rows($resultado)) {
                $erro = 'Receptora não encontrada.';
            }
        }
    } catch (Exception $e) {
        $erro = 'Não foi possível atualizar o status. Tente novamente.';
    }
    DBClose($link);
}

$_SESSION['receptora_flash'] = array(
    'erro' => $erro,
    'nome' => '',
    'sucesso' => 'Status da receptora atualizado com sucesso.',
);
header('Location: ../geral.php?pg=lista_receptoras&pag=' . $pagina . '&busca=' . rawurlencode($busca), true, 303);
exit;
