<?php
require __DIR__ . '/../animal/cadastrar/_lista_nascimentos.php';
$consultas = array();
$totalTeste = 437;
function DBRead($fonte, $condicao, $campos = '*')
{
    global $consultas, $totalTeste;
    $consultas[] = array($fonte, $condicao, $campos);
    if ($campos === 'COUNT(*) AS total') { return array(array('total' => $totalTeste)); }
    return array(array('id' => 99, 'nome' => '<Animal>', 'sexo' => 'Fêmea', 'status' => 1,
        'data_de_nascimento' => '2026-09-20', 'pai' => 5, 'mae' => 8,
        'terceiro_pai' => 1, 'terceiro_mae' => 0, 'nome_pai' => 'Pai', 'nome_mae' => null));
}
function conferirNascimentos($condicao)
{
    if (!$condicao) { throw new RuntimeException('Falha na paginação dos nascimentos.'); }
}
$dados = consultarUltimosNascimentos(array('pagina_nascimentos' => 2, 'por_pagina_nascimentos' => 30));
conferirNascimentos($dados['offset'] === 30 && $dados['pagina'] === 2 && $dados['paginas'] === 15);
conferirNascimentos(count($consultas) === 2 && strpos($consultas[1][0], 'LIMIT 30,30') !== false);
conferirNascimentos(strpos($consultas[1][0], 'LEFT JOIN terceiros') !== false);
ob_start(); renderListaNascimentos($dados); $html = ob_get_clean();
conferirNascimentos(strpos($html, '&lt;Animal&gt;') !== false && strpos($html, '20/09/2026') !== false);
conferirNascimentos(strpos($html, 'abrir_terceiro(5)') !== false && strpos($html, 'data-pagina-nascimentos="3"') !== false);
$dados = consultarUltimosNascimentos(array('pagina_nascimentos' => 99999, 'por_pagina_nascimentos' => 100));
conferirNascimentos($dados['pagina'] === 5 && $dados['offset'] === 400);
$dados = consultarUltimosNascimentos(array('pagina_nascimentos' => -3, 'por_pagina_nascimentos' => 'inválido'));
conferirNascimentos($dados['pagina'] === 1 && $dados['limite'] === 15);
$totalTeste = 0; $consultas = array();
$dados = consultarUltimosNascimentos(array());
conferirNascimentos(count($consultas) === 1 && $dados['animais'] === array());
ob_start(); renderListaNascimentos($dados); $html = ob_get_clean();
conferirNascimentos(strpos($html, 'Nenhum nascimento cadastrado.') !== false);
echo "Paginação no banco, limites, vínculos de pais, escape e lista vazia: OK\n";
