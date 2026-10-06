<?php
require_once __DIR__ . '/../../_config.php';
$idLote = (int)($_GET['id_lote'] ?? 0);
if ($idLote < 1 || !DBRead('inseminacao', "WHERE id = '$idLote'")) { header('Location: ../../geral.php?pg=lista_inseminacao'); exit; }

function voltarCadastroInseminacao($mensagem)
{
    global $idLote;
    $_SESSION['alerta_cadastro_inseminacao'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>$mensagem);
    $_SESSION['campos_cadastro_inseminacao'] = array_intersect_key($_POST, array_flip(array('lote', 'data_inicial', 'macho', 'macho_id', 'macho_origem', 'raca', 'semen', 'notificacao')));
    header('Location: ../../geral.php?pg=inseminacao&id_lote=' . $idLote);
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
if (DBRead('inseminacao', "WHERE codigo = '$codigoEscapado' AND id != '$idLote'")) {
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

$dados = array('codigo'=>$codigo, 'data'=>$data->format('Y-m-d'), 'semen'=>$semen, 'raca'=>$raca,
    'macho'=>'', 'notificacao'=>$notificacao, 'terceiro'=>$terceiro, 'id_macho'=>$idMacho);
if (!DBUpdate('inseminacao', array_map('DBEscape', $dados), "id = '$idLote'")) {
    voltarCadastroInseminacao('Não foi possível alterar o lote. Tente novamente.');
}
$_SESSION['alerta_cadastro_inseminacao'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Registro alterado com sucesso.');
unset($_SESSION['campos_cadastro_inseminacao']);
header('Location: ../../geral.php?pg=inseminacao&id_lote=' . $idLote);
exit;
