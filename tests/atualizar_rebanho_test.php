<?php
require_once __DIR__ . '/../animal/importacao/atualizar_animal.php';
require_once __DIR__ . '/../animal/importacao/leitor_consulta_rebanho.php';
require_once __DIR__ . '/../animal/importacao/comparar_rebanho.php';
function exigirAtualizacao($ok, $mensagem) { if (!$ok) throw new RuntimeException($mensagem); }
function rejeitarAtualizacao(callable $acao) {
    try { $acao(); } catch (RuntimeException $erro) { return; }
    throw new RuntimeException('Operação inválida foi aceita.');
}
$dados = array('Nome' => "OVELHA D'ÁGUA", 'FBB/FBE' => 'O0001', 'Tat.' => '0007', 'Nasc.' => '29/02/2024');
$novos = dadosAtualizacaoPrevia($dados);
exigirAtualizacao($novos['data_de_nascimento'] === '2024-02-29' && $novos['tatuagem'] === '0007', 'Conversão de dados incorreta.');
foreach (array('Nome', 'FBB/FBE', 'Tat.', 'Nasc.') as $campo) {
    $invalidos = $dados; $invalidos[$campo] = '';
    rejeitarAtualizacao(function () use ($invalidos) { dadosAtualizacaoPrevia($invalidos); });
}
foreach (array('Nome' => 80, 'FBB/FBE' => 20, 'Tat.' => 15) as $campo => $limite) {
    $invalidos = $dados; $invalidos[$campo] = str_repeat('A', $limite + 1);
    rejeitarAtualizacao(function () use ($invalidos) { dadosAtualizacaoPrevia($invalidos); });
}
exigirAtualizacao(selecionarCamposAtualizacaoPrevia($novos, array('nome')) === array('nome' => $novos['nome']), 'Filtragem deve manter somente os campos selecionados.');
rejeitarAtualizacao(function () use ($novos) { selecionarCamposAtualizacaoPrevia($novos, array()); });
rejeitarAtualizacao(function () use ($novos) { selecionarCamposAtualizacaoPrevia($novos, array('id')); });
$invalidos = $dados; $invalidos['Nasc.'] = '31/02/2024';
rejeitarAtualizacao(function () use ($invalidos) { dadosAtualizacaoPrevia($invalidos); });
$anterior = array('id' => 1, 'nome' => 'ANTIGO', 'fbb' => 'F1', 'tatuagem' => 'T1', 'data_de_nascimento' => '2020-01-01');
$loteCampos = array();
for ($i = 1; $i <= 21; $i++) {
    $loteCampos[] = array('linha' => $i, 'anterior' => $anterior, 'novos' => $novos);
}
$filtrado = excluirCamposLotePrevia($loteCampos, array(2 => array('fbb')), array('nome', 'tatuagem', 'data_de_nascimento'));
exigirAtualizacao(count($filtrado) === 20, 'Seleção global deve respeitar exclusões individuais.');
foreach ($filtrado as $item) {
    exigirAtualizacao($item['novos'] === array('fbb' => $novos['fbb']) && !$item['completa'], 'Somente FBB deve mudar em todas as páginas, preservando atualização parcial.');
}
rejeitarAtualizacao(function () use ($loteCampos) { excluirCamposLotePrevia($loteCampos, array(), array('id')); });
rejeitarAtualizacao(function () use ($loteCampos, $novos) { excluirCamposLotePrevia($loteCampos, array(), array_keys($novos)); });
$previa = array('versao' => 3, 'banco' => 'fazenda', 'login' => 'usuario', 'envio' => 'envio-1', 'possiveis' => array(array('linha' => 2, 'dados' => $dados, 'animais_banco' => array($anterior))));
exigirAtualizacao(selecionarAtualizacaoPrevia($previa, 'envio-1', 2, 1, 'fazenda', 'usuario')[1] === $novos, 'Seleção inválida.');
foreach (array(array('envio-2', 2, 1, 'fazenda', 'usuario'), array('envio-1', 3, 1, 'fazenda', 'usuario'), array('envio-1', 2, 99, 'fazenda', 'usuario'), array('envio-1', 2, 1, 'outra', 'usuario'), array('envio-1', 2, 1, 'fazenda', 'outro')) as $parametros) {
    rejeitarAtualizacao(function () use ($previa, $parametros) { selecionarAtualizacaoPrevia($previa, ...$parametros); });
}
exigirAtualizacao(count(atualizacoesEmLotePrevia($previa)) === 1, 'Lote deve incluir candidato único.');
$ambiguo = $previa;
$ambiguo['possiveis'][0]['animais_banco'][] = array_merge($anterior, array('id' => 2));
exigirAtualizacao(!atualizacoesEmLotePrevia($ambiguo), 'Lote não pode escolher entre candidatos.');
$repetido = $previa;
$repetido['possiveis'][] = array_merge($previa['possiveis'][0], array('linha' => 3));
exigirAtualizacao(!atualizacoesEmLotePrevia($repetido), 'Animal repetido no lote deve ficar para revisão.');
$previaAntiga = $previa; unset($previaAntiga['versao']);
rejeitarAtualizacao(function () use ($previaAntiga) { selecionarAtualizacaoPrevia($previaAntiga, 'envio-1', 2, 1, 'fazenda', 'usuario'); });
$previa['atualizados'][2] = 1;
rejeitarAtualizacao(function () use ($previa) { selecionarAtualizacaoPrevia($previa, 'envio-1', 2, 1, 'fazenda', 'usuario'); });
echo "Validação dos dados, candidato, contexto, prévia expirada e reenvio: OK\n";
if (($argv[1] ?? '') !== '--mysql') { exit; }
$databaseName = getenv('DB_DATABASE') ?: 'siste870_sob';
require __DIR__ . '/../mysqli/environment.php';
require __DIR__ . '/../mysqli/_conexao.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();
try {
    // A tabela temporária oculta a tabela real apenas nesta conexão; nenhum cadastro real é escrito.
    mysqli_query($link, 'CREATE TEMPORARY TABLE animais (id INT PRIMARY KEY, nome VARCHAR(80), fbb VARCHAR(20), tatuagem VARCHAR(15), data_de_nascimento DATE, sexo VARCHAR(20), status INT NOT NULL DEFAULT 0, causa_da_perda VARCHAR(50), data_de_saida DATE) ENGINE=InnoDB');
    mysqli_query($link, "INSERT INTO animais (id,nome,fbb,tatuagem,data_de_nascimento,sexo,status) VALUES (1, 'ANTIGO', 'F1', 'T1', '2020-01-01', 'Fêmea', 0), (2, 'OUTRO', 'F2', 'T2', '2021-01-01', 'Macho', 0)");
    // Percorre leitura da exportação, comparação, seleção autorizada e gravação.
    $html = '<table><tr>';
    foreach (colunasArquivoRebanho() as $coluna) {
        $html .= '<th>' . htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') . '</th>';
    }
    $html .= '</tr><tr>';
    $linhaXls = array('F1', "OVELHA D'ÁGUA T1", '29/02/2024', 'Fêmea', 'PAI P001', 'MÃE M001');
    foreach ($linhaXls as $valor) { $html .= '<td>' . htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') . '</td>'; }
    $registros = lerConsultaRebanho($html . '</tr></table>');
    $animaisAntes = mysqli_fetch_all(mysqli_query($link, 'SELECT * FROM animais'), MYSQLI_ASSOC);
    $grupos = compararAnimaisPrevia($registros, $animaisAntes);
    exigirAtualizacao(count($grupos['possiveis_atualizacoes']) === 1, 'Candidato da importação não encontrado.');
    $sessao = array('versao' => 3, 'banco' => DB_DATABASE, 'login' => 'teste', 'envio' => 'teste-xls', 'possiveis' => $grupos['possiveis_atualizacoes']);
    list($anterior, $novos) = selecionarAtualizacaoPrevia($sessao, 'teste-xls', 2, 1, DB_DATABASE, 'teste');
    exigirAtualizacao(mysqli_fetch_all(mysqli_query($link, 'SELECT * FROM animais'), MYSQLI_ASSOC) === $animaisAntes, 'A prévia alterou o banco.');
    atualizarAnimalDaPrevia($link, $anterior, $novos);
    $gruposDepois = compararAnimaisPrevia($registros, mysqli_fetch_all(mysqli_query($link, 'SELECT * FROM animais'), MYSQLI_ASSOC));
    exigirAtualizacao(count($gruposDepois['cadastrados']) === 1 && !$gruposDepois['possiveis_atualizacoes'], 'Cadastro atualizado não foi reconhecido após a gravação.');
    $salvo = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1'));
    foreach ($novos as $campo => $valor) { exigirAtualizacao($salvo[$campo] === $valor, 'Valor não foi gravado: ' . $campo); }
    exigirAtualizacao($salvo['sexo'] === 'Fêmea', 'Campo fora do escopo alterado.');
    rejeitarAtualizacao(function () use ($link, $anterior, $novos) { atualizarAnimalDaPrevia($link, $anterior, $novos); });
    $conflito = $novos; $conflito['nome'] = 'OUTRO';
    atualizarAnimalDaPrevia($link, $salvo, $conflito);
    $homonimo = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1'));
    exigirAtualizacao($homonimo['nome'] === 'OUTRO', 'Atualização deve permitir nome igual ao de outro animal.');
    atualizarAnimalDaPrevia($link, $homonimo, $novos);
    $conflitoFbb = $novos; $conflitoFbb['fbb'] = 'F2';
    rejeitarAtualizacao(function () use ($link, $salvo, $conflitoFbb) { atualizarAnimalDaPrevia($link, $salvo, $conflitoFbb); });
    $depois = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1'));
    exigirAtualizacao($salvo === $depois, 'Falha não preservou os dados anteriores.');
    exigirAtualizacao(mysqli_fetch_assoc(mysqli_query($link, 'SELECT nome FROM animais WHERE id=2'))['nome'] === 'OUTRO', 'Animal não selecionado foi alterado.');
    // Também testa rollback com truncamento quando o servidor não usa modo estrito.
    $modo = mysqli_fetch_assoc(mysqli_query($link, 'SELECT @@SESSION.sql_mode AS modo'))['modo'];
    mysqli_query($link, "SET SESSION sql_mode = ''");
    $excesso = $novos; $excesso['nome'] = str_repeat('X', 81);
    rejeitarAtualizacao(function () use ($link, $salvo, $excesso) { atualizarAnimalDaPrevia($link, $salvo, $excesso); });
    exigirAtualizacao(mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1')) === $salvo, 'Truncamento não foi desfeito.');
    mysqli_query($link, "SET SESSION sql_mode = '" . mysqli_real_escape_string($link, $modo) . "'");
    $marcarMorto = $novos; $marcarMorto['status'] = 1;
    rejeitarAtualizacao(function () use ($link, $salvo, $marcarMorto) { atualizarAnimalDaPrevia($link, $salvo, $marcarMorto); });
    $salvo = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1'));
    exigirAtualizacao((int)$salvo['status'] === 0, 'Situação não pode ser modificada pela atualização.');
    $segundo = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=2'));
    $novoPrimeiro = $novos; $novoPrimeiro['fbb'] = 'LOTE1';
    $novoSegundo = array('nome' => 'NOVO SEGUNDO', 'fbb' => 'LOTE2', 'tatuagem' => 'T1', 'data_de_nascimento' => '2021-01-01');
    $loteTeste = array(
        array('anterior' => $salvo, 'novos' => $novoPrimeiro),
        array('anterior' => array_merge($segundo, array('nome' => 'DESATUALIZADO')), 'novos' => $novoSegundo)
    );
    rejeitarAtualizacao(function () use ($link, $loteTeste) { atualizarLoteDaPrevia($link, $loteTeste); });
    exigirAtualizacao(mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1')) === $salvo, 'Falha no segundo animal deve desfazer o primeiro.');
    $loteTeste[1]['anterior'] = $segundo;
    atualizarLoteDaPrevia($link, $loteTeste);
    exigirAtualizacao(mysqli_fetch_assoc(mysqli_query($link, 'SELECT fbb FROM animais WHERE id=1'))['fbb'] === 'LOTE1', 'Primeiro animal do lote não atualizado.');
    exigirAtualizacao(mysqli_fetch_assoc(mysqli_query($link, 'SELECT fbb FROM animais WHERE id=2'))['fbb'] === 'LOTE2', 'Segundo animal do lote não atualizado.');
    $antesObito = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1'));
    $dadosObito = array('Nome' => 'MoRtO99', 'FBB/FBE' => $antesObito['fbb'], 'Tat.' => 'MoRtO99', 'Nasc.' => '29/02/2024', 'Situação' => 'Vivo');
    atualizarAnimalDaPrevia($link, $antesObito, dadosAtualizacaoPrevia($dadosObito));
    $aposObito = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1'));
    exigirAtualizacao($aposObito['status'] === $antesObito['status'] && $aposObito['causa_da_perda'] === 'Nascimento' && $aposObito['data_de_saida'] === '2024-02-29', 'Morte no nascimento não persistiu na atualização.');
    $propostaParcial = $aposObito;
    $propostaParcial['nome'] = 'NOME SELECIONADO';
    $propostaParcial['status'] = 0;
    $propostaParcial['fbb'] = 'NAO APLICAR';
    $propostaParcial['data_de_nascimento'] = '2020-01-01';
    atualizarAnimalDaPrevia($link, $aposObito, selecionarCamposAtualizacaoPrevia($propostaParcial, array('nome')));
    $aposParcial = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=1'));
    $esperadoParcial = $aposObito; $esperadoParcial['nome'] = 'NOME SELECIONADO';
    exigirAtualizacao($aposParcial === $esperadoParcial, 'Campos desmarcados foram alterados.');
    rejeitarAtualizacao(function () use ($link, $aposParcial) { atualizarAnimalDaPrevia($link, $aposParcial, array()); });
    $antesFbb = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=2'));
    $registroFbb = array('linha' => 8, 'problemas' => array(), 'dados' => array(
        'Nome' => $antesFbb['nome'], 'FBB/FBE' => 'FBB-CORRIGIDO', 'Tat.' => $antesFbb['tatuagem'],
        'Nasc.' => (new DateTimeImmutable($antesFbb['data_de_nascimento']))->format('d/m/Y')
    ));
    $sugestaoFbb = compararAnimaisPrevia(array($registroFbb), array($antesFbb));
    exigirAtualizacao(count($sugestaoFbb['possiveis_atualizacoes']) === 1, 'Coincidência por nome/tatuagem não permitiu corrigir FBB.');
    $contextoFbb = array('versao' => 3, 'banco' => DB_DATABASE, 'login' => 'teste', 'envio' => 'fbb', 'possiveis' => $sugestaoFbb['possiveis_atualizacoes']);
    list($anteriorFbb, $novosFbb) = selecionarAtualizacaoPrevia($contextoFbb, 'fbb', 8, 2, DB_DATABASE, 'teste');
    atualizarAnimalDaPrevia($link, $anteriorFbb, selecionarCamposAtualizacaoPrevia($novosFbb, array('fbb')));
    $depoisFbb = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id=2'));
    $esperadoFbb = $antesFbb; $esperadoFbb['fbb'] = 'FBB-CORRIGIDO';
    exigirAtualizacao($depoisFbb === $esperadoFbb, 'Correção do FBB alterou outros campos.');
    echo "Sugestão por nome/tatuagem e atualização somente do FBB: OK\n";
    echo "Seleção de campos: grava somente nome e preserva situação, FBB e demais dados: OK\n";
    echo "Lote: seleção sem ambiguidades, gravação conjunta e rollback integral: OK\n";
    echo "Fluxo XLS → comparação → seleção → gravação → cadastro reconhecido: OK\n";
    echo "MySQL em tabela temporária: quatro campos, apóstrofo, zeros, conflito, concorrência e rollback: OK\n";
} finally { DBClose($link); }
