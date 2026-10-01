<?php
require_once __DIR__ . '/../animal/importacao/cadastrar_animal.php';
function planoCadastroTeste($registros, $linha, $animais, $terceiros, $raca = 'Dorper') {
    return planejarCadastroPrevia($registros, $linha, $animais, $terceiros, $raca);
}
function exigirCadastro($condicao, $mensagem) { if (!$condicao) { throw new RuntimeException($mensagem); } }
function rejeitarCadastro(callable $acao) {
    try { $acao(); } catch (RuntimeException $erro) { return; }
    throw new RuntimeException('Cadastro inválido foi aceito.');
}
function registroCadastro($linha, $nome, $sexo, $pai = '', $mae = '', $nasc = '01/01/2020') {
    return array('linha' => $linha, 'problemas' => array(), 'dados' => array(
        'Nome' => $nome, 'FBB/FBE' => 'FBB' . $linha, 'Tat.' => tatuagemNomeRebanho($nome),
        'Nasc.' => $nasc, 'Sexo' => $sexo, 'Pai' => $pai, 'Mãe' => $mae
    ));
}
$filho = registroCadastro(2, "FILHO D'ÁGUA 0001", 'Macho', 'PAI P001', 'MÃE M001');
$pai = registroCadastro(3, 'PAI P001', 'Macho', 'AVÔ A001', 'AVÓ A002', '01/01/2018');
$mae = registroCadastro(4, 'MÃE M001', 'Fêmea', '', '', '01/01/2018');
$registros = array($filho, $pai, $mae);
$registros[0]['dados']['Situação'] = 'Vivo';
$registros[1]['dados']['Situação'] = 'Morto';
$registros[2]['dados']['Situação'] = 'Vivo';
$plano = planoCadastroTeste($registros, 2, array(), array());
foreach ($plano as $op) { exigirCadastro($op['dados']['raca'] === 'Dorper', 'Raça do perfil não aplicada a todos os cadastros.'); }
rejeitarCadastro(function () use ($registros) { planoCadastroTeste($registros, 2, array(), array(), ''); });
exigirCadastro(array_column($plano, 'chave') === array('terceiro:AVÔ A001', 'terceiro:AVÓ A002', 'linha:3', 'linha:4', 'linha:2'), 'Ordem de dependências incorreta.');
exigirCadastro(planoCadastroTeste($registros, 'todos', array(), array()) === $plano, 'Lote deve compartilhar as dependências e não cadastrar pais duas vezes.');
exigirCadastro($plano[4]['dados']['tatuagem'] === '0001', 'Zeros da tatuagem perdidos.');
$existente = array('id' => 10, 'nome' => 'PAI P001', 'sexo' => 'Macho', 'fbb' => 'FBB3', 'tatuagem' => 'P001', 'data_de_nascimento' => '2018-01-01');
$terceira = array('id' => 11, 'nome' => 'MÃE M001', 'sexo' => 'Fêmea', 'fbb' => '', 'tatuagem' => 'M001');
$soFilho = planoCadastroTeste($registros, 2, array($existente), array($terceira));
exigirCadastro(count($soFilho) === 1 && $soFilho[0]['pai']['id'] === 10 && $soFilho[0]['mae']['origem'] === 'terceiros', 'Pais existentes não foram reutilizados.');
$compartilhados = $registros;
$compartilhados[2]['dados']['Pai'] = 'AVÔ A001';
$compartilhados[2]['dados']['Mãe'] = 'AVÓ A002';
exigirCadastro(count(planoCadastroTeste($compartilhados, 2, array(), array())) === 5, 'Ancestrais compartilhados duplicados.');
$duplicado = $registros; $duplicado[] = array_merge($pai, array('linha' => 5));
rejeitarCadastro(function () use ($duplicado) { planoCadastroTeste($duplicado, 2, array(), array()); });
$ciclo = $registros; $ciclo[1]['dados']['Pai'] = $filho['dados']['Nome'];
rejeitarCadastro(function () use ($ciclo) { planoCadastroTeste($ciclo, 2, array(), array()); });
$errado = $existente; $errado['sexo'] = 'Fêmea';
rejeitarCadastro(function () use ($registros, $errado) { planoCadastroTeste($registros, 2, array($errado), array()); });
rejeitarCadastro(function () use ($registros, $existente) { planoCadastroTeste($registros, 2, array($existente, array_merge($existente, array('id' => 20))), array()); });
$colisao = $registros; $colisao[1]['dados']['FBB/FBE'] = $filho['dados']['FBB/FBE'];
rejeitarCadastro(function () use ($colisao) { planoCadastroTeste($colisao, 2, array(), array()); });
$invalido = $registros; $invalido[1]['dados']['Nasc.'] = '31/02/2020';
rejeitarCadastro(function () use ($invalido) { planoCadastroTeste($invalido, 2, array(), array()); });
$validoAntes = registroCadastro(20, 'VALIDO V020', 'Macho');
$validoDepois = registroCadastro(21, 'VALIDO V021', 'Fêmea');
$inconsistente = registroCadastro(22, 'INVALIDO I022', 'Macho', '-', '-');
$ignorados = array();
$parcial = planejarCadastroPrevia(array($validoAntes, $inconsistente, $validoDepois), 'todos', array(), array(), 'Dorper', $ignorados);
exigirCadastro(array_column($parcial, 'linha') === array(20, 21), 'Animal inválido não pode bloquear os anteriores ou seguintes nem deixar terceiros órfãos.');
exigirCadastro(count($ignorados) === 1 && $ignorados[0]['linha'] === 22 && strpos($ignorados[0]['motivo'], 'pai e mãe') !== false, 'Informar animal ignorado e motivo.');
exigirCadastro(planejarCadastroPrevia(array($inconsistente), 'todos', array(), array(), 'Dorper', $ignorados) === array() && count($ignorados) === 1, 'Lote totalmente inválido deve retornar somente os motivos.');
$parcial = planejarCadastroPrevia(array_merge($invalido, array($validoDepois)), 'todos', array(), array(), 'Dorper', $ignorados);
exigirCadastro(array_column($parcial, 'linha') === array(4, 21) && count($ignorados) === 2, 'Parente inválido deve ignorar dependentes e permitir os independentes.');
$parcial = planejarCadastroPrevia(array_merge($ciclo, array($validoDepois)), 'todos', array(), array(), 'Dorper', $ignorados);
exigirCadastro(array_column($parcial, 'linha') === array(4, 21), 'Ciclos não podem contaminar o próximo animal.');
$parcial = planejarCadastroPrevia(array_merge($colisao, array($validoDepois)), 'todos', array(), array(), 'Dorper', $ignorados);
exigirCadastro(in_array(21, array_column($parcial, 'linha'), true) && !in_array(2, array_column($parcial, 'linha'), true), 'Colisão com dependência deve ignorar o filho e continuar.');
$duplicadoFila = $validoDepois; $duplicadoFila['dados']['FBB/FBE'] = $validoAntes['dados']['FBB/FBE'];
$parcial = planejarCadastroPrevia(array($validoAntes, $duplicadoFila), 'todos', array(), array(), 'Dorper', $ignorados);
exigirCadastro(array_column($parcial, 'linha') === array(20) && count($ignorados) === 1, 'FBB duplicado na fila deve preservar o primeiro válido.');
echo "Planejamento: dependências, terceiros, referências existentes, compartilhamento, ciclos e validação: OK\n";
if (($argv[1] ?? '') !== '--mysql') { exit; }
$databaseName = $argv[2] ?? (getenv('DB_DATABASE') ?: 'siste870_sob');
require __DIR__ . '/../mysqli/environment.php';
require __DIR__ . '/../mysqli/_conexao.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = DBConnect();
try {
    // SHOW CREATE TABLE replica o esquema real. As tabelas temporárias ocultam as reais só nesta conexão.
    foreach (array('animais', 'terceiros', 'matriz', 'reprodutor') as $tabela) {
        $schema = mysqli_fetch_assoc(mysqli_query($link, "SHOW CREATE TABLE $tabela"))['Create Table'];
        mysqli_query($link, preg_replace('/^CREATE TABLE/', 'CREATE TEMPORARY TABLE', $schema));
    }
    mysqli_query($link, 'CREATE TEMPORARY TABLE admin (id INT PRIMARY KEY, raca VARCHAR(30))');
    mysqli_query($link, "INSERT INTO admin VALUES (1, 'Dorper')");
    $listas = consultarCadastroPrevia($link);
    exigirCadastro(!$listas['animais'] && !$listas['terceiros'], 'Fixtures não estão vazias.');
    mysqli_query($link, "UPDATE admin SET raca = 'Santa Inês'");
    rejeitarCadastro(function () use ($link, $registros, $plano) { confirmarCadastroPrevia($link, $registros, 2, $plano); });
    mysqli_query($link, "UPDATE admin SET raca = 'Dorper'");
    $ids = confirmarCadastroPrevia($link, $registros, 2, $plano);
    foreach (array('animais', 'terceiros') as $tabela) {
        exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS total FROM $tabela WHERE raca <> 'Dorper'"))['total'] === 0, 'Raça incorreta gravada.');
    }
    $cadastrado = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id = ' . (int)$ids['linha:2']));
    exigirCadastro((int)$cadastrado['pai'] === $ids['linha:3'] && (int)$cadastrado['mae'] === $ids['linha:4'], 'IDs dos pais incorretos.');
    exigirCadastro($cadastrado['nome'] === $filho['dados']['Nome'] && $cadastrado['tatuagem'] === '0001', 'Nome, acentos ou zeros não preservados.');
    $paiSalvo = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id = ' . (int)$ids['linha:3']));
    exigirCadastro((int)$paiSalvo['status'] === 1 && (int)$cadastrado['status'] === 0, 'Situação vivo/morto não persistida.');
    exigirCadastro((int)$paiSalvo['terceiro_pai'] === 1 && (int)$paiSalvo['pai'] === $ids['terceiro:AVÔ A001'], 'Terceiro pai não vinculado.');
    exigirCadastro((int)$paiSalvo['terceiro_mae'] === 1 && (int)$paiSalvo['mae'] === $ids['terceiro:AVÓ A002'], 'Terceira mãe não vinculada.');
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, 'SELECT qtd_crias FROM reprodutor'))['qtd_crias'] === 1, 'Ranking do pai não atualizado.');
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, 'SELECT qtd_crias FROM matriz'))['qtd_crias'] === 1, 'Ranking da mãe não atualizado.');
    rejeitarCadastro(function () use ($link, $registros, $plano) { confirmarCadastroPrevia($link, $registros, 2, $plano); });
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, 'SELECT COUNT(*) AS total FROM animais'))['total'] === 3, 'Reenvio duplicou cadastros.');
    $outro = array(registroCadastro(6, 'FILHO F002', 'Fêmea', 'PAI P001', 'AVÓ A002'));
    $listas = consultarCadastroPrevia($link);
    $planoOutro = planoCadastroTeste($outro, 6, $listas['animais'], $listas['terceiros']);
    exigirCadastro(count($planoOutro) === 1, 'Parentes existentes duplicados.');
    $idsOutro = confirmarCadastroPrevia($link, $outro, 6, $planoOutro);
    $filha = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id = ' . (int)$idsOutro['linha:6']));
    exigirCadastro((int)$filha['terceiro_mae'] === 1 && (int)$filha['mae'] === $ids['terceiro:AVÓ A002'], 'Terceira existente não reutilizada.');
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, 'SELECT qtd_crias FROM reprodutor'))['qtd_crias'] === 2, 'Contagem de crias incorreta.');
    $pendente = array(registroCadastro(7, 'PENDENTE F003', 'Macho', 'PAI P001', 'AVÓ A002'));
    $listas = consultarCadastroPrevia($link);
    $planoPendente = planoCadastroTeste($pendente, 7, $listas['animais'], $listas['terceiros']);
    mysqli_query($link, "UPDATE animais SET nome = 'PAI ALTERADO P001' WHERE id = " . (int)$ids['linha:3']);
    rejeitarCadastro(function () use ($link, $pendente, $planoPendente) { confirmarCadastroPrevia($link, $pendente, 7, $planoPendente); });
    // Falha depois de criar os terceiros: todos devem ser desfeitos.
    mysqli_query($link, 'ALTER TABLE animais ADD CONSTRAINT teste_nome_cadastro CHECK (nome <> \'FALHA F004\')');
    $falha = array(registroCadastro(8, 'FALHA F004', 'Macho', 'NOVO PAI P099', 'NOVA MÃE M099'));
    $listas = consultarCadastroPrevia($link);
    $planoFalha = planoCadastroTeste($falha, 8, $listas['animais'], $listas['terceiros']);
    rejeitarCadastro(function () use ($link, $falha, $planoFalha) { confirmarCadastroPrevia($link, $falha, 8, $planoFalha); });
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, 'SELECT COUNT(*) AS total FROM terceiros'))['total'] === 2, 'Falha deixou terceiros gravados.');
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, 'SELECT COUNT(*) AS total FROM animais'))['total'] === 4, 'Falha deixou animal gravado.');
    $irmaos = array(
        registroCadastro(9, 'LOTE L009', 'Macho', 'NOVO PAI P011', 'NOVA MÃE M011'),
        registroCadastro(10, 'LOTE L009', 'Fêmea', 'NOVO PAI P011', 'NOVA MÃE M011')
    );
    $listas = consultarCadastroPrevia($link);
    $planoTodos = planoCadastroTeste($irmaos, 'todos', $listas['animais'], $listas['terceiros']);
    exigirCadastro(count($planoTodos) === 4, 'Lote não compartilhou terceiros.');
    $idsTodos = confirmarCadastroPrevia($link, $irmaos, 'todos', $planoTodos);
    exigirCadastro(count($idsTodos) === 4, 'Lote não cadastrado integralmente.');
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS total FROM animais WHERE nome = 'LOTE L009'"))['total'] === 2, 'Nomes iguais com FBBs distintos não foram cadastrados.');
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS total FROM animais WHERE tatuagem = 'L009'"))['total'] === 2, 'Tatuagens iguais devem ser aceitas no lote.');
    $mesmaTat = array(registroCadastro(13, 'LOTE L009', 'Macho'));
    $listas = consultarCadastroPrevia($link);
    exigirCadastro(count(planoCadastroTeste($mesmaTat, 13, $listas['animais'], $listas['terceiros'])) === 1, 'Tatuagem já existente bloqueou novo cadastro.');
    $loteFalha = array(registroCadastro(11, 'LOTE L011', 'Macho'), registroCadastro(12, 'FALHA F004', 'Macho'));
    $listas = consultarCadastroPrevia($link);
    $planoLoteFalha = planoCadastroTeste($loteFalha, 'todos', $listas['animais'], $listas['terceiros']);
    rejeitarCadastro(function () use ($link, $loteFalha, $planoLoteFalha) { confirmarCadastroPrevia($link, $loteFalha, 'todos', $planoLoteFalha); });
    exigirCadastro((int)mysqli_fetch_assoc(mysqli_query($link, 'SELECT COUNT(*) AS total FROM animais'))['total'] === 6, 'Falha do lote não reverteu o primeiro animal.');
    $obito = array(registroCadastro(14, 'morto14', 'Macho'));
    $listas = consultarCadastroPrevia($link);
    $planoObito = planoCadastroTeste($obito, 14, $listas['animais'], $listas['terceiros']);
    $idsObito = confirmarCadastroPrevia($link, $obito, 14, $planoObito);
    $salvoObito = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM animais WHERE id = ' . (int)$idsObito['linha:14']));
    exigirCadastro((int)$salvoObito['status'] === 1 && $salvoObito['causa_da_perda'] === 'Nascimento' && $salvoObito['data_de_saida'] === $salvoObito['data_de_nascimento'], 'Morte no nascimento não persistiu no cadastro.');
    echo "Cadastrar todos: dependências compartilhadas, gravação e rollback integral: OK\n";
    echo "MySQL: esquema real temporário, cadastro ordenado, IDs, terceiros, indicadores, reenvio, plano obsoleto e rollback: OK\n";
} finally { DBClose($link); }
