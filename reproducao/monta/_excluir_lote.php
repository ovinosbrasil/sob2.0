<?php
require_once __DIR__ . '/../../_config.php';

$id_lote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
if (!$id_lote || $id_lote < 1 || !DBRead('monta', "WHERE id = '" . (int)$id_lote . "'")) {
    $_SESSION['alerta_cadastro_monta'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Lote de monta não encontrado. Atualize a página e tente novamente.');
    header('Location: ../../geral.php?pg=lista_monta');
    exit;
}
$id_lote = (int)$id_lote;

DBDelete('monta_controle', "id_monta = '$id_lote'");
DBDelete('monta', "id = '$id_lote'");
DBDelete('lotes_reproducao', "id_lote = '$id_lote' AND tipo = '0'");
$_SESSION['alerta_cadastro_monta'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Lote de monta excluído com sucesso.');
header('Location: ../../geral.php?pg=lista_monta');
exit;
?>
