<?php
require __DIR__ . '/../../_config.php';
$id_lote = filter_input(INPUT_GET, 'id_lote', FILTER_VALIDATE_INT);
if (!$id_lote || $id_lote < 1) {
    http_response_code(400);
    exit('Lote inválido.');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../geral.php?pg=te&id_lote=' . $id_lote);
    exit;
}
$nome = isset($_POST['receptora']) && is_string($_POST['receptora']) ? trim($_POST['receptora']) : '';
$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$erro = '';
if (empty($_SESSION['receptora_csrf']) || !hash_equals($_SESSION['receptora_csrf'], $token)) {
    $erro = 'Sua sessão expirou. Atualize a página e tente novamente.';
} elseif ($nome === '' || !preg_match('//u', $nome) || preg_match_all('/./us', $nome) > 50) {
    $erro = 'Informe um nome válido com até 50 caracteres.';
} else {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    try {
        mysqli_set_charset($link, 'utf8mb4');
        mysqli_begin_transaction($link);
        // Serializa inclusões no mesmo lote e confirma sua existência.
        $lote = mysqli_query($link, 'SELECT id FROM transplante WHERE id = ' . $id_lote . ' FOR UPDATE');
        if (!mysqli_num_rows($lote)) {
            throw new DomainException('Lote não encontrado.');
        }
        $stmt = mysqli_prepare($link, 'INSERT INTO receptora (nome, ativo) VALUES (?, 1) ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)');
        mysqli_stmt_bind_param($stmt, 's', $nome);
        mysqli_stmt_execute($stmt);
        $idReceptora = mysqli_insert_id($link);
        mysqli_stmt_close($stmt);
        $receptora = mysqli_fetch_assoc(mysqli_query($link, 'SELECT nome, ativo FROM receptora WHERE id = ' . $idReceptora . ' FOR UPDATE'));
        if (!$receptora['ativo']) {
            throw new DomainException('Essa receptora está inativa. Ative-a no cadastro de receptoras antes de adicioná-la.');
        }
        $nome = $receptora['nome'];
        $stmt = mysqli_prepare($link, 'SELECT id FROM transplante_controle WHERE id_lote = ? AND (id_receptora = ? OR TRIM(receptora) = ?) LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'iis', $id_lote, $idReceptora, $nome);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $duplicada = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        if ($duplicada) {
            throw new DomainException('Receptora já cadastrada neste lote.');
        }
        // Mantém o nome para os relatórios legados que ainda utilizam esse campo.
        $stmt = mysqli_prepare($link, 'INSERT INTO transplante_controle (receptora, id_receptora, id_lote, ultrassom, status_nascimento, n_embrioes) VALUES (?, ?, ?, 0, 0, 0)');
        mysqli_stmt_bind_param($stmt, 'sii', $nome, $idReceptora, $id_lote);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $ranking = mysqli_fetch_assoc(mysqli_query($link, 'SELECT COUNT(*) AS qtd, SUM(ultrassom = 1) AS ultrassom, SUM(status_nascimento = 1) AS nascimento FROM transplante_controle WHERE id_lote = ' . $id_lote));
        $qtd = (int) $ranking['qtd'];
        $ultrassom = $ranking['ultrassom'] * 100 / $qtd;
        $nascimento = $ranking['nascimento'] * 100 / $qtd;
        $stmt = mysqli_prepare($link, 'UPDATE lotes_reproducao SET ultrassom = ?, crias = ?, femeas = ? WHERE id_lote = ? AND tipo = 2');
        mysqli_stmt_bind_param($stmt, 'ddii', $ultrassom, $nascimento, $qtd, $id_lote);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_commit($link);
    } catch (Exception $e) {
        mysqli_rollback($link);
        $erro = $e instanceof DomainException ? $e->getMessage() : 'Não foi possível adicionar a receptora. Verifique as migrações do banco e tente novamente.';
    }
    DBClose($link);
}
$_SESSION['te_receptora_flash'] = array('erro' => $erro, 'nome' => $erro !== '' ? $nome : '');
header('Location: ../../geral.php?pg=te&id_lote=' . $id_lote, true, 303);
exit;
