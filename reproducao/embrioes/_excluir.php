<?php
require_once __DIR__ . '/../../_config.php';
$id = filter_var($_GET['id_embriao'] ?? 0, FILTER_VALIDATE_INT);
if ($id > 0 && DBRead('embriao', "WHERE id = '" . (int)$id . "'") && DBDelete('embriao', "id = '" . (int)$id . "'")) {
    $_SESSION['alerta_embrioes'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Registro de embriões excluído com sucesso.');
} else {
    $_SESSION['alerta_embrioes'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Registro de embriões não encontrado. Atualize a página e tente novamente.');
}
header('Location: ../../geral.php?pg=embrioes', true, 303);
exit;
