<?php
require_once __DIR__ . '/../../_config.php';
function finalizarExclusaoChip($tipo, $mensagem)
{
    $_SESSION['alerta_chip'] = array('tipo'=>$tipo, 'titulo'=>$tipo === 'success' ? 'Sucesso!' : ($tipo === 'danger' ? 'Erro!' : 'Atenção!'), 'mensagem'=>$mensagem);
    header('Location: ../../geral.php?pg=cadastrar_chip', true, 303);
    exit;
}
try {
    if (function_exists('mysqli_report')) mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $idAnimal = filter_var($_GET['id_animal'] ?? 0, FILTER_VALIDATE_INT);
    if (!$idAnimal || $idAnimal < 1) finalizarExclusaoChip('warning', 'Animal inválido. Atualize a página e tente novamente.');
    $animais = DBRead('animais', "WHERE id = '$idAnimal'");
    if (!$animais) finalizarExclusaoChip('warning', 'Animal não encontrado.');
    if (empty($animais[0]['chip'])) finalizarExclusaoChip('warning', 'Este animal não possui chip cadastrado.');
    if (!DBUpdate('animais', array('chip'=>0), "id = '$idAnimal'")) finalizarExclusaoChip('danger', 'Não foi possível excluir o chip. Tente novamente.');
    finalizarExclusaoChip('success', 'Chip removido do animal com sucesso.');
} catch (Throwable $erro) {
    error_log('Falha ao excluir chip: ' . $erro->getMessage());
    finalizarExclusaoChip('danger', 'Não foi possível excluir o chip. Tente novamente.');
}
