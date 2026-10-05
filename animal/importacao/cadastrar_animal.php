<?php
require_once __DIR__ . '/leitor_consulta_rebanho.php';
require_once __DIR__ . '/comparar_rebanho.php';
require_once __DIR__ . '/atualizar_animal.php';

function dadosNovoAnimalPrevia(array $dados, $raca)
{
    $dados['Tat.'] = tatuagemNomeRebanho($dados['Nome'] ?? '');
    $novos = dadosAtualizacaoPrevia($dados);
    $situacao = $dados['Situação'] ?? 'Vivo';
    if (!in_array($situacao, array('Vivo', 'Morto'), true)) { throw new RuntimeException('Situação inválida para o cadastro.'); }
    $novos['status'] = $situacao === 'Morto' || morteNascimentoNomePrevia($novos['nome']) ? 1 : 0;
    if (!in_array($dados['Sexo'] ?? '', array('Macho', 'Fêmea'), true)) {
        throw new RuntimeException('Sexo inválido para ' . $novos['nome'] . '.');
    }
    return array_merge($novos, array(
        'sexo' => $dados['Sexo'], 'raca' => $raca, 'data_de_entrada' => null,
        'observacoes' => '', 'entrada' => 2, 'tipo_reproducao' => '',
        'link_fbb' => '', 'fbb_img' => '', 'peso2' => 0, 'parcelas' => 0,
        'tipo_venda' => '', 'tipo' => 0, 'confirmacao' => 0, 'prolapso' => '',
        'criador' => '', 'status' => $novos['status'] ?? 0, 'receptora' => '', 'chip' => ''
    ));
}

/** Plano sem escrita, em ordem de dependência; os IDs novos só existem na confirmação. */
function planejarCadastroPrevia(array $registros, $linha, array $animais, array $terceiros, $raca, &$ignorados = array())
{
    $ignorados = array();
    $raca = trim((string)$raca);
    if ($raca === '' || mb_strlen($raca, 'UTF-8') > 30) {
        throw new RuntimeException('Informe uma raça válida no perfil da fazenda antes de cadastrar pela importação.');
    }
    $porLinha = $porNome = $indices = $fbbsBanco = array();
    foreach ($registros as $registro) {
        $porLinha[(int)$registro['linha']] = $registro;
        $porNome[identificadorAnimalPrevia($registro['dados']['Nome'] ?? '')][] = $registro;
    }
    foreach (array('animais' => $animais, 'terceiros' => $terceiros) as $origem => $lista) {
        foreach ($lista as $animal) {
            $indices[$origem][identificadorAnimalPrevia($animal['nome'])][] = $animal;
            if ($origem === 'animais') $fbbsBanco[identificadorAnimalPrevia($animal['fbb'] ?? '')] = true;
        }
    }
    if ($linha === 'todos') {
        $grupos = compararAnimaisPrevia($registros, $animais);
        $selecionados = $grupos['nao_cadastrados'];
        if (!$selecionados) { throw new RuntimeException('Nenhum animal disponível para cadastro.'); }
    } else {
        if (!isset($porLinha[$linha])) { throw new RuntimeException('Linha não encontrada na prévia.'); }
        $selecionados = array($porLinha[$linha]);
    }
    $operacoes = $visitando = $resolvidos = $fbbsPlano = array();
    $resolverRegistro = null;
    $resolverParente = function ($nome, $sexo) use (&$resolverRegistro, &$operacoes, &$resolvidos, $indices, $porNome, $raca) {
        $nome = textoConsultaRebanho((string)$nome);
        if ($nome === '') { return null; }
        $chave = identificadorAnimalPrevia($nome);
        $linhasComNome = $porNome[$chave] ?? array();
        if (count($linhasComNome) > 1) { throw new RuntimeException('Parentesco ambíguo: mais de um animal da planilha tem o nome ' . $nome . '. Revise o pai ou a mãe.'); }
        foreach (array('animais', 'terceiros') as $origem) {
            $encontrados = $indices[$origem][$chave] ?? array();
            if (count($encontrados) > 1) { throw new RuntimeException('Mais de um cadastro encontrado para ' . $nome . '. Revise antes de cadastrar.'); }
            if ($encontrados) {
                $animal = $encontrados[0];
                if ($origem === 'terceiros' && isset($animal['ativo']) && !(int)$animal['ativo']) {
                    throw new RuntimeException('O terceiro ' . $nome . ' está inativo. Ative o cadastro antes de utilizá-lo como parente.');
                }
                if ($linhasComNome && identificadorAnimalPrevia($animal['fbb'] ?? '') !== '' && identificadorAnimalPrevia($animal['fbb'] ?? '') !== identificadorAnimalPrevia($linhasComNome[0]['dados']['FBB/FBE'] ?? '')) {
                    throw new RuntimeException('Parentesco ambíguo: ' . $nome . ' tem FBB diferente no banco e na planilha. Revise o pai ou a mãe.');
                }
                if (($animal['sexo'] ?? '') !== $sexo) { throw new RuntimeException('Sexo do parente ' . $nome . ' incompatível com o vínculo.'); }
                return array('origem' => $origem, 'id' => (int)$animal['id'], 'nome' => $animal['nome']);
            }
        }
        $linhas = $porNome[$chave] ?? array();
        if (count($linhas) > 1) { throw new RuntimeException('Parentesco ambíguo: mais de um animal da planilha tem o nome ' . $nome . '. Revise o pai ou a mãe antes de cadastrar.'); }
        if ($linhas) {
            if (($linhas[0]['dados']['Sexo'] ?? '') !== $sexo) { throw new RuntimeException('Sexo na planilha incompatível para ' . $nome . '.'); }
            return $resolverRegistro($linhas[0]);
        }
        $key = 'terceiro:' . $chave;
        if (isset($resolvidos[$key])) {
            if ($operacoes[$key]['dados']['sexo'] !== $sexo) { throw new RuntimeException('O mesmo animal foi informado como pai e mãe: ' . $nome . '.'); }
            return $resolvidos[$key];
        }
        $tat = tatuagemNomeRebanho($nome);
        if (mb_strlen($nome, 'UTF-8') > 100 || mb_strlen($tat, 'UTF-8') > 20) { throw new RuntimeException('Nome ou tatuagem do terceiro excede o limite: ' . $nome . '.'); }
        $operacoes[$key] = array('chave' => $key, 'origem' => 'terceiros', 'dados' => array(
            'nome' => $nome, 'sexo' => $sexo, 'tatuagem' => $tat, 'fbb' => '', 'raca' => $raca, 'pai' => '', 'mae' => ''
        ));
        return $resolvidos[$key] = array('origem' => 'terceiros', 'chave' => $key, 'nome' => $nome);
    };
    $resolverRegistro = function ($registro) use (&$resolverRegistro, $resolverParente, &$operacoes, &$visitando, &$resolvidos, &$fbbsPlano, $fbbsBanco, $raca) {
        $key = 'linha:' . $registro['linha'];
        if (isset($visitando[$key])) { throw new RuntimeException('Ciclo de parentesco encontrado em ' . $registro['dados']['Nome'] . '.'); }
        if (isset($resolvidos[$key])) { return $resolvidos[$key]; }
        if (!empty($registro['problemas'])) { throw new RuntimeException('Revise a linha de ' . $registro['dados']['Nome'] . ': ' . implode(' ', $registro['problemas'])); }
        $novos = dadosNovoAnimalPrevia($registro['dados'], $raca);
        $fbb = identificadorAnimalPrevia($novos['fbb']);
        if (isset($fbbsBanco[$fbb])) throw new RuntimeException('Já existe um possível cadastro para ' . $novos['nome'] . '. Revise as correspondências antes de cadastrar.');
        if (isset($fbbsPlano[$fbb])) throw new RuntimeException('FBB repetido entre animais a cadastrar: ' . $novos['nome'] . '.');
        $visitando[$key] = true;
        $pai = $resolverParente($registro['dados']['Pai'] ?? '', 'Macho');
        $mae = $resolverParente($registro['dados']['Mãe'] ?? '', 'Fêmea');
        if (isset($fbbsPlano[$fbb])) throw new RuntimeException('FBB repetido entre animais do cadastro. Revise a planilha.');
        $fbbsPlano[$fbb] = true;
        unset($visitando[$key]);
        $operacoes[$key] = array('chave' => $key, 'origem' => 'animais', 'linha' => (int)$registro['linha'], 'dados' => $novos, 'pai' => $pai, 'mae' => $mae);
        return $resolvidos[$key] = array('origem' => 'animais', 'chave' => $key, 'nome' => $novos['nome']);
    };
    foreach ($selecionados as $registro) {
        // Desfaz também parentes preparados exclusivamente para uma linha inválida.
        $antes = array($operacoes, $visitando, $resolvidos, $fbbsPlano);
        try {
            $resolverRegistro($registro);
        } catch (RuntimeException $erro) {
            if ($linha !== 'todos') throw $erro;
            list($operacoes, $visitando, $resolvidos, $fbbsPlano) = $antes;
            $ignorados[] = array('linha' => (int)$registro['linha'], 'nome' => $registro['dados']['Nome'] ?? '', 'motivo' => $erro->getMessage());
        }
    }
    return array_values($operacoes);
}

function consultarCadastroPrevia($link, $bloquear = false)
{
    $listas = array();
    foreach (array('animais', 'terceiros') as $tabela) {
        $result = mysqli_query($link, 'SELECT id, nome, fbb, tatuagem, sexo' . ($tabela === 'animais' ? ', data_de_nascimento' : ', ativo') . ' FROM ' . $tabela . ($bloquear ? ' FOR UPDATE' : ''));
        $listas[$tabela] = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
    }
    return $listas;
}

/** Tabelas e nomes de colunas vêm apenas do plano gerado no servidor. */
function inserirCadastroPrevia($link, $tabela, array $dados)
{
    if (!in_array($tabela, array('animais', 'terceiros', 'matriz', 'reprodutor'), true)) { throw new RuntimeException('Destino de cadastro inválido.'); }
    $colunas = implode(', ', array_map(function ($campo) { return '`' . $campo . '`'; }, array_keys($dados)));
    $stmt = mysqli_prepare($link, 'INSERT INTO ' . $tabela . ' (' . $colunas . ') VALUES (' . implode(',', array_fill(0, count($dados), '?')) . ')');
    $valores = array_values($dados);
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($valores)), ...$valores);
    mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($link);
    if (mysqli_warning_count($link)) { throw new RuntimeException('O banco não preservou os dados do cadastro. Nenhuma alteração foi aplicada.'); }
    mysqli_stmt_close($stmt);
    if (in_array($tabela, array('animais', 'terceiros'), true)) {
        $salvo = mysqli_fetch_assoc(mysqli_query($link, 'SELECT * FROM ' . $tabela . ' WHERE id = ' . (int)$id));
        foreach ($dados as $campo => $valor) {
            if (!$salvo || ($valor === null ? $salvo[$campo] !== null : (string)$salvo[$campo] !== (string)$valor)) {
                throw new RuntimeException('O banco não preservou todos os dados de ' . $dados['nome'] . '. Nenhuma alteração foi aplicada.');
            }
        }
    }
    return $id;
}

function executarPlanoCadastroPrevia($link, array $plano)
{
    $ids = $paisRebanho = $maesRebanho = array();
    foreach ($plano as $op) {
        $dados = $op['dados'];
        if ($op['origem'] === 'animais') {
            foreach (array('pai', 'mae') as $campo) {
                $ref = $op[$campo];
                $dados[$campo] = $ref ? ($ref['id'] ?? ($ids[$ref['chave']] ?? 0)) : 0;
                if ($ref && !$dados[$campo]) { throw new RuntimeException('Referência de parentesco não resolvida.'); }
                $dados['terceiro_' . $campo] = $ref && $ref['origem'] === 'terceiros' ? 1 : 0;
                if ($ref && !$dados['terceiro_' . $campo]) {
                    if ($campo === 'pai') { $paisRebanho[$dados[$campo]] = true; }
                    else { $maesRebanho[$dados[$campo]] = true; }
                }
            }
        }
        $ids[$op['chave']] = inserirCadastroPrevia($link, $op['origem'], $dados);
    }
    foreach (array('pai' => $paisRebanho, 'mae' => $maesRebanho) as $campo => $referencias) {
        foreach (array_keys($referencias) as $id) { atualizarRankingCadastroPrevia($link, $campo, $id); }
    }
    return $ids;
}

function confirmarCadastroPrevia($link, array $registros, $linha, array $planoConfirmado)
{
    mysqli_begin_transaction($link);
    try {
        $listas = consultarCadastroPrevia($link, true);
        $plano = planejarCadastroPrevia($registros, $linha, $listas['animais'], $listas['terceiros'], consultarRacaCadastroPrevia($link, true));
        if ($plano !== $planoConfirmado) { throw new RuntimeException('Os cadastros mudaram. Abra novamente a confirmação para revisar os vínculos.'); }
        $ids = executarPlanoCadastroPrevia($link, $plano);
        mysqli_commit($link);
        return $ids;
    } catch (Throwable $erro) {
        mysqli_rollback($link);
        throw $erro;
    }
}

function atualizarRankingCadastroPrevia($link, $campo, $id)
{
    $tabela = $campo === 'pai' ? 'reprodutor' : 'matriz';
    $chave = $campo === 'pai' ? 'id_macho' : 'id_femea';
    $id = (int)$id;
    $crias = mysqli_fetch_all(mysqli_query($link, "SELECT data_de_nascimento, tipo_reproducao FROM animais WHERE $campo = '$id' AND terceiro_$campo = 0 ORDER BY data_de_nascimento"), MYSQLI_ASSOC);
    $dados = array('qtd_crias' => count($crias));
    if ($campo === 'mae') {
        $mae = mysqli_fetch_assoc(mysqli_query($link, "SELECT data_de_nascimento FROM animais WHERE id = $id"));
        $datas = $partosJovens = array();
        $criasJovens = 0;
        $nascimentoMae = strtotime($mae['data_de_nascimento'] ?? '') ?: null;
        foreach ($crias as $cria) {
            if ($cria['tipo_reproducao'] === 'Embrionagem' || !$cria['data_de_nascimento'] || $cria['data_de_nascimento'] <= '2011-01-01') { continue; }
            $tempo = strtotime($cria['data_de_nascimento']);
            if (!$tempo) { continue; }
            $datas[$tempo] = true;
            if ($nascimentoMae && $tempo >= $nascimentoMae && ($tempo - $nascimentoMae) / 86400 < 2555) {
                $partosJovens[$tempo] = true;
                $criasJovens++;
            }
        }
        $tempos = array_keys($datas);
        sort($tempos);
        $dados['intervalo'] = count($tempos) > 1 ? (end($tempos) - $tempos[0]) / 86400 / (count($tempos) - 1) : 0;
        $dados['prolificidade'] = $partosJovens ? $criasJovens / count($partosJovens) : 0;
    }
    $existente = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM $tabela WHERE $chave = $id FOR UPDATE"));
    if ($existente) {
        $sets = array();
        foreach (array_keys($dados) as $coluna) { $sets[] = "$coluna = ?"; }
        $stmt = mysqli_prepare($link, "UPDATE $tabela SET " . implode(', ', $sets) . " WHERE $chave = $id");
        $valores = array_values($dados);
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($valores)), ...$valores);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $zeros = array_fill_keys(array('qtd_avaliadas', 'tipo2', 'tipo3', 'tipo4', 'tipo5', 'qtd_vendas', 'qtd_mortes', 'venda_macho', 'venda_femea', 'venda_geral', 'nota', 'pesagem', 'cabeca', 'pescoco', 'quarto_anterior', 'barril', 'quarto_posterior', 'comprimento', 'orgao', 'gordura', 'cobertura', 'cor', 'conformacao'), 0);
        $zeros += $campo === 'pai' ? array('gmd' => 0) : array('peso_apartacao' => 0, 'intervalo' => 0, 'prolificidade' => 0, 'qtd_partos' => 0);
        inserirCadastroPrevia($link, $tabela, array_merge($zeros, array($chave => $id), $dados));
    }
}

function consultarRacaCadastroPrevia($link, $bloquear = false)
{
    $resultado = mysqli_query($link, 'SELECT raca FROM admin ORDER BY id LIMIT 1' . ($bloquear ? ' FOR UPDATE' : ''));
    $perfil = mysqli_fetch_assoc($resultado);
    mysqli_free_result($resultado);
    return $perfil['raca'] ?? '';
}
