<?php
require_once __DIR__ . '/../../_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../geral.php?pg=semen');
    exit;
}

$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$nomeMacho = trim((string)($_POST['macho'] ?? ''));
$idMacho = filter_input(INPUT_POST, 'macho_id', FILTER_VALIDATE_INT);
$origem = trim((string)($_POST['macho_origem'] ?? ''));
$dataInformada = trim((string)($_POST['data'] ?? ''));
$quantidade = filter_input(INPUT_POST, 'qtd', FILTER_VALIDATE_INT);
$botijao = trim((string)($_POST['botijao'] ?? ''));
$palheta = trim((string)($_POST['palheta'] ?? ''));
$qualidade = trim((string)($_POST['qualidade'] ?? ''));
$erro = '';

$data = DateTimeImmutable::createFromFormat('!d/m/Y', $dataInformada);
if (empty($_SESSION['semen_csrf']) || !hash_equals($_SESSION['semen_csrf'], $token)) {
    $erro = 'Sua sessão expirou. Atualize a página e tente novamente.';
} elseif (!$idMacho || $idMacho < 1 || !in_array($origem, array('rebanho', 'terceiros'), true)) {
    $erro = 'Selecione um macho na lista de pesquisa.';
} elseif (!$data || $data->format('d/m/Y') !== $dataInformada) {
    $erro = 'Informe uma data válida.';
} elseif (!$quantidade || $quantidade < 1) {
    $erro = 'Informe uma quantidade maior que zero.';
} elseif (mb_strlen($botijao, 'UTF-8') > 100 || mb_strlen($palheta, 'UTF-8') > 30 || mb_strlen($qualidade, 'UTF-8') > 30) {
    $erro = 'Revise o tamanho dos dados informados.';
} else {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    try {
        mysqli_set_charset($link, 'utf8mb4');
        $tabela = $origem === 'terceiros' ? 'terceiros' : 'animais';
        $filtroAtivo = $tabela === 'terceiros' ? ' AND ativo = 1' : '';
        $stmt = mysqli_prepare($link, "SELECT id FROM $tabela WHERE id = ? AND sexo = 'Macho'$filtroAtivo LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $idMacho);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $machoExiste = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        if (!$machoExiste) {
            throw new DomainException('Macho não encontrado. Faça a pesquisa novamente.');
        }

        $dataBanco = $data->format('Y-m-d');
        $terceiro = $origem === 'terceiros' ? 1 : 0;
        $vazio = '';
        $stmt = mysqli_prepare($link, 'INSERT INTO semen (id_animal, data, qtd, vigor, partida, motilidade, congelado, botijao, terceiro, palheta, qualidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'isisssssiss', $idMacho, $dataBanco, $quantidade, $vazio, $vazio, $vazio, $vazio, $botijao, $terceiro, $palheta, $qualidade);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        $erro = $e instanceof DomainException ? $e->getMessage() : 'Não foi possível adicionar o sêmen. Tente novamente.';
    }
    DBClose($link);
}

if ($erro !== '') {
    $_SESSION['semen_flash'] = array(
        'erro' => $erro,
        'macho' => $nomeMacho,
        'macho_id' => (int)$idMacho,
        'macho_origem' => $origem,
        'data' => $dataInformada,
        'qtd' => (string)($_POST['qtd'] ?? ''),
        'botijao' => $botijao,
        'palheta' => $palheta,
        'qualidade' => $qualidade,
    );
}
$_SESSION['alerta_semen'] = $erro !== ''
    ? array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>$erro)
    : array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Sêmen cadastrado com sucesso.');
header('Location: ../../geral.php?pg=semen', true, 303);
exit;
