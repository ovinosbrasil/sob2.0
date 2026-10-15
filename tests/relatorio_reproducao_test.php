<?php
// Executar no contêiner app: php tests/relatorio_reproducao_test.php
set_error_handler(function ($nivel, $mensagem) { throw new RuntimeException($mensagem); });
$linkTeste = new mysqli(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'root', getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$linkTeste->set_charset('utf8mb4');
$bancoTeste = 'teste_relatorio_reproducao_' . bin2hex(random_bytes(6));
$linkTeste->query("CREATE DATABASE `$bancoTeste` CHARACTER SET utf8mb4");
register_shutdown_function(function () use ($linkTeste, $bancoTeste) { $linkTeste->query("DROP DATABASE `$bancoTeste`"); });
$linkTeste->select_db($bancoTeste);
function DBEscape($valor) { global $linkTeste; return $linkTeste->real_escape_string($valor); }
function DBRead($tabela, $params = '', $campos = '*') {
    global $linkTeste;
    if (strpos($tabela . $params . $campos, 'lotes_reproducao') !== false) throw new RuntimeException('Dependência da tabela de resumo.');
    return $linkTeste->query("SELECT $campos FROM $tabela $params")->fetch_all(MYSQLI_ASSOC);
}
function exigirReproducao($condicao, $mensagem) { if (!$condicao) throw new RuntimeException($mensagem); }
// Banco exclusivo de teste, removido ao finalizar; não utiliza dados cadastrados.
$linkTeste->query('CREATE TABLE animais (id INT, nome VARCHAR(100), mae INT, pai INT, terceiro_mae INT, terceiro_pai INT, tipo_reproducao VARCHAR(100), data_de_nascimento DATE, status INT, causa_da_perda VARCHAR(100))');
$linkTeste->query('CREATE TABLE terceiros (id INT, nome VARCHAR(100))');
$linkTeste->query('CREATE TABLE monta (id INT, codigo VARCHAR(100), id_animal INT, macho VARCHAR(100), terceiro INT, data_inicio DATE, data_fim DATE)');
$linkTeste->query('CREATE TABLE inseminacao (id INT, codigo VARCHAR(100), id_macho INT, terceiro INT, data DATE)');
$linkTeste->query('CREATE TABLE transplante (id INT, codigo VARCHAR(100), id_pai INT, id_mae INT, pai VARCHAR(100), mae VARCHAR(100), terceiro_pai INT, terceiro_mae INT, data DATE)');
$linkTeste->query('CREATE TABLE monta_controle (id INT, id_monta INT, id_animal INT, terceiro INT, ultrassom INT)');
$linkTeste->query('CREATE TABLE inseminacao_controle (id INT, id_lote INT, id_femea INT, terceiro INT, ultrassom INT)');
$linkTeste->query('CREATE TABLE transplante_controle (id INT, id_lote INT, ultrassom INT)');
$_GET = array();
ob_start();
require __DIR__ . '/../relatorios/relatorio_reproducao.php';
$html = ob_get_clean();
exigirReproducao(strpos($html, 'Nenhum lote encontrado') !== false, 'Relatório vazio.');
$linkTeste->query("INSERT INTO animais (id, nome) VALUES (1, 'Reprodutor'), (2, 'Matriz')");
foreach (array('monta', 'inseminacao', 'transplante') as $tabela) {
    $colunas = $tabela === 'monta' ? "id_animal, macho, terceiro, data_inicio, data_fim" : ($tabela === 'inseminacao' ? 'id_macho, terceiro, data' : 'id_pai, id_mae, pai, mae, terceiro_pai, terceiro_mae, data');
    $valores = $tabela === 'monta' ? "1, 'Reprodutor', 0, '2026-01-01', '2026-01-01'" : ($tabela === 'inseminacao' ? "1, 0, '2026-01-01'" : "1, 2, 'Reprodutor', 'Matriz', 0, 0, '2026-01-01'");
    $linkTeste->query("INSERT INTO $tabela (id, codigo, $colunas) VALUES (1, 'Com controles', $valores), (2, 'Sem controles', $valores)");
}
$linkTeste->query('INSERT INTO monta_controle VALUES (1,1,2,0,1),(2,1,3,0,2),(3,1,4,0,0),(4,1,5,0,NULL)');
$linkTeste->query('INSERT INTO inseminacao_controle VALUES (1,1,2,0,1),(2,1,3,0,2),(3,1,4,0,0),(4,1,5,0,NULL)');
$linkTeste->query('INSERT INTO transplante_controle VALUES (1,1,1),(2,1,2),(3,1,0),(4,1,NULL)');
foreach (array('Monta Natural', 'Inseminação Artificial', 'Embrionagem') as $i => $tipo) {
    $id = 10 + $i * 2;
    $linkTeste->query("INSERT INTO animais VALUES ($id,'Viva',2,1,0,0,'$tipo','2026-05-10',0,''), (" . ($id + 1) . ",'Morta',2,1,0,0,'$tipo','2026-05-10',1,'Nascimento')");
}
foreach (array('Monta natural', 'Inseminação artificial', 'Transplante de embriões') as $tipo) {
    $registros = registrosRelatorioReproducao($tipo);
    exigirReproducao(count($registros) === 2, "$tipo: incluir lote sem resumo/controles.");
    $porId = array_column($registros, null, 'id_lote');
    exigirReproducao((int)$porId[1]['femeas'] === 4 && (float)$porId[1]['ultrassom'] === 25.0, "$tipo: quantidade e percentual com positivos, negativos e pendentes.");
    exigirReproducao((int)$porId[1]['vivos'] === 1 && (int)$porId[1]['mortes'] === 1, "$tipo: nascimentos preservados.");
    exigirReproducao((int)$porId[2]['femeas'] === 0 && (float)$porId[2]['ultrassom'] === 0.0, "$tipo: lote sem controles zerado.");
}
$legado = registrosRelatorioReproducao('Monta natural', true);
exigirReproducao(count($legado) === 2 && $legado[0]['nome_macho'] === 'Reprodutor', 'Compatibilidade com monta por nome.');
$linkTeste->query('UPDATE monta_controle SET ultrassom = 1 WHERE id = 2');
$atualizados = array_column(registrosRelatorioReproducao('Monta natural'), null, 'id_lote');
exigirReproducao((float)$atualizados[1]['ultrassom'] === 50.0, 'Refletir atualização diretamente dos controles.');
echo "Reprodução: três modalidades, lotes vazios, ultrassom em tempo real, nascimentos e monta legada: OK\n";
