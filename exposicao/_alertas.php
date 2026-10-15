<?php
// Respostas das ações de exposição seguem o mesmo flash de sessão dos demais módulos.
function avisarExposicao($tipo, $mensagem, $idEvento = 0, $aba = '')
{
    $_SESSION['alerta_exposicao'] = array(
        'tipo' => $tipo,
        'titulo' => $tipo === 'success' ? 'Sucesso!' : ($tipo === 'danger' ? 'Erro!' : 'Atenção!'),
        'mensagem' => $mensagem
    );
    $parametros = array('pg' => 'cadastrar_exposicao');
    if ((int)$idEvento > 0) {
        $parametros = array('pg' => 'exposicao', 'id_exposicao' => (int)$idEvento);
        if (in_array($aba, array('julgamento', 'vendas'), true)) $parametros['aba'] = $aba;
    }
    header('Location: ../geral.php?' . http_build_query($parametros), true, 303);
    exit;
}

function gravarExposicao($funcao, ...$argumentos)
{
    $resultado = $funcao(...$argumentos);
    if ($resultado === false) throw new RuntimeException('A gravação da exposição falhou.');
    return $resultado;
}

function dadosEventoExposicao($idEvento = 0)
{
    $campos = array();
    foreach (array('evento', 'data', 'cidade', 'local') as $nome) {
        $campos[$nome] = is_string($_POST[$nome] ?? null) ? trim($_POST[$nome]) : '';
    }
    $_SESSION['campos_exposicao'] = $campos;
    $data = DateTimeImmutable::createFromFormat('!d/m/Y', $campos['data']);
    if ($campos['evento'] === '' || $campos['data'] === '') {
        avisarExposicao('warning', 'Preencha os campos obrigatórios: Evento e Data.', $idEvento);
    }
    if (!$data || $data->format('d/m/Y') !== $campos['data']) {
        avisarExposicao('warning', 'Informe uma data válida no formato dd/mm/aaaa.', $idEvento);
    }
    return array('nome' => $campos['evento'], 'cidade' => $campos['cidade'], 'local' => $campos['local'], 'data' => $data->format('Y-m-d'));
}

function validarAnimalExposicao($participante = true)
{
    $idEvento = filter_var($_GET['id_evento'] ?? 0, FILTER_VALIDATE_INT);
    $idAnimal = filter_var($_GET['id_animal'] ?? 0, FILTER_VALIDATE_INT);
    if (!$idEvento || $idEvento < 1 || !DBRead('julgamento', "WHERE id = '$idEvento'")) {
        avisarExposicao('warning', 'Exposição não encontrada. Atualize a página e tente novamente.');
    }
    if (!$idAnimal || $idAnimal < 1 || !DBRead('animais', "WHERE id = '$idAnimal'")) {
        avisarExposicao('warning', 'Animal não encontrado. Selecione um animal válido.', $idEvento);
    }
    if ($participante && !DBRead('animais_evento', "WHERE id_animal = '$idAnimal' AND id_julgamento = '$idEvento'")) {
        avisarExposicao('warning', 'O animal não está cadastrado nesta exposição.', $idEvento);
    }
    return array((int)$idAnimal, (int)$idEvento);
}

set_exception_handler(function ($erro) {
    error_log('Falha no fluxo de exposição: ' . $erro->getMessage());
    $idEvento = filter_var($_GET['id_evento'] ?? 0, FILTER_VALIDATE_INT);
    avisarExposicao('danger', 'Não foi possível concluir a operação. Atualize a página e tente novamente.', $idEvento ?: 0);
});
