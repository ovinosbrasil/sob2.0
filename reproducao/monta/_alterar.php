<?php
require_once __DIR__ . '/../../_config.php';

$idLote = (int)($_GET['id_lote'] ?? 0);
function voltarAlteracaoMonta($mensagem)
{
    global $idLote;
    $_SESSION['alerta_cadastro_monta'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>$mensagem);
    $_SESSION['campos_cadastro_monta'] = array_intersect_key($_POST, array_flip(array('lote', 'data_inicial', 'data_final', 'macho', 'raca', 'notificacao')));
    header('Location: ../../geral.php?pg=monta&id_lote=' . $idLote);
    exit;
}
function dataAlteracaoMonta($valor)
{
    if (!is_string($valor)) return null;
    $valor = trim($valor);
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $valor);
    return $data && $data->format('d/m/Y') === $valor && (int)$data->format('Y') >= 1000 ? $data : null;
}
if ($idLote < 1 || !DBRead('monta', "WHERE id = '$idLote'")) {
    header('Location: ../../geral.php?pg=lista_monta');
    exit;
}
$codigo = trim((string)($_POST['lote'] ?? ''));
$inicio = dataAlteracaoMonta($_POST['data_inicial'] ?? '');
$fim = dataAlteracaoMonta($_POST['data_final'] ?? '');
$macho = trim((string)($_POST['macho'] ?? ''));
$raca = trim((string)($_POST['raca'] ?? ''));
$notificacao = trim((string)($_POST['notificacao'] ?? ''));
if ($codigo === '' || !$inicio || !$fim || $macho === '' || $raca === '' || !in_array($notificacao, array('PO', 'PC'), true)) {
    voltarAlteracaoMonta('Preencha corretamente todos os campos obrigatórios.');
}
$dias = (int)$inicio->diff($fim)->format('%r%a');
if ($dias < 0) voltarAlteracaoMonta('A data final não pode ser anterior à data inicial.');
if ($dias > 90) voltarAlteracaoMonta('A diferença entre as datas não pode ser maior que 90 dias.');
$codigoEscapado = DBEscape($codigo);
if (DBRead('monta', "WHERE codigo = '$codigoEscapado' AND id != '$idLote'")) {
    voltarAlteracaoMonta('Lote já existe. Tente novamente.');
}
$machoEscapado = DBEscape($macho);
$rebanho = DBRead('animais', "WHERE nome = '$machoEscapado' AND sexo = 'Macho'");
$terceiros = DBRead('terceiros', "WHERE ativo = 1 AND nome = '$machoEscapado' AND sexo = 'Macho'");
$idMacho = (int)($rebanho[0]['id'] ?? $terceiros[0]['id'] ?? 0);
if (!$idMacho) voltarAlteracaoMonta('Animal não existe. Tente novamente.');
$dados = array('codigo'=>$codigo, 'data_inicio'=>$inicio->format('Y-m-d'), 'data_fim'=>$fim->format('Y-m-d'),
    'raca'=>$raca, 'notificacao'=>$notificacao, 'terceiro'=>empty($rebanho[0]['id']) ? 1 : 0, 'id_animal'=>$idMacho);
if (!DBUpdate('monta', array_map('DBEscape', $dados), "id = '$idLote'")) {
    voltarAlteracaoMonta('Não foi possível alterar o lote. Tente novamente.');
}
$_SESSION['alerta_cadastro_monta'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Registro alterado com sucesso.');
unset($_SESSION['campos_cadastro_monta']);
header('Location: ../../geral.php?pg=monta&id_lote=' . $idLote);
exit;
