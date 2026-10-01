<?php
require_once __DIR__ . '/../relatorios/matriz/indicadores_matrizes.php';
$databaseName = 'siste870_romana';
require __DIR__ . '/../mysqli/environment.php';
require __DIR__ . '/../mysqli/_conexao.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();
function DBRead($tabela, $params, $fields) {
    global $link;
    $base = array('id'=>1,'nome'=>'Matriz','status'=>0,'sexo'=>'Fêmea','mae'=>0,'terceiro_mae'=>0,'tipo'=>0,'causa_da_perda'=>'','peso3'=>0,'peso_inicial'=>0,'data_de_nascimento'=>'2015-01-01','data3'=>null,'tipo_reproducao'=>'Monta Natural','peso2'=>0,'data2'=>null);
    $animais = array($base);
    $animais[] = array_merge($base,array('id'=>2,'mae'=>1,'tipo'=>2,'sexo'=>'Macho','data_de_nascimento'=>'2020-01-01','peso2'=>10,'data2'=>'2020-02-20'));
    $animais[] = array_merge($animais[1],array('id'=>3,'tipo'=>4,'sexo'=>'Fêmea'));
    $animais[] = array_merge($base,array('id'=>4,'mae'=>1,'data_de_nascimento'=>'2021-01-01','peso2'=>20,'data2'=>'2021-04-11'));
    $animais[] = array_merge($animais[1],array('id'=>5,'tipo_reproducao'=>'Embrionagem','data_de_nascimento'=>'2020-06-01'));
    $animais[] = array_merge($animais[1],array('id'=>6,'terceiro_mae'=>1));
    $animais[] = array_merge($base,array('id'=>7));
    $partes = array();
    foreach ($animais as $animal) {
        $colunas = array();
        foreach ($animal as $campo=>$valor) $colunas[] = ($valor === null ? 'NULL' : "'" . mysqli_real_escape_string($link,(string)$valor) . "'") . ' AS `' . $campo . '`';
        $partes[] = 'SELECT ' . implode(', ', $colunas);
    }
    $sql = "SELECT $fields FROM $tabela $params";
    if (preg_match('/\bFROM matriz\b|\bJOIN matriz\b|\breprodutor\b/i',$sql)) throw new RuntimeException('Tabela antiga consultada.');
    $sql = preg_replace('/\banimais\b/', '(' . implode(' UNION ALL ', $partes) . ')', $sql);
    $sql = preg_replace('/\bvendas\b/', "(SELECT 2 id_animal, 100 preco_de_venda, '2011-01-01' data UNION ALL SELECT 3,300,'2020-01-01' UNION ALL SELECT 2,900,'2010-12-31')", $sql);
    return mysqli_fetch_all(mysqli_query($link,$sql),MYSQLI_ASSOC);
}
try {
    $dados=consultarIndicadoresMatrizes();
    if(count($dados)!==1)throw new RuntimeException('Matrizes incorretas.');
    foreach(array('qtd_crias'=>4,'qtd_avaliadas'=>3,'nota'=>8/3,'qtd_partos'=>2,'intervalo'=>366,'peso_apartacao'=>27,'prolificidade'=>1.5,'venda_geral'=>200) as $campo=>$valor) {
        if(abs((float)$dados[0][$campo]-$valor)>0.001)throw new RuntimeException('Indicador incorreto: '.$campo.' = '.$dados[0][$campo]);
    }
    echo "Matrizes: múltiplos, intervalo, prolificidade, apartação, embrionagem, terceiros e vendas: OK\n";
} finally { DBClose($link); }
