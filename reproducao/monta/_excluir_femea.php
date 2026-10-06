<?php
require_once __DIR__ . '/../../_config.php';

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$idControle = filter_var($_GET['id_controle'] ?? 0, FILTER_VALIDATE_INT);

if (!$idLote) {
    header('Location: ../../geral.php?pg=lista_monta');
    exit;
}
$idLote = (int)$idLote;

$condicao = null;
if ($idControle) {
    $condicao = "id = '" . (int)$idControle . "' AND id_monta = '$idLote'";
} else {
    // Compatibilidade com links antigos.
    $idMae = filter_var($_GET['id_mae'] ?? 0, FILTER_VALIDATE_INT);
    if ($idMae) {
        $condicao = "id_animal = '" . (int)$idMae . "' AND id_monta = '$idLote'";
    }
}
if (!$condicao || !DBRead('monta_controle', 'WHERE ' . $condicao) || !DBDelete('monta_controle', $condicao)) {
    $_SESSION['alerta_cadastro_monta'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Não foi possível excluir a fêmea do lote. Atualize a página e tente novamente.');
    header('Location: ../../geral.php?pg=monta&id_lote=' . $idLote);
    exit;
}

$controles = DBRead('monta_controle', "WHERE id_monta = '$idLote'") ?: array();
$positivos = DBRead('monta_controle', "WHERE id_monta = '$idLote' AND ultrassom = '1'") ?: array();
$nascimentos = DBRead('monta_controle', "WHERE id_monta = '$idLote' AND status_nascimento = '1'") ?: array();
$total = count($controles);

DBUpdate('lotes_reproducao', array(
    'ultrassom' => $total ? count($positivos) * 100 / $total : 0,
    'crias' => $total ? count($nascimentos) * 100 / $total : 0,
    'femeas' => $total
), "id_lote = '$idLote' AND tipo = '0'");

$_SESSION['alerta_cadastro_monta'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Fêmea excluída do lote com sucesso.');
header('Location: ../../geral.php?pg=monta&id_lote=' . $idLote);
exit;
