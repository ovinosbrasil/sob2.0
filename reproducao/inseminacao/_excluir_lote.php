<?php
require_once __DIR__ . '/../../_config.php';

$id_lote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
if (!$id_lote || $id_lote < 1 || !DBRead('inseminacao', "WHERE id = '" . (int)$id_lote . "'")) {
    $_SESSION['alerta_cadastro_inseminacao'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Lote de inseminação não encontrado. Atualize a página e tente novamente.');
    header('Location: ../../geral.php?pg=lista_inseminacao');
    exit;
}
$id_lote = (int)$id_lote;

DBDelete('inseminacao_controle', "id_lote = '$id_lote'");
DBDelete('inseminacao', "id = '$id_lote'");
DBDelete('lotes_reproducao', "id_lote = '$id_lote' AND tipo = '1'");
$_SESSION['alerta_cadastro_inseminacao'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Lote de inseminação excluído com sucesso.');
header('Location: ../../geral.php?pg=lista_inseminacao');
exit;
?>
