<?php
// Render the page without authentication or a database connection.
function DBRead($table, $where = '') {
    return $table === 'controle_financeiro' ? $GLOBALS['financeiro_fixture'] : array();
}
function renderFinanceiro($post, $fixture) {
    $GLOBALS['financeiro_fixture'] = $fixture;
    $_POST = $post;
    $_GET = array();
    $source = file_get_contents(__DIR__ . '/../financeiro/financeiro.php');
    $source = str_replace("require_once __DIR__ . '/../_config.php';", '', $source);
    $source = preg_replace('/<link rel="stylesheet"[^\n]+/', '', $source);
    ob_start();
    eval('?>' . $source);
    return ob_get_clean();
}
function checkFinanceiro($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}
set_error_handler(function ($severity, $message) { throw new RuntimeException($message); });
$base = array('id' => 1, 'id_animal' => 0, 'id_comprador' => 0, 'data' => date('Y') . '-04-11', 'forma_de_pagamento' => 'Dinheiro');
$fixture = array(
    array_merge($base, array('tipo' => 0, 'status' => 1, 'valor' => 200, 'titulo' => 'Receita paga', 'categoria' => 'Vendas')),
    array_merge($base, array('tipo' => 1, 'status' => 0, 'valor' => 100, 'titulo' => 'Despesa pendente', 'categoria' => 'Internet')),
    array_merge($base, array('tipo' => 1, 'status' => 1, 'valor' => 50, 'titulo' => 'Despesa paga', 'categoria' => 'Internet'))
);
$html = renderFinanceiro(array(), $fixture);
checkFinanceiro(strpos($html, 'R$ 150,00') !== false, 'Saldo deve descontar apenas despesas pagas.');
checkFinanceiro(strpos($html, 'Pendentes: R$ 100,00') !== false, 'Resumo deve mostrar despesas pendentes.');
checkFinanceiro(strpos($html, 'Despesas: R$ 150,00') !== false, 'Gráfico deve agregar despesas pagas e pendentes.');
checkFinanceiro(substr_count($html, 'class="receitas"') === 12, 'Período padrão deve mostrar os 12 meses do ano.');
$html = renderFinanceiro(array('tipo_filtro' => '1', 'status_filtro' => 'Aguardando', 'categoria_filtro' => 'Internet'), $fixture);
checkFinanceiro(strpos($html, 'Despesa pendente') !== false && strpos($html, 'Receita paga') === false && strpos($html, 'Despesa paga') === false, 'Filtros devem ser combinados.');
checkFinanceiro(strpos($html, 'Despesas: R$ 100,00') !== false, 'Gráfico deve acompanhar os filtros.');
$html = renderFinanceiro(array('tipo_filtro' => '0'), $fixture);
checkFinanceiro(strpos($html, 'Receita paga') !== false && strpos($html, 'Despesa pendente') === false, 'Filtro de receita deve preservar o valor zero.');
$html = renderFinanceiro(array(), array());
checkFinanceiro(strpos($html, 'Nenhum lançamento encontrado') !== false, 'Deve mostrar estado vazio.');
echo "Financeiro: resumo, gráfico, filtros e estado vazio OK\n";
