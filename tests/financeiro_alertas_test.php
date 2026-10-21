<?php
if (isset($argv[1])) {
    $caso = json_decode(base64_decode($argv[1]), true);
    $_SESSION = array('_csrf_sob'=>'token-teste');
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_GET = array('id_financeiro' => 7);
    $sufixo = $caso['sufixo'] ?? 'debito';
    $_POST = array();
    foreach (array('descricao'=>'Internet', 'data'=>'31/01/2026', 'valor'=>'1,200.50', 'forma'=>'Pix', 'categoria'=>'Outros', 'parcelas'=>'2') as $nome=>$valor) {
        $_POST[$nome . '_' . $sufixo] = $valor;
    }
    $_POST = array_merge($_POST, $caso['post'] ?? array());
    $_POST['_csrf_sob'] = 'token-teste';
    $gravacoes = array();
    function DBEscape($valor) { return addslashes($valor); }
    function DBRead($table, $where) { return !empty($GLOBALS['caso']['ausente']) ? false : array(array_merge(array('id'=>7, 'tipo'=>1), $GLOBALS['caso']['registro'] ?? array())); }
    function DBCreate($table, $dados) {
        if (!empty($GLOBALS['caso']['sql_die'])) die('SQL error');
        $GLOBALS['gravacoes'][] = $dados;
        return empty($GLOBALS['caso']['falha']);
    }
    function DBUpdate($table, $dados, $where) { return DBCreate($table, $dados); }
    function DBDelete($table, $where) { return DBCreate($table, array('excluido'=>true)); }
    ob_start();
    // Executa depois do shutdown do helper, inclusive no adaptador SQL legado.
    require __DIR__ . '/../financeiro/_alertas.php';
    register_shutdown_function(function () {
        if (ob_get_level()) ob_clean();
        echo json_encode(array('alerta'=>$_SESSION['alerta_financeiro'] ?? null, 'campos'=>$_SESSION['campos_financeiro'] ?? null, 'gravacoes'=>$GLOBALS['gravacoes']));
    });
    $arquivo = $caso['arquivo'] ?? '_cadastrar.php';
    $sufixoFinanceiro = $sufixo;
    $tipoFinanceiro = $sufixo === 'faturamento' ? 0 : 1;
    $source = file_get_contents(__DIR__ . '/../financeiro/' . $arquivo);
    $source = str_replace(array("require_once __DIR__ . '/../_config.php';", "require_once __DIR__ . '/_alertas.php';"), '', $source);
    $source = str_replace('__DIR__', var_export(realpath(__DIR__ . '/../financeiro'), true), $source);
    eval('?>' . $source);
    exit;
}
$casos = array(
    array('arquivo'=>'_alterar.php', 'sufixo'=>'alterar', 'registro'=>array('tipo'=>0, 'titulo'=>'Pacote de vendas 2/10'), 'post'=>array('descricao_alterar'=>'Título adulterado'), 'titulo_bloqueado'=>true, 'esperado'=>'success'),
    array('arquivo'=>'_alterar.php', 'sufixo'=>'alterar', 'registro'=>array('tipo'=>0, 'titulo'=>'Venda de sêmen - Animal - 2 Doses - 1/3', 'id_semen'=>4), 'post'=>array('descricao_alterar'=>''), 'titulo_bloqueado'=>true, 'esperado'=>'success'),
    array('esperado'=>'success'),
    array('sufixo'=>'faturamento', 'esperado'=>'success'),
    array('post'=>array('valor_debito'=>'100.00', 'parcelas_debito'=>'3'), 'esperado'=>'success'),
    array('sufixo'=>'faturamento', 'post'=>array('valor_faturamento'=>'100.00', 'parcelas_faturamento'=>'3'), 'esperado'=>'success'),
    array('post'=>array('parcelas_debito'=>'1'), 'esperado'=>'success'),
    array('post'=>array('valor_debito'=>'0.01', 'parcelas_debito'=>'2'), 'esperado'=>'warning'),
    array('post'=>array('descricao_debito'=>''), 'esperado'=>'warning'),
    array('post'=>array('data_debito'=>'31/02/2026'), 'esperado'=>'warning'),
    array('post'=>array('valor_debito'=>'0.00'), 'esperado'=>'warning'),
    array('post'=>array('valor_debito'=>'abc'), 'esperado'=>'warning'),
    array('post'=>array('categoria_debito'=>''), 'esperado'=>'warning'),
    array('post'=>array('parcelas_debito'=>'11'), 'esperado'=>'warning'),
    array('falha'=>true, 'esperado'=>'danger'),
    array('sql_die'=>true, 'esperado'=>'danger'),
    array('arquivo'=>'_confirmar.php', 'esperado'=>'success'),
    array('arquivo'=>'_cancelar.php', 'esperado'=>'success'),
    array('arquivo'=>'_excluir.php', 'esperado'=>'success'),
    array('arquivo'=>'_excluir.php', 'ausente'=>true, 'esperado'=>'warning'),
    array('arquivo'=>'_alterar.php', 'sufixo'=>'alterar', 'esperado'=>'success'),
    array('arquivo'=>'_alterar.php', 'sufixo'=>'alterar', 'post'=>array('data_alterar'=>'invalida'), 'esperado'=>'warning')
);
foreach ($casos as $caso) {
    $cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . escapeshellarg(base64_encode(json_encode($caso)));
    $resultado = json_decode(shell_exec($cmd), true);
    if (($resultado['alerta']['tipo'] ?? '') !== $caso['esperado']) throw new RuntimeException('Retorno incorreto: ' . json_encode($caso) . ' ' . json_encode($resultado));
    if (!empty($caso['titulo_bloqueado']) && array_key_exists('titulo', $resultado['gravacoes'][0])) throw new RuntimeException('Título de venda não pode ser atualizado.');
    if ($caso['esperado'] === 'warning' && $resultado['gravacoes']) throw new RuntimeException('Gravou dados inválidos.');
    if ($caso['esperado'] === 'warning' && empty($caso['ausente']) && !$resultado['campos']) throw new RuntimeException('Não preservou campos.');
    if ($caso['esperado'] === 'success' && empty($caso['arquivo'])) {
        $sufixoTeste = $caso['sufixo'] ?? 'debito';
        $n = (int)($caso['post']['parcelas_' . $sufixoTeste] ?? 2);
        $total = (int)round((float)str_replace(',', '', $caso['post']['valor_' . $sufixoTeste] ?? '1,200.50') * 100);
        $datas = array_slice(array('2026-01-31', '2026-02-28', '2026-03-31'), 0, $n);
        if (array_column($resultado['gravacoes'], 'data') !== $datas) throw new RuntimeException('Datas inválidas nas parcelas.');
        $soma = 0;
        foreach ($resultado['gravacoes'] as $i => $gravacao) {
            $centavos = (int)round((float)$gravacao['valor'] * 100);
            $soma += $centavos;
            if ($centavos !== intdiv($total, $n) + ($i < $total % $n ? 1 : 0)) throw new RuntimeException('Divisão incorreta.');
            if ($gravacao['titulo'] !== 'Internet ' . ($i + 1) . '/' . $n) throw new RuntimeException('Identificação da parcela incorreta.');
        }
        if ($soma !== $total) throw new RuntimeException('A soma das parcelas difere do valor total.');
        foreach (array('categoria', 'id_tipo', 'obs', 'id_animal', 'id_comprador', 'id_embriao', 'id_semen') as $obrigatorio) {
            if (!array_key_exists($obrigatorio, $resultado['gravacoes'][0])) throw new RuntimeException('Coluna obrigatória ausente: ' . $obrigatorio);
        }
        if ($resultado['gravacoes'][0]['forma_de_pagamento'] !== 'Pix') throw new RuntimeException('Pix não foi preservado.');
        if ($resultado['campos'] !== null) throw new RuntimeException('Campos persistiram após sucesso.');
    }
}
echo "Financeiro: alertas, operações e divisão de parcelas OK\n";
