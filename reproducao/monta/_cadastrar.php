<?php
require_once __DIR__ . '/../../_config.php';

function voltarCadastroMonta($mensagem)
{
    $_SESSION['alerta_cadastro_monta'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>$mensagem);
    $_SESSION['campos_cadastro_monta'] = array_intersect_key($_POST, array_flip(array('lote', 'data_inicial', 'data_final', 'macho', 'raca', 'notificacao')));
    header('Location: ../../geral.php?pg=cadastrar_monta');
    exit;
}

function dataCadastroMonta($valor)
{
    $valor = trim((string)$valor);
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $valor);
    return $data && $data->format('d/m/Y') === $valor ? $data : null;
}

$codigo = trim((string)($_POST['lote'] ?? ''));
$inicio = dataCadastroMonta($_POST['data_inicial'] ?? '');
$fim = dataCadastroMonta($_POST['data_final'] ?? '');
$nomeMacho = trim((string)($_POST['macho'] ?? ''));
$raca = trim((string)($_POST['raca'] ?? ''));
$notificacao = trim((string)($_POST['notificacao'] ?? ''));

if ($codigo === '' || !$inicio || !$fim || $nomeMacho === '' || $raca === '' || !in_array($notificacao, array('PO', 'PC'), true)) {
    voltarCadastroMonta('Preencha corretamente todos os campos obrigatórios.');
}

$dias = (int)$inicio->diff($fim)->format('%r%a');
if ($dias < 0) {
    voltarCadastroMonta('A data final não pode ser anterior à data inicial.');
}
if ($dias > 90) {
    voltarCadastroMonta('A diferença entre as datas não pode ser maior que 90 dias.');
}

$codigoEscapado = DBEscape($codigo);
if (DBRead('monta', "WHERE codigo = '$codigoEscapado'")) {
    voltarCadastroMonta('Lote já existe. Tente novamente.');
}

$nomeMachoEscapado = DBEscape($nomeMacho);
$machoRebanho = DBRead('animais', "WHERE nome = '$nomeMachoEscapado' AND sexo = 'Macho'") ?: array();
$machoTerceiro = DBRead('terceiros', "WHERE ativo = 1 AND nome = '$nomeMachoEscapado' AND sexo = 'Macho'") ?: array();

$idMacho = 0;
$terceiro = 0;
if (!empty($machoRebanho[0]['id'])) {
    $idMacho = (int)$machoRebanho[0]['id'];
} elseif (!empty($machoTerceiro[0]['id'])) {
    $idMacho = (int)$machoTerceiro[0]['id'];
    $terceiro = 1;
}
if (!$idMacho) {
    voltarCadastroMonta('Animal não existe. Tente novamente.');
}

$idLote = DBCreate('monta', array(
    'codigo' => $codigo,
    'data_inicio' => $inicio->format('Y-m-d'),
    'data_fim' => $fim->format('Y-m-d'),
    'macho' => '',
    'raca' => $raca,
    'notificacao' => $notificacao,
    'id_animal' => $idMacho,
    'terceiro' => $terceiro
), true);

DBCreate('lotes_reproducao', array(
    'id_lote' => (int)$idLote,
    'ultrassom' => 0,
    'femeas' => 0,
    'crias' => 0,
    'mortes' => 0,
    'tipo' => 0,
    'vivos' => 0
));

$_SESSION['alerta_cadastro_monta'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Lote de monta natural cadastrado com sucesso.');
unset($_SESSION['campos_cadastro_monta']);
header('Location: ../../geral.php?pg=monta&id_lote=' . (int)$idLote);
exit;
