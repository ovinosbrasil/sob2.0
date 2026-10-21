<?php
function avisarFinanceiro($tipo, $mensagem, $encerrar = true) {
    $_SESSION['alerta_financeiro'] = array('tipo' => $tipo, 'mensagem' => $mensagem);
    $params = array('pg' => 'financeiro');
    foreach (array('data_inicial', 'data_final') as $campo) {
        $valor = $_POST[$campo] ?? $_GET[$campo] ?? '';
        if (is_string($valor) && preg_match('~^\d{2}/\d{2}/\d{4}$~D', $valor)) $params[$campo] = $valor;
    }
    header('Location: ../geral.php?' . http_build_query($params), true, 303);
    if ($encerrar) exit;
}
function gravarFinanceiro($funcao, ...$argumentos) {
    $GLOBALS['financeiro_gravando'] = true;
    $resultado = $funcao(...$argumentos);
    $GLOBALS['financeiro_gravando'] = false;
    if ($resultado === false) throw new RuntimeException('Falha na gravação financeira.');
    return $resultado;
}
function registroFinanceiro() {
    $id = filter_var($_POST['id_financeiro'] ?? $_GET['id_financeiro'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    if (!$id) avisarFinanceiro('warning', 'Selecione um lançamento válido.');
    $registros = DBRead('controle_financeiro', "WHERE id = '$id'");
    if (!$registros) avisarFinanceiro('warning', 'O lançamento não foi encontrado. Atualize a lista.');
    return $registros[0];
}
function dadosFinanceiro($sufixo, $tipo) {
    $campos = array();
    foreach (array('descricao', 'data', 'valor', 'forma', 'categoria', 'parcelas') as $campo) {
        $valor = $_POST[$campo . '_' . $sufixo] ?? ($campo === 'parcelas' && $sufixo === 'alterar' ? '1' : '');
        $campos[$campo] = is_string($valor) ? trim($valor) : '';
    }
    $_SESSION['campos_financeiro'] = array('sufixo' => $sufixo, 'campos' => $campos, 'id' => (int)($_GET['id_financeiro'] ?? 0));
    if (!$campos['descricao'] || !$campos['data'] || !$campos['valor'] || !$campos['forma'] || ($tipo && !$campos['categoria'])) {
        avisarFinanceiro('warning', 'Preencha os campos obrigatórios do lançamento.');
    }
    $data = DateTime::createFromFormat('!d/m/Y', $campos['data']);
    if (!$data || $data->format('d/m/Y') !== $campos['data']) avisarFinanceiro('warning', 'Informe uma data de vencimento válida.');
    // Os campos monetários existentes usam ponto decimal e vírgula de milhar.
    if (!preg_match('/^(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d{1,2})?$/D', $campos['valor'])) avisarFinanceiro('warning', 'Informe um valor válido maior que zero.');
    $valor = (float)str_replace(',', '', $campos['valor']);
    if (!is_finite($valor) || $valor <= 0) avisarFinanceiro('warning', 'Informe um valor válido maior que zero.');
    $parcelas = filter_var($campos['parcelas'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 10)));
    if (!$parcelas) avisarFinanceiro('warning', 'Selecione de 1 a 10 parcelas.');
    return array('titulo' => $campos['descricao'], 'data' => $data->format('Y-m-d'), 'valor' => number_format($valor, 2, '.', ''), 'forma_de_pagamento' => $campos['forma'], 'categoria' => $campos['categoria'], 'parcelas' => $parcelas);
}
set_exception_handler(function ($erro) {
    $GLOBALS['financeiro_gravando'] = false;
    error_log('Falha no financeiro: ' . $erro->getMessage());
    avisarFinanceiro('danger', 'Não foi possível concluir a operação. Confira os lançamentos antes de tentar novamente.');
});
// O adaptador legado encerra a execução em erros SQL. Converte esse retorno em alerta.
register_shutdown_function(function () {
    if (!empty($GLOBALS['financeiro_gravando'])) {
        $GLOBALS['financeiro_gravando'] = false;
        if (ob_get_level()) ob_clean();
        avisarFinanceiro('danger', 'Não foi possível concluir a operação. Confira os lançamentos antes de tentar novamente.', false);
    }
});
