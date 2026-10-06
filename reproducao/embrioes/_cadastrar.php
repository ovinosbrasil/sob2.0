<?php
require_once __DIR__ . '/../../_config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../../geral.php?pg=embrioes'); exit; }

$token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$nomeMacho = trim((string)($_POST['macho'] ?? ''));
$nomeFemea = trim((string)($_POST['femea'] ?? ''));
$idMacho = filter_input(INPUT_POST, 'macho_id', FILTER_VALIDATE_INT);
$idFemea = filter_input(INPUT_POST, 'femea_id', FILTER_VALIDATE_INT);
$origemMacho = trim((string)($_POST['macho_origem'] ?? ''));
$origemFemea = trim((string)($_POST['femea_origem'] ?? ''));
$dataInformada = trim((string)($_POST['data'] ?? ''));
$quantidade = filter_input(INPUT_POST, 'qtd', FILTER_VALIDATE_INT);
$botijao = trim((string)($_POST['botijao'] ?? ''));
$palheta = trim((string)($_POST['palheta'] ?? ''));
$qualidade = trim((string)($_POST['qualidade'] ?? ''));
$data = DateTimeImmutable::createFromFormat('!d/m/Y', $dataInformada);
$erro = '';

if (empty($_SESSION['embriao_csrf']) || !hash_equals($_SESSION['embriao_csrf'], $token)) {
    $erro = 'Sua sessão expirou. Atualize a página e tente novamente.';
} elseif (!$idMacho || !in_array($origemMacho, array('rebanho', 'terceiros'), true)) {
    $erro = 'Selecione um macho na lista de pesquisa.';
} elseif (!$idFemea || !in_array($origemFemea, array('rebanho', 'terceiros'), true)) {
    $erro = 'Selecione uma fêmea na lista de pesquisa.';
} elseif (!$data || $data->format('d/m/Y') !== $dataInformada) {
    $erro = 'Informe uma data válida.';
} elseif (!$quantidade || $quantidade < 1) {
    $erro = 'Informe uma quantidade maior que zero.';
} elseif ($botijao === '') {
    $erro = 'Informe o botijão.';
} elseif (mb_strlen($botijao, 'UTF-8') > 30 || mb_strlen($palheta, 'UTF-8') > 30 || mb_strlen($qualidade, 'UTF-8') > 30) {
    $erro = 'Revise o tamanho dos dados informados.';
} else {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    try {
        mysqli_set_charset($link, 'utf8mb4');
        foreach (array(array($idMacho, $origemMacho, 'Macho'), array($idFemea, $origemFemea, 'Fêmea')) as $animal) {
            $tabela = $animal[1] === 'terceiros' ? 'terceiros' : 'animais';
            $filtroAtivo = $tabela === 'terceiros' ? ' AND ativo = 1' : '';
            $stmt = mysqli_prepare($link, "SELECT sexo FROM $tabela WHERE id = ?$filtroAtivo LIMIT 1");
            mysqli_stmt_bind_param($stmt, 'i', $animal[0]);
            mysqli_stmt_execute($stmt);
            $registroAnimal = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            $sexoCorreto = $registroAnimal && ($animal[2] === 'Macho'
                ? $registroAnimal['sexo'] === 'Macho'
                : strncmp((string)$registroAnimal['sexo'], 'F', 1) === 0);
            if (!$sexoCorreto) {
                $mensagemSexo = $animal[2] === 'Macho' ? 'Macho não encontrado.' : 'Fêmea não encontrada.';
                throw new DomainException($mensagemSexo . ' Faça a pesquisa novamente.');
            }
        }
        $dataBanco = $data->format('Y-m-d');
        $terceiroMacho = $origemMacho === 'terceiros' ? 1 : 0;
        $terceiroFemea = $origemFemea === 'terceiros' ? 1 : 0;
        $stmt = mysqli_prepare($link, 'INSERT INTO embriao (pai, mae, qtd, data, terceiro, terceiro_mae, botijao, palheta, qualidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'iiisiisss', $idMacho, $idFemea, $quantidade, $dataBanco, $terceiroMacho, $terceiroFemea, $botijao, $palheta, $qualidade);
        mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        $erro = $e instanceof DomainException ? $e->getMessage() : 'Não foi possível adicionar os embriões. Tente novamente.';
    }
    DBClose($link);
}
if ($erro !== '') {
    $_SESSION['embriao_flash'] = array('erro'=>$erro,'macho'=>$nomeMacho,'macho_id'=>(int)$idMacho,'macho_origem'=>$origemMacho,'femea'=>$nomeFemea,'femea_id'=>(int)$idFemea,'femea_origem'=>$origemFemea,'data'=>$dataInformada,'qtd'=>(string)($_POST['qtd'] ?? ''),'botijao'=>$botijao,'palheta'=>$palheta,'qualidade'=>$qualidade);
}
$_SESSION['alerta_embrioes'] = $erro !== ''
    ? array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>$erro)
    : array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Embriões cadastrados com sucesso.');
header('Location: ../../geral.php?pg=embrioes', true, 303);
exit;
