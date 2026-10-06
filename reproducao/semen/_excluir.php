<?php
require_once __DIR__ . '/../../_config.php';
$id = filter_var($_GET['id_embriao'] ?? 0, FILTER_VALIDATE_INT);
if ($id > 0 && DBRead('semen', "WHERE id = '" . (int)$id . "'") && DBDelete('semen', "id = '" . (int)$id . "'")) {
    $_SESSION['alerta_semen'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Sêmen excluído com sucesso.');
} else {
    $_SESSION['alerta_semen'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Registro de sêmen não encontrado. Atualize a página e tente novamente.');
}
header('Location: ../../geral.php?pg=semen', true, 303);
exit;
