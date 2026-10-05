<?php
require_once __DIR__ . '/../../_config.php';

function voltarCadastroInseminacao($mensagem)
{
    echo '<script>alert(' . json_encode($mensagem, JSON_UNESCAPED_UNICODE) . '); history.back();</script>';
    exit;
}

function dataCadastroInseminacao($valor)
{
    $valor = trim((string)$valor);
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $valor);
    return $data && $data->format('d/m/Y') === $valor ? $data : null;
}

$codigo = trim((string)($_POST['lote'] ?? ''));
$data = dataCadastroInseminacao($_POST['data_inicial'] ?? '');
$nomeMacho = trim((string)($_POST['macho'] ?? ''));
$raca = trim((string)($_POST['raca'] ?? ''));
$semen = trim((string)($_POST['semen'] ?? ''));
$notificacao = trim((string)($_POST['notificacao'] ?? ''));

if (
    $codigo === '' || !$data || $nomeMacho === '' || $raca === '' ||
    !in_array($semen, array('A fresco', 'Congelado', 'Refrigerado'), true) ||
    !in_array($notificacao, array('PO', 'PC'), true)
) {
    voltarCadastroInseminacao('Preencha corretamente todos os campos obrigatórios.');
}

$codigoEscapado = DBEscape($codigo);
if (DBRead('inseminacao', "WHERE codigo = '$codigoEscapado'")) {
    voltarCadastroInseminacao('Lote já existe. Tente novamente.');
}

$idMacho = 0;
$terceiro = 0;
$idSelecionado = filter_var($_POST['macho_id'] ?? 0, FILTER_VALIDATE_INT);
$origemSelecionada = $_POST['macho_origem'] ?? '';

if ($idSelecionado && in_array($origemSelecionada, array('rebanho', 'terceiros'), true)) {
    $tabela = $origemSelecionada === 'terceiros' ? 'terceiros' : 'animais';
    $idSelecionado = (int)$idSelecionado;
    $selecionado = DBRead($tabela, "WHERE id = '$idSelecionado' AND sexo = 'Macho'" . ($tabela === 'terceiros' ? ' AND ativo = 1' : '')) ?: array();
    if (!empty($selecionado[0]['id'])) {
        $idMacho = (int)$selecionado[0]['id'];
        $terceiro = $origemSelecionada === 'terceiros' ? 1 : 0;
    }
}

// Mantém compatibilidade com formulários antigos que enviam somente o nome.
if (!$idMacho) {
    $nomeMachoEscapado = DBEscape($nomeMacho);
    $machoRebanho = DBRead('animais', "WHERE nome = '$nomeMachoEscapado' AND sexo = 'Macho'") ?: array();
    $machoTerceiro = DBRead('terceiros', "WHERE ativo = 1 AND nome = '$nomeMachoEscapado' AND sexo = 'Macho'") ?: array();

    if (!empty($machoRebanho[0]['id'])) {
        $idMacho = (int)$machoRebanho[0]['id'];
    } elseif (!empty($machoTerceiro[0]['id'])) {
        $idMacho = (int)$machoTerceiro[0]['id'];
        $terceiro = 1;
    }
}

if (!$idMacho) {
    voltarCadastroInseminacao('Animal não existe. Tente novamente.');
}

$idLote = DBCreate('inseminacao', array(
    'codigo' => $codigo,
    'data' => $data->format('Y-m-d'),
    'semen' => $semen,
    'raca' => $raca,
    'macho' => '',
    'notificacao' => $notificacao,
    'terceiro' => $terceiro,
    'id_macho' => $idMacho
), true);

DBCreate('lotes_reproducao', array(
    'id_lote' => (int)$idLote,
    'ultrassom' => 0,
    'femeas' => 0,
    'crias' => 0,
    'mortes' => 0,
    'tipo' => 1,
    'vivos' => 0
));

header('Location: ../../geral.php?pg=inseminacao&id_lote=' . (int)$idLote);
exit;
