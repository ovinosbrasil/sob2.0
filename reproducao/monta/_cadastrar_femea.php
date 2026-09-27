<?php
require_once __DIR__ . '/../../_config.php';

function voltarCadastroFemeaMonta($mensagem)
{
    echo '<script>alert(' . json_encode($mensagem, JSON_UNESCAPED_UNICODE) . '); history.back();</script>';
    exit;
}

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$nomeMae = trim((string)($_POST['mae'] ?? ''));

if (!$idLote || $nomeMae === '') {
    voltarCadastroFemeaMonta('Informe uma fêmea válida.');
}

$idLote = (int)$idLote;
$idMae = 0;
$origemTerceiro = 0;
$idSelecionado = filter_var($_POST['mae_id'] ?? 0, FILTER_VALIDATE_INT);
$origemSelecionada = $_POST['mae_origem'] ?? '';

if ($idSelecionado && in_array($origemSelecionada, array('rebanho', 'terceiros'), true)) {
    $tabela = $origemSelecionada === 'terceiros' ? 'terceiros' : 'animais';
    $idSelecionado = (int)$idSelecionado;
    $selecionada = DBRead($tabela, "WHERE id = '$idSelecionado' AND sexo = 'Fêmea'") ?: array();
    if (!empty($selecionada[0]['id'])) {
        $idMae = (int)$selecionada[0]['id'];
        $origemTerceiro = $origemSelecionada === 'terceiros' ? 1 : 0;
    }
}

// Compatibilidade com formulários antigos que enviam somente nome ou chip.
if (!$idMae) {
    $nomeMaeEscapado = DBEscape($nomeMae);
    $animal = DBRead('animais', "WHERE nome = '$nomeMaeEscapado' AND sexo = 'Fêmea'") ?: array();
    $terceiro = DBRead('terceiros', "WHERE nome = '$nomeMaeEscapado' AND sexo = 'Fêmea'") ?: array();
    $animalPorChip = DBRead('animais', "WHERE chip = '$nomeMaeEscapado' AND sexo = 'Fêmea'") ?: array();

    if (!empty($animal[0]['id'])) {
        $idMae = (int)$animal[0]['id'];
    } elseif (!empty($terceiro[0]['id'])) {
        $idMae = (int)$terceiro[0]['id'];
        $origemTerceiro = 1;
    } elseif (!empty($animalPorChip[0]['id'])) {
        $idMae = (int)$animalPorChip[0]['id'];
    }
}

if (!$idMae) {
    voltarCadastroFemeaMonta('Animal não existe. Tente novamente.');
}

$jaCadastrada = DBRead(
    'monta_controle',
    "WHERE id_animal = '$idMae' AND id_monta = '$idLote' AND terceiro = '$origemTerceiro'"
);
if ($jaCadastrada) {
    voltarCadastroFemeaMonta('Animal já cadastrado no lote. Tente novamente.');
}

DBCreate('monta_controle', array(
    'id_monta' => $idLote,
    'nome' => '',
    'status_nascimento' => 0,
    'id_animal' => $idMae,
    'terceiro' => $origemTerceiro,
    'ultrassom' => 0
));

$controles = DBRead('monta_controle', "WHERE id_monta = '$idLote'") ?: array();
$positivos = DBRead('monta_controle', "WHERE id_monta = '$idLote' AND ultrassom = '1'") ?: array();
$nascimentos = DBRead('monta_controle', "WHERE id_monta = '$idLote' AND status_nascimento = '1'") ?: array();
$total = count($controles);

DBUpdate('lotes_reproducao', array(
    'ultrassom' => $total ? count($positivos) * 100 / $total : 0,
    'crias' => $total ? count($nascimentos) * 100 / $total : 0,
    'femeas' => $total
), "id_lote = '$idLote' AND tipo = '0'");

header('Location: ../../geral.php?pg=monta&id_lote=' . $idLote);
exit;
