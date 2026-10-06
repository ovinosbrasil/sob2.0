<?php
require_once __DIR__ . '/../../_config.php';

$id_lote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
if (!$id_lote || $id_lote < 1 || !DBRead('transplante', "WHERE id = '" . (int)$id_lote . "'")) {
    $_SESSION['alerta_te'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Lote de transplante não encontrado. Atualize a página e tente novamente.');
    header('Location: ../../geral.php?pg=lista_te');
    exit;
}
$id_lote = (int)$id_lote;

DBDelete('transplante_controle', "id_lote = '$id_lote'");
DBDelete('transplante', "id = '$id_lote'");
DBDelete('lotes_reproducao', "id_lote = '$id_lote' AND tipo = '2'");
$_SESSION['alerta_te'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Lote de transplante excluído com sucesso.');
header('Location: ../../geral.php?pg=lista_te');
exit;
?>
