<?php
require_once __DIR__ . '/../animal/importacao/cadastrar_animal.php';
function exigirDuas($condicao, $mensagem) { if (!$condicao) { throw new RuntimeException($mensagem); } }
function rejeitarDuas(callable $acao) {
    try { $acao(); } catch (RuntimeException $erro) { return; }
    throw new RuntimeException('Importação inválida foi aceita.');
}
function arquivoDuas(array $linhas) {
    $html = '<table><tr>';
    foreach (colunasArquivoRebanho() as $coluna) { $html .= '<th>' . htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') . '</th>'; }
    $html .= '</tr>';
    foreach ($linhas as $linha) {
        $html .= '<tr>';
        foreach ($linha as $valor) { $html .= '<td>' . htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') . '</td>'; }
        $html .= '</tr>';
    }
    return $html . '</table>';
}
$vivos = lerConsultaRebanho(arquivoDuas(array(array('V001', 'FILHO 001', '01/01/2024', 'Macho', 'PAI 001', ''))));
$mortos = lerConsultaRebanho(arquivoDuas(array(array('M001', 'PAI 001', '01/01/2020', 'Macho', '', ''))));
$unidos = unirPlanilhasRebanho($vivos, $mortos);
exigirDuas(count($unidos) === 2 && $unidos[0]['linha'] !== $unidos[1]['linha'], 'Linhas dos dois arquivos colidiram.');
exigirDuas($unidos[0]['dados']['Situação'] === 'Vivo' && $unidos[1]['dados']['Situação'] === 'Morto', 'Situações de origem incorretas.');
$plano = planejarCadastroPrevia($unidos, 'todos', array(), array(), 'Dorper');
exigirDuas(count($plano) === 2 && $plano[0]['dados']['nome'] === 'PAI 001' && $plano[0]['dados']['status'] === 1 && $plano[1]['dados']['status'] === 0, 'Pai morto da outra planilha não foi planejado antes do filho.');
exigirDuas($plano[1]['pai']['chave'] === $plano[0]['chave'], 'Filho não referencia pai da outra planilha.');
$existente = array_merge($plano[0]['dados'], array('id' => 10, 'status' => 0));
$grupos = compararAnimaisPrevia(array($unidos[1]), array($existente));
exigirDuas(count($grupos['cadastrados']) === 1 && !$grupos['possiveis_atualizacoes'], 'Situação diferente não deve sugerir atualização.');
exigirDuas(!array_filter(divergenciasAnimalPrevia($unidos[1]['dados'], $existente)), 'Situação não deve entrar na comparação de atualização.');
exigirDuas(!array_key_exists('status', dadosAtualizacaoPrevia($unidos[1]['dados'])), 'Atualização não deve incluir situação.');
rejeitarDuas(function () use ($vivos) { unirPlanilhasRebanho($vivos, $vivos); });
$mesmoFbb = $mortos; $mesmoFbb[0]['dados']['FBB/FBE'] = 'V001';
rejeitarDuas(function () use ($vivos, $mesmoFbb) { unirPlanilhasRebanho($vivos, $mesmoFbb); });
$mesmoNome = $mortos; $mesmoNome[0]['dados']['Nome'] = ' filho   001 ';
exigirDuas(count(unirPlanilhasRebanho($vivos, $mesmoNome)) === 2, 'Nomes iguais com FBBs distintos devem ser aceitos entre listas.');
exigirDuas(lerConsultaRebanho(arquivoDuas(array()), true) === array(), 'Lista sem animais com cabeçalho deve ser aceita.');
exigirDuas(count(unirPlanilhasRebanho($vivos, array())) === 1, 'Lista de mortos vazia bloqueou vivos.');
rejeitarDuas(function () { unirPlanilhasRebanho(array(), array()); });
rejeitarDuas(function () { lerArquivosRebanho(array()); });
rejeitarDuas(function () { lerArquivosRebanho(array('planilha_vivos' => array('error' => UPLOAD_ERR_OK))); });
rejeitarDuas(function () { lerArquivosRebanho(array('planilha_mortos' => array('error' => UPLOAD_ERR_OK))); });
echo "Duas planilhas: ambas obrigatórias, situações, parentesco cruzado, IDs únicos e conflitos: OK\n";

$nomesMortos = lerConsultaRebanho(arquivoDuas(array(
    array('MX1', 'MORTO', '01/01/2020', 'Macho', '', ''),
    array('MX2', 'animal morto', '01/01/2020', 'Fêmea', '', '')
)));
$nomesVivos = lerConsultaRebanho(arquivoDuas(array(array('VX1', 'Morto', '01/01/2024', 'Macho', '', ''))));
$numerados = unirPlanilhasRebanho($nomesVivos, $nomesMortos);
exigirDuas(array_column(array_column($numerados, 'dados'), 'Nome') === array('MORTO1', 'MORTO2', 'MORTO3'), 'Numeração deve ser única e sem espaços nas duas listas.');
exigirDuas(array_column(array_column($numerados, 'dados'), 'Tat.') === array('MORTO1', 'MORTO2', 'MORTO3'), 'Tatuagem deve manter morto junto ao número.');
exigirDuas(numerarNomesMortosPrevia($numerados) === $numerados, 'Recarregar não deve alterar a numeração.');
$antigos = $numerados;
foreach ($antigos as $i => &$antigo) { $antigo['dados']['Nome'] = 'morto ' . ($i + 1); $antigo['dados']['Tat.'] = (string)($i + 1); }
unset($antigo);
exigirDuas(numerarNomesMortosPrevia($antigos) === $numerados, 'Prévia antiga não foi migrada para nome e tatuagem juntos.');
$planoNumerado = planejarCadastroPrevia($numerados, 'todos', array(), array(), 'Dorper');
exigirDuas($planoNumerado[0]['dados']['tatuagem'] === 'MORTO1' && $planoNumerado[2]['dados']['tatuagem'] === 'MORTO3', 'Cadastro não preservou tatuagem numerada.');
echo "Nomes e tatuagens morto1, morto2, morto3; sequência única e estável: OK\n";

foreach ($planoNumerado as $op) {
    exigirDuas($op['dados']['status'] === 1 && $op['dados']['causa_da_perda'] === 'Nascimento' && $op['dados']['data_de_saida'] === $op['dados']['data_de_nascimento'], 'Nome morto deve indicar morte no nascimento.');
}
$cadastroMorto = array_merge($planoNumerado[0]['dados'], array('id' => 25, 'causa_da_perda' => '', 'data_de_saida' => null));
$gruposMorte = compararAnimaisPrevia(array($numerados[0]), array($cadastroMorto));
exigirDuas(count($gruposMorte['possiveis_atualizacoes']) === 1, 'Causa ausente deve aparecer para atualização.');
$cadastroMorto['causa_da_perda'] = 'Nascimento'; $cadastroMorto['data_de_saida'] = $cadastroMorto['data_de_nascimento'];
exigirDuas(count(compararAnimaisPrevia(array($numerados[0]), array($cadastroMorto))['cadastrados']) === 1, 'Cadastro corrigido não reconhecido.');
echo "Nomes contendo morto: situação, causa e data de morte no nascimento: OK\n";

$prefixados = numerarNomesMortosPrevia($numerados, '  Buria  ');
exigirDuas(array_column(array_column($prefixados, 'dados'), 'Nome') === array('BURIA MORTO1', 'BURIA MORTO2', 'BURIA MORTO3'), 'Prefixo da fazenda em maiúsculas ausente.');
exigirDuas(array_column(array_column($prefixados, 'dados'), 'Tat.') === array('MORTO1', 'MORTO2', 'MORTO3'), 'Prefixo não deve fazer parte da tatuagem.');
exigirDuas(numerarNomesMortosPrevia($prefixados, 'BURIA') === $prefixados, 'Prefixo duplicado ao recarregar.');
echo "Prefixo do perfil, nomes maiúsculos e tatuagem MORTO sequencial: OK\n";
