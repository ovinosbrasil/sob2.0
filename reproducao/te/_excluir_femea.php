<?php
require_once __DIR__ . '/../../_config.php';

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$idControle = filter_var($_GET['id_controle'] ?? 0, FILTER_VALIDATE_INT);

if (!$idLote) {
    header('Location: ../../geral.php?pg=lista_te');
    exit;
}
$idLote = (int)$idLote;

$condicao = null;
if ($idControle) {
    $condicao = "id = '" . (int)$idControle . "' AND id_lote = '$idLote'";
}

if (!$condicao || !DBRead('transplante_controle', 'WHERE ' . $condicao) || !DBDelete('transplante_controle', $condicao)) {
    $_SESSION['alerta_te'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Não foi possível excluir a receptora do lote. Atualize a página e tente novamente.');
    header('Location: ../../geral.php?pg=te&id_lote=' . $idLote);
    exit;
}

$controles = DBRead('transplante_controle', "WHERE id_lote = '$idLote'") ?: array();
$positivos = DBRead('transplante_controle', "WHERE id_lote = '$idLote' AND ultrassom = '1'") ?: array();
$nascimentos = DBRead('transplante_controle', "WHERE id_lote = '$idLote' AND status_nascimento = '1'") ?: array();
$total = count($controles);

DBUpdate('lotes_reproducao', array(
    'ultrassom' => $total ? count($positivos) * 100 / $total : 0,
    'crias' => $total ? count($nascimentos) * 100 / $total : 0,
    'femeas' => $total
), "id_lote = '$idLote' AND tipo = '2'");

$_SESSION['alerta_te'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Receptora excluída do lote com sucesso.');
header('Location: ../../geral.php?pg=te&id_lote=' . $idLote);
exit;
