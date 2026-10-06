<?php
require_once __DIR__ . '/../../_config.php';

function voltarCadastroFemeaInseminacao($mensagem)
{
    global $idLote;
    $_SESSION['alerta_cadastro_inseminacao'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>$mensagem);
    header('Location: ../../geral.php?pg=' . ($idLote > 0 ? 'inseminacao&id_lote=' . (int)$idLote : 'lista_inseminacao'));
    exit;
}

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$nomeMae = trim((string)($_POST['mae'] ?? ''));

if (!$idLote || $nomeMae === '') {
    voltarCadastroFemeaInseminacao('Informe uma fêmea válida.');
}

$idLote = (int)$idLote;
$idMae = 0;
$origemTerceiro = 0;
$idSelecionado = filter_var($_POST['mae_id'] ?? 0, FILTER_VALIDATE_INT);
$origemSelecionada = $_POST['mae_origem'] ?? '';

if ($idSelecionado && in_array($origemSelecionada, array('rebanho', 'terceiros'), true)) {
    $tabela = $origemSelecionada === 'terceiros' ? 'terceiros' : 'animais';
    $idSelecionado = (int)$idSelecionado;
    $selecionada = DBRead($tabela, "WHERE id = '$idSelecionado' AND sexo = 'Fêmea'" . ($tabela === 'terceiros' ? ' AND ativo = 1' : '')) ?: array();
    if (!empty($selecionada[0]['id'])) {
        $idMae = (int)$selecionada[0]['id'];
        $origemTerceiro = $origemSelecionada === 'terceiros' ? 1 : 0;
    }
}

// Mantém compatibilidade com formulários antigos que enviam somente nome ou chip.
if (!$idMae) {
    $nomeMaeEscapado = DBEscape($nomeMae);
    $animal = DBRead('animais', "WHERE nome = '$nomeMaeEscapado' AND sexo = 'Fêmea'") ?: array();
    $terceiro = DBRead('terceiros', "WHERE ativo = 1 AND nome = '$nomeMaeEscapado' AND sexo = 'Fêmea'") ?: array();
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
    voltarCadastroFemeaInseminacao('Animal não existe. Tente novamente.');
}

$jaCadastrada = DBRead(
    'inseminacao_controle',
    "WHERE id_femea = '$idMae' AND id_lote = '$idLote' AND terceiro = '$origemTerceiro'"
);
if ($jaCadastrada) {
    voltarCadastroFemeaInseminacao('Animal já cadastrado no lote. Tente novamente.');
}

DBCreate('inseminacao_controle', array(
    'animal' => '',
    'id_lote' => $idLote,
    'status_nascimento' => 0,
    'terceiro' => $origemTerceiro,
    'ultrassom' => 0,
    'qtd' => 0,
    'id_femea' => $idMae
));

$controles = DBRead('inseminacao_controle', "WHERE id_lote = '$idLote'") ?: array();
$positivos = DBRead('inseminacao_controle', "WHERE id_lote = '$idLote' AND ultrassom = '1'") ?: array();
$nascimentos = DBRead('inseminacao_controle', "WHERE id_lote = '$idLote' AND status_nascimento = '1'") ?: array();
$total = count($controles);

DBUpdate('lotes_reproducao', array(
    'ultrassom' => $total ? count($positivos) * 100 / $total : 0,
    'crias' => $total ? count($nascimentos) * 100 / $total : 0,
    'femeas' => $total
), "id_lote = '$idLote' AND tipo = '1'");

$_SESSION['alerta_cadastro_inseminacao'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Fêmea adicionada ao lote com sucesso.');
header('Location: ../../geral.php?pg=inseminacao&id_lote=' . $idLote);
exit;
