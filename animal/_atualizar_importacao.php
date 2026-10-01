<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/importacao/atualizar_animal.php';
date_default_timezone_set('America/Sao_Paulo');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}
try {
    $token = $_POST['token_previa'] ?? '';
    if (!is_string($token) || empty($_SESSION['token_previa_rebanho']) || !hash_equals($_SESSION['token_previa_rebanho'], $token)) {
        throw new RuntimeException('A sessão de atualização expirou. Recarregue a página.');
    }
    $previa = $_SESSION['previa_rebanho'] ?? array();
    validarContextoPrevia($previa, $_POST['envio'] ?? '', DB_DATABASE, $_SESSION['login']);
    if (($_POST['acao'] ?? '') === 'todos') {
        $lote = atualizacoesEmLotePrevia($previa);
    } else {
        $linha = filter_var($_POST['linha'] ?? 0, FILTER_VALIDATE_INT);
        $id = filter_var($_POST['id_animal'] ?? 0, FILTER_VALIDATE_INT);
        list($anterior, $novos) = selecionarAtualizacaoPrevia(
            $previa, $_POST['envio'] ?? '', $linha, $id, DB_DATABASE, $_SESSION['login']
        );
        $lote = array(array('linha' => $linha, 'anterior' => $anterior, 'novos' => $novos));
    }
    if (isset($_POST['exclusoes_campos'])) {
        if (($_POST['acao'] ?? '') !== 'todos' || !is_string($_POST['exclusoes_campos'])) throw new RuntimeException('Seleção inválida.');
        $exclusoes = json_decode($_POST['exclusoes_campos'], true);
        if (!is_array($exclusoes)) throw new RuntimeException('Seleção inválida.');
        $globaisJson = $_POST['exclusoes_globais'] ?? '[]';
        if (!is_string($globaisJson)) throw new RuntimeException('Seleção inválida.');
        $globais = json_decode($globaisJson, true);
        if (!is_array($globais)) throw new RuntimeException('Seleção inválida.');
        $lote = excluirCamposLotePrevia($lote, $exclusoes, $globais);
    }
    if (($_POST['selecionar_campos'] ?? '') === '1') {
        $selecoes = $_POST['campos'] ?? array();
        if (!is_array($selecoes)) { throw new RuntimeException('Seleção de campos inválida.'); }
        $loteSelecionado = array();
        foreach ($lote as $item) {
            $campos = ($_POST['acao'] ?? '') === 'todos' ? ($selecoes[$item['linha']] ?? array()) : $selecoes;
            if (($_POST['acao'] ?? '') === 'todos' && $campos === array()) { continue; }
            $novosSelecionados = selecionarCamposAtualizacaoPrevia($item['novos'], $campos);
            $diferentes = camposDiferentesPrevia($item['anterior'], $item['novos']);
            $item['completa'] = !array_diff_key($diferentes, $novosSelecionados);
            $item['novos'] = $novosSelecionados;
            $loteSelecionado[] = $item;
        }
        if (!$loteSelecionado) { throw new RuntimeException('Selecione pelo menos um campo para atualizar.'); }
        $lote = $loteSelecionado;
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $link = DBConnect();
    try { atualizarLoteDaPrevia($link, $lote); }
    finally { DBClose($link); }
    foreach ($lote as $item) {
        if ($item['completa'] ?? true) {
            $_SESSION['previa_rebanho']['atualizados'][$item['linha']] = (int)$item['anterior']['id'];
        }
    }
    $_SESSION['mensagem_previa_rebanho'] = array('tipo' => 'success', 'texto' => count($lote) . ' animal(is) atualizado(s) com sucesso.');
} catch (Throwable $erro) {
    if ($erro instanceof mysqli_sql_exception || !($erro instanceof RuntimeException)) {
        error_log('Falha na atualização pela importação de rebanho: ' . $erro->getMessage());
    }
    $mensagem = $erro instanceof RuntimeException && !($erro instanceof mysqli_sql_exception)
        ? $erro->getMessage() : 'Não foi possível atualizar o animal. Nenhuma alteração foi aplicada. Tente novamente.';
    $_SESSION['mensagem_previa_rebanho'] = array('tipo' => 'danger', 'texto' => $mensagem);
}
header('Location: ../geral.php?pg=atualizar_rebanho', true, 303);
exit;
