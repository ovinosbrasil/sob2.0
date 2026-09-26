<?php
require __DIR__ . '/../_config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../geral.php?pg=lista_receptoras');
    exit;
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$pagina = max(1, (int)filter_input(INPUT_POST, 'pag', FILTER_VALIDATE_INT));
$porPagina = filter_var($_POST['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) { $porPagina = 10; }
$busca = isset($_POST['busca']) && is_string($_POST['busca']) ? trim($_POST['busca']) : '';
$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$erro = '';
if (empty($_SESSION['receptora_csrf']) || !hash_equals($_SESSION['receptora_csrf'], $token)) {
    $erro = 'Sua sessão expirou. Atualize a página e tente novamente.';
} elseif (!$id || $id < 1) {
    $erro = 'Receptora inválida.';
} else {
    $link = DBConnect();
    try {
        // Não apaga histórico nem deixa referências sem cadastro.
        $stmt = mysqli_prepare($link, 'DELETE r FROM receptora r
            WHERE r.id = ?
            AND NOT EXISTS (SELECT 1 FROM transplante_controle t WHERE t.id_receptora = r.id OR t.receptora = r.nome)
            AND NOT EXISTS (SELECT 1 FROM animais a WHERE a.id_receptora = r.id OR a.receptora = r.nome)
            AND NOT EXISTS (SELECT 1 FROM crias c WHERE c.id_receptora = r.id OR c.receptora = r.nome)');
        if (!$stmt) { throw new RuntimeException('Falha ao preparar exclusão.'); }
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (!mysqli_stmt_execute($stmt)) { throw new RuntimeException('Falha ao excluir.'); }
        if (mysqli_stmt_affected_rows($stmt) === 0) {
            $erro = 'A receptora não foi excluída: ela possui vínculos ou já não existe. Se houver histórico, utilize o interruptor para inativá-la.';
        }
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        $erro = 'Não foi possível excluir a receptora. Tente novamente.';
    }
    DBClose($link);
}
$_SESSION['receptora_flash'] = array('erro' => $erro, 'nome' => '', 'sucesso' => 'Receptora excluída com sucesso.');
header('Location: ../geral.php?pg=lista_receptoras&pag=' . $pagina . '&por_pagina=' . $porPagina . '&busca=' . rawurlencode($busca), true, 303);
exit;
