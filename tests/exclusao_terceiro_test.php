<?php
require_once __DIR__ . '/../animal/_dependencias_terceiro.php';
require_once __DIR__ . '/../animal/estado_terceiro.php';
$databaseName = 'siste870_buria';
require __DIR__ . '/../mysqli/environment.php';
require __DIR__ . '/../mysqli/_conexao.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();
function exigirExclusao($condicao, $mensagem) {
    if (!$condicao) { throw new RuntimeException($mensagem); }
}
try {
    // Tabelas temporárias isolam as fixtures dos dados reais.
    $esquema = array();
    foreach (mapaExclusaoTerceiro() as $tabela => $config) {
        $colunas = array('id');
        foreach ($config[1] as $par) { $colunas = array_merge($colunas, $par); }
        $colunas = array_values(array_unique($colunas));
        $definicoes = array_map(function ($coluna) { return "`$coluna` INT DEFAULT 0"; }, $colunas);
        if ($tabela === 'monta') { $colunas[] = 'macho'; $definicoes[] = "macho VARCHAR(100) DEFAULT ''"; }
        consultarExclusaoTerceiro($link, "CREATE TEMPORARY TABLE `$tabela` (" . implode(', ', $definicoes) . ')');
        $esquema[$tabela] = $colunas;
    }
    exigirExclusao(!dependenciasExclusaoTerceiro($link, $esquema, 7, 'Teste'), 'Cadastro sem vínculos bloqueado.');
    consultarExclusaoTerceiro($link, 'INSERT INTO animais (pai, terceiro_pai, mae, terceiro_mae) VALUES (7,0,7,0)');
    exigirExclusao(!dependenciasExclusaoTerceiro($link, $esquema, 7, 'Teste'), 'ID do rebanho confundido com terceiro.');
    consultarExclusaoTerceiro($link, 'INSERT INTO animais (pai, terceiro_pai, mae, terceiro_mae) VALUES (7,1,7,1)');
    $vinculos = dependenciasExclusaoTerceiro($link, $esquema, 7, 'Teste');
    exigirExclusao(count($vinculos) === 1 && $vinculos[0]['quantidade'] === 1, 'Cria deve ser contada uma vez.');
    consultarExclusaoTerceiro($link, 'INSERT INTO transplante (id_pai_2, terceiro_pai_2) VALUES (7,1)');
    consultarExclusaoTerceiro($link, 'INSERT INTO semen (id_animal, terceiro) VALUES (7,1)');
    consultarExclusaoTerceiro($link, 'INSERT INTO embriao (mae, terceiro_mae) VALUES (7,1)');
    consultarExclusaoTerceiro($link, "INSERT INTO monta (macho) VALUES ('Teste')");
    exigirExclusao(count(dependenciasExclusaoTerceiro($link, $esquema, 7, 'Teste')) === 5, 'Vínculos de estoque, TE ou legado ausentes.');
    exigirExclusao(!dependenciasExclusaoTerceiro($link, $esquema, 8, "Outro' nome"), 'Nome escapado incorretamente.');
    consultarExclusaoTerceiro($link, 'CREATE TEMPORARY TABLE terceiros (id INT PRIMARY KEY, nome VARCHAR(100), ativo TINYINT NOT NULL DEFAULT 1)');
    consultarExclusaoTerceiro($link, "INSERT INTO terceiros (id, nome) VALUES (7, 'Teste')");
    exigirExclusao(definirEstadoTerceiro($link, 7, 0), 'Não inativou cadastro existente.');
    $ativos = mysqli_fetch_assoc(consultarExclusaoTerceiro($link, 'SELECT COUNT(*) total FROM terceiros WHERE ativo = 1'));
    exigirExclusao((int)$ativos['total'] === 0, 'Inativo continua disponível para novos vínculos.');
    exigirExclusao(count(dependenciasExclusaoTerceiro($link, $esquema, 7, 'Teste')) === 5, 'Inativação modificou vínculos históricos.');
    exigirExclusao(definirEstadoTerceiro($link, 7, 1), 'Não reativou cadastro.');
    $ativos = mysqli_fetch_assoc(consultarExclusaoTerceiro($link, 'SELECT COUNT(*) total FROM terceiros WHERE ativo = 1'));
    exigirExclusao((int)$ativos['total'] === 1, 'Ativado não voltou a ficar disponível.');
    exigirExclusao(!definirEstadoTerceiro($link, 999, 1), 'Aceitou cadastro inexistente.');
    echo "OK: inativação, reativação e preservação integral do histórico.\n";
    echo "OK: sem vínculos, origem, contagem, macho complementar, estoques e monta legada.\n";
} finally { DBClose($link); }
