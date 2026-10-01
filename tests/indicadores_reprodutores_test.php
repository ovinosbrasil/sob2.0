<?php
require_once __DIR__ . '/../relatorios/reprodutor/indicadores_reprodutores.php';
$databaseName = 'siste870_romana';
require __DIR__ . '/../mysqli/environment.php';
require __DIR__ . '/../mysqli/_conexao.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();
function DBRead($tabela, $params, $fields) {
    global $link;
    // Fixtures inline: executa a consulta real sem escrever em nenhuma tabela.
    $animais = "(SELECT 1 id, 'Pai' nome, 0 status, 'Macho' sexo, 0 pai, 0 terceiro_pai, 0 tipo, '' causa_da_perda, 0 peso3, 0 peso_inicial, '2020-01-01' data_de_nascimento, '2020-01-01' data3
        UNION ALL SELECT 2,'Cria A',0,'Macho',1,0,2,'',24,4,'2020-01-01','2020-04-10'
        UNION ALL SELECT 3,'Cria B',1,'Fêmea',1,0,4,'Nascimento',14,4,'2020-01-01','2020-04-10'
        UNION ALL SELECT 4,'Cria C',0,'Macho',1,0,0,'',20,4,'2020-01-01','2020-01-01'
        UNION ALL SELECT 5,'Outro pai terceiro',0,'Macho',1,1,5,'Nascimento',99,4,'2020-01-01','2020-04-10'
        UNION ALL SELECT 6,'Pai sem crias',0,'Macho',0,0,0,'',0,0,'2020-01-01','2020-01-01')";
    $vendas = "(SELECT 2 id_animal, 100 preco_de_venda, '2011-01-01' data UNION ALL SELECT 3,300,'2020-01-01' UNION ALL SELECT 2,900,'2010-12-31' UNION ALL SELECT 5,9999,'2020-01-01')";
    $valoresAvaliacao = array();
    foreach (array('tamanho', 'cabeca', 'pescoco', 'quarto_anterior', 'barril', 'quarto_posterior', 'comprimento', 'orgao', 'distribuicao', 'cobertura', 'cor', 'conformacao') as $campo) $valoresAvaliacao[] = "base.valor AS $campo";
    $avaliacoes = '(SELECT base.id, base.id_animal, base.etapa AS avaliacao, ' . implode(', ', $valoresAvaliacao) . ' FROM (
        SELECT 1 id, 2 id_animal, 1 etapa, 1 valor
        UNION ALL SELECT 2,2,2,3
        UNION ALL SELECT 3,2,2,5
        UNION ALL SELECT 4,3,1,3
        UNION ALL SELECT 5,5,2,99
    ) base)';
    $sql = "SELECT $fields FROM $tabela $params";
    $sql = preg_replace('/\banimais\b/', $animais, $sql);
    $sql = preg_replace('/\bvendas\b/', $vendas, $sql);
    if (preg_match('/\breprodutor\b/', $sql)) throw new RuntimeException('Consulta à tabela antiga.');
    $sql = preg_replace('/FROM avaliacao e/', 'FROM ' . $avaliacoes . ' e', $sql);
    $sql = preg_replace('/FROM avaliacao outra/', 'FROM ' . $avaliacoes . ' outra', $sql);
    return mysqli_fetch_all(mysqli_query($link, $sql), MYSQLI_ASSOC);
}
try {
    $dados = consultarIndicadoresReprodutores();
    if (count($dados) !== 1) throw new RuntimeException('Pais incorretos.');
    foreach (array('qtd_crias'=>3, 'qtd_avaliadas'=>2, 'nota'=>3, 'venda_geral'=>200, 'qtd_mortes'=>100/3, 'gmd'=>0.15, 'qtd_vendas'=>2, 'total_vendas'=>400, 'venda_macho'=>100, 'venda_femea'=>300, 'tipo2'=>1, 'tipo4'=>1) as $campo=>$valor) {
        if (abs((float)$dados[0][$campo]-$valor)>0.001) throw new RuntimeException('Indicador incorreto: '.$campo.' = '.$dados[0][$campo]);
    }
    $tipificacao = consultarTipificacaoReprodutores();
    if (count($tipificacao) !== 1 || (int)$tipificacao[0]['qtd_avaliadas'] !== 2 || (float)$tipificacao[0]['tamanho'] !== 4.0) throw new RuntimeException('Prioridade ou duplicação de avaliações incorreta.');
    require_once __DIR__ . '/../relatorios/reprodutor/detalhes_indicadores.php';
    set_error_handler(function ($nivel, $mensagem) { throw new RuntimeException($mensagem); });
    foreach (array(1,2,3,4,5) as $aba) {
        ob_start(); renderDetalhesReprodutor(1, $aba); $html = ob_get_clean();
        if (strpos($html, '<table') === false) throw new RuntimeException('Aba sem dados.');
    }
    ob_start(); renderDetalhesReprodutor(6, 1); $vazio = ob_get_clean();
    if (strpos($vazio, 'Nenhuma cria') === false) throw new RuntimeException('Pai sem crias.');
    restore_error_handler();
    echo "SQL: crias, tipos, venda desde 2011, mortalidade, GMD, pai terceiro e ausência de dados: OK\n";
} finally { DBClose($link); }
