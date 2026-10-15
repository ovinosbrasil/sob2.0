<?php
require_once __DIR__ . '/../../_config.php';

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$idControle = filter_var($_GET['id_controle'] ?? 0, FILTER_VALIDATE_INT);

if (!$idLote) {
    header('Location: ../../geral.php?pg=lista_inseminacao');
    exit;
}
$idLote = (int)$idLote;

$condicao = null;
if ($idControle) {
    $condicao = "id = '" . (int)$idControle . "' AND id_lote = '$idLote'";
} else {
    // Compatibilidade com links antigos.
    $idMae = filter_var($_GET['id_mae'] ?? 0, FILTER_VALIDATE_INT);
    if ($idMae) {
        $condicao = "id_femea = '" . (int)$idMae . "' AND id_lote = '$idLote'";
    }
}
if (!$condicao || !DBRead('inseminacao_controle', 'WHERE ' . $condicao) || !DBDelete('inseminacao_controle', $condicao)) {
    $_SESSION['alerta_cadastro_inseminacao'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Não foi possível excluir a fêmea do lote. Atualize a página e tente novamente.');
    header('Location: ../../geral.php?pg=inseminacao&id_lote=' . $idLote);
    exit;
}

$_SESSION['alerta_cadastro_inseminacao'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Fêmea excluída do lote com sucesso.');
header('Location: ../../geral.php?pg=inseminacao&id_lote=' . $idLote);
exit;
