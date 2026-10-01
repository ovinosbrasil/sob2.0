<?php
require_once __DIR__ . '/../animal/importacao/leitor_consulta_rebanho.php';
function conferir($condicao, $mensagem) {
    if (!$condicao) { throw new RuntimeException($mensagem); }
}
function tabelaTesteRebanho(array $linhas, $colunas = null) {
    $html = '<table><tr>';
    foreach ($colunas ?? colunasArquivoRebanho() as $coluna) {
        $html .= '<th>' . htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') . '</th>';
    }
    $html .= '</tr>';
    foreach ($linhas as $linha) {
        $html .= '<tr>';
        foreach ($linha as $valor) { $html .= '<td>' . htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') . '</td>'; }
        $html .= '</tr>';
    }
    return $html . '</table>';
}
function deveRejeitarRebanho($html) {
    try { lerConsultaRebanho($html); } catch (RuntimeException $erro) { return; }
    throw new RuntimeException('Arquivo inválido foi aceito.');
}
$linha = array('O000123', 'ANIMAL TESTE 0007', '29/02/2024', 'Fêmea', 'PAI P001', 'MÃE M001');
$resultado = lerConsultaRebanho(tabelaTesteRebanho(array($linha)));
conferir(count($resultado) === 1 && !$resultado[0]['problemas'], 'Linha válida rejeitada.');
conferir($resultado[0]['dados']['Tat.'] === '0007', 'Zeros à esquerda perdidos.');
conferir($resultado[0]['dados']['FBB/FBE'] === 'O000123', 'Registro alterado.');
conferir($resultado[0]['dados']['Pai'] === 'PAI P001' && $resultado[0]['dados']['Mãe'] === 'MÃE M001', 'Filiação alterada.');
foreach (array('BURIA E081' => 'E081', 'BURIA KK E007' => 'E007', '  BURIA  TE  0007  ' => '0007', "BURIA\xc2\xa0E089" => 'E089', 'E081' => 'E081', '' => '') as $nome => $tatuagem) {
    conferir(tatuagemNomeRebanho($nome) === $tatuagem, 'Tatuagem incorreta para: ' . $nome);
}
$colunasAvos = array('FBB', 'Nome', 'Nasc.', 'Sexo', 'Pai', 'Avós Paternos', 'Mãe', 'Avós Maternos');
$comAvos = array('O000123', 'ANIMAL TESTE 0007', '29/02/2024', 'Fêmea', 'PAI P001', '<b>Avô:</b> IGNORADO A123', 'MÃE M001', '<b>Avó:</b> IGNORADA B456');
conferir(lerConsultaRebanho(tabelaTesteRebanho(array($comAvos), $colunasAvos))[0]['dados'] === $resultado[0]['dados'], 'Avós interferiram nos dados importados.');
conferir(lerConsultaRebanho(tabelaTesteRebanho(array(array_reverse($comAvos)), array_reverse($colunasAvos)))[0]['dados'] === $resultado[0]['dados'], 'Colunas de avós reordenadas interferiram no mapeamento.');
$comTat = array_merge($linha, array('ERRADA'));
conferir(lerConsultaRebanho(tabelaTesteRebanho(array($comTat), array_merge(colunasArquivoRebanho(), array('Tat.'))))[0]['dados']['Tat.'] === '0007', 'Tatuagem deve vir sempre do nome.');
$duplicados = lerConsultaRebanho(tabelaTesteRebanho(array($linha, $linha)));
conferir(count($duplicados[0]['problemas']) === 1 && count($duplicados[1]['problemas']) === 1, 'Duplicidade deve marcar ambas as linhas.');
$invalida = $linha;
$invalida[2] = '31/02/2024'; $invalida[3] = 'Outro';
conferir(count(lerConsultaRebanho(tabelaTesteRebanho(array($invalida)))[0]['problemas']) === 2, 'Data/sexo inválidos não sinalizados.');
conferir(count(lerConsultaRebanho(tabelaTesteRebanho(array(array('O1'))))[0]['problemas']) > 0, 'Linha incompleta não sinalizada.');
$colunasInvertidas = array_reverse(colunasArquivoRebanho());
$invertida = lerConsultaRebanho(tabelaTesteRebanho(array(array_reverse($linha)), $colunasInvertidas));
conferir($invertida[0]['dados'] === $resultado[0]['dados'] || $invertida[0]['dados'] == $resultado[0]['dados'], 'Cabeçalho reordenado mapeado incorretamente.');
$maliciosa = $linha; $maliciosa[1] = '<script>alert(1)</script>';
$registrosPrevia = lerConsultaRebanho(tabelaTesteRebanho(array($maliciosa)));
conferir($registrosPrevia[0]['dados']['Nome'] === $maliciosa[1], 'Conteúdo deve ser lido como dado.');
foreach (array('', '<p>Inválido</p>', 'PKarquivo', "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1", '<!DOCTYPE html><table></table>', str_repeat('x', 5242881), tabelaTesteRebanho(array()), tabelaTesteRebanho(array($linha)) . tabelaTesteRebanho(array($linha))) as $arquivo) {
    deveRejeitarRebanho($arquivo);
}
$latin = iconv('UTF-8', 'Windows-1252', tabelaTesteRebanho(array($linha)));
conferir(lerConsultaRebanho($latin)[0]['dados']['Sexo'] === 'Fêmea', 'Falha na codificação Windows-1252.');
if (isset($argv[1])) {
    $real = lerConsultaRebanho(file_get_contents($argv[1]));
    foreach ($real as $registro) {
        conferir($registro['dados']['Tat.'] === tatuagemNomeRebanho($registro['dados']['Nome']), 'Tatuagem divergente do nome no arquivo.');
    }
    echo 'Arquivo de referência: ' . count($real) . ' animais; ' . count(array_filter($real, function ($r) { return (bool)$r['problemas']; })) . " linhas para revisão.\n";
}
echo "Leitura, validação, duplicidade, codificação e rejeição de formatos: OK\n";

$arquivoLimite = tabelaTesteRebanho(array($linha));
$arquivoLimite .= str_repeat(' ', 5 * 1024 * 1024 - strlen($arquivoLimite));
conferir(count(lerConsultaRebanho($arquivoLimite)) === 1, 'Arquivo no limite de 5 MB rejeitado.');
deveRejeitarRebanho($arquivoLimite . ' ');
echo "Limite de upload: aceita 5 MB e rejeita acima: OK\n";

$muitasLinhas = array();
for ($i = 1; $i <= 5001; $i++) {
    $animal = $linha;
    $animal[0] = 'FBB' . $i;
    $animal[1] = 'ANIMAL ' . $i;
    $muitasLinhas[] = $animal;
}
conferir(count(lerConsultaRebanho(tabelaTesteRebanho($muitasLinhas))) === 5001, 'Importação deve aceitar mais de 5.000 animais.');
echo "Importação sem limite de quantidade: 5.001 animais lidos: OK\n";
