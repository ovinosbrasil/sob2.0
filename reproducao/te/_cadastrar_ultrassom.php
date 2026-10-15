<?php
require_once __DIR__ . '/../../_config.php';

$idControle = filter_var($_GET['id_controle'] ?? 0, FILTER_VALIDATE_INT);
$status = filter_var($_GET['status'] ?? 0, FILTER_VALIDATE_INT);

if (!$idControle || !in_array($status, array(1, 2), true)) {
    $_SESSION['alerta_te'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Informe um registro e um status de ultrassom válidos.');
    header('Location: ../../geral.php?pg=lista_te');
    exit;
}
$idControle = (int)$idControle;
$controle = DBRead('transplante_controle', "WHERE id = '$idControle'") ?: array();
if (!$controle) {
    $_SESSION['alerta_te'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Registro de ultrassom não encontrado.');
    header('Location: ../../geral.php?pg=lista_te');
    exit;
}

$idLote = (int)$controle[0]['id_lote'];
DBUpdate('transplante_controle', array('ultrassom' => $status, 'data_ultrassom' => (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d')), "id = '$idControle'");

$_SESSION['alerta_te'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Ultrassom alterado para ' . ($status === 1 ? 'positivo' : 'negativo') . ' com sucesso.');
header('Location: ../../geral.php?pg=te&id_lote=' . $idLote);
exit;
