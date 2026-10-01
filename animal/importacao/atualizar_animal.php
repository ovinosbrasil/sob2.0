<?php
require_once __DIR__ . '/leitor_consulta_rebanho.php';
function dadosAtualizacaoPrevia(array $dados)
{
    $campos = array('nome' => 'Nome', 'fbb' => 'FBB/FBE', 'tatuagem' => 'Tat.');
    $limites = array('nome' => 80, 'fbb' => 20, 'tatuagem' => 15);
    $novos = array();
    foreach ($campos as $campo => $coluna) {
        $valor = $dados[$coluna] ?? '';
        if (!is_string($valor) || trim($valor) === '') {
            throw new RuntimeException('Preencha nome, FBB e tatuagem na planilha antes de atualizar.');
        }
        $novos[$campo] = trim($valor);
        if (mb_strlen($novos[$campo], 'UTF-8') > $limites[$campo]) {
            throw new RuntimeException($coluna . ' excede o limite de ' . $limites[$campo] . ' caracteres do cadastro.');
        }
    }
    $nascimento = $dados['Nasc.'] ?? '';
    $data = is_string($nascimento) ? DateTimeImmutable::createFromFormat('!d/m/Y', $nascimento) : false;
    if (!$data || $data->format('d/m/Y') !== $nascimento || $data > new DateTimeImmutable('today') || (int)$data->format('Y') < 1000) {
        throw new RuntimeException('Corrija a data de nascimento na planilha antes de atualizar.');
    }
    $novos['data_de_nascimento'] = $data->format('Y-m-d');
    if (morteNascimentoNomePrevia($novos['nome'])) {
        $novos['causa_da_perda'] = 'Nascimento';
        $novos['data_de_saida'] = $novos['data_de_nascimento'];
    }
    return $novos;
}

function validarContextoPrevia(array $previa, $envio, $banco, $login)
{
    if (($previa['versao'] ?? 0) !== 3 || ($previa['banco'] ?? '') !== $banco || ($previa['login'] ?? '') !== $login ||
        !is_string($envio) || !hash_equals($previa['envio'] ?? '', $envio) || !$envio) {
        throw new RuntimeException('Esta prévia expirou. Envie a planilha novamente.');
    }
}

function selecionarAtualizacaoPrevia(array $previa, $envio, $linha, $id, $banco, $login)
{
    validarContextoPrevia($previa, $envio, $banco, $login);
    if (!$linha || !$id || isset($previa['atualizados'][$linha])) {
        throw new RuntimeException('Esta linha não está disponível para atualização.');
    }
    foreach ($previa['possiveis'] ?? array() as $registro) {
        if ((int)$registro['linha'] !== $linha) { continue; }
        foreach ($registro['animais_banco'] as $candidato) {
            if ((int)$candidato['id'] === $id) {
                return array($candidato, dadosAtualizacaoPrevia($registro['dados']));
            }
        }
    }
    throw new RuntimeException('O animal selecionado não pertence aos candidatos desta linha.');
}

function atualizarAnimalDaPrevia($link, array $anterior, array $novos, $transacaoPropria = true)
{
    if (array_key_exists('status', $novos)) { throw new RuntimeException('A situação só pode ser definida no cadastro pela importação.'); }
    if (!$novos) { throw new RuntimeException('Selecione pelo menos um campo para atualizar.'); }
    $id = (int)$anterior['id'];
    if ($transacaoPropria) { mysqli_begin_transaction($link); }
    try {
        $stmt = mysqli_prepare($link, 'SELECT id, nome, fbb, tatuagem, data_de_nascimento, status, causa_da_perda, data_de_saida FROM animais WHERE id = ? FOR UPDATE');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $atual = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$atual) { throw new RuntimeException('O animal não está mais cadastrado. Envie a planilha novamente.'); }
        foreach (array_keys($novos) as $campo) {
            if ((string)($atual[$campo] ?? '') !== (string)($anterior[$campo] ?? '')) {
                throw new RuntimeException('O cadastro mudou desde a conferência. Recarregue a página e revise os dados antes de atualizar.');
            }
        }
        if (array_key_exists('fbb', $novos)) {
        $stmt = mysqli_prepare($link, 'SELECT id FROM animais WHERE fbb = ? AND id <> ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'si', $novos['fbb'], $id);
        mysqli_stmt_execute($stmt);
        $duplicado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if ($duplicado) { throw new RuntimeException('Já existe outro animal com o FBB da planilha. Revise os cadastros antes de atualizar.'); }
        }
        $camposPermitidos = array_flip(array('nome', 'fbb', 'tatuagem', 'data_de_nascimento', 'causa_da_perda', 'data_de_saida'));
        $dadosGravacao = array_intersect_key($novos, $camposPermitidos);
        $atribuicoes = array_map(function ($campo) { return $campo . ' = ?'; }, array_keys($dadosGravacao));
        $stmt = mysqli_prepare($link, 'UPDATE animais SET ' . implode(', ', $atribuicoes) . ' WHERE id = ?');
        $valores = array_values($dadosGravacao);
        $valores[] = $id;
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($valores)), ...$valores);
        mysqli_stmt_execute($stmt);
        if (mysqli_warning_count($link)) { throw new RuntimeException('Os valores excedem o formato permitido no cadastro. Revise a planilha.'); }
        mysqli_stmt_close($stmt);
        // Confere a persistência antes do commit, inclusive conversões feitas pelo banco.
        $stmt = mysqli_prepare($link, 'SELECT nome, fbb, tatuagem, data_de_nascimento, status, causa_da_perda, data_de_saida FROM animais WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $gravado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        foreach (array_keys($novos) as $campo) {
            if (!$gravado || (string)$gravado[$campo] !== (string)$novos[$campo]) {
                throw new RuntimeException('O banco não preservou os valores da planilha. Nenhuma alteração foi aplicada.');
            }
        }
        if ($transacaoPropria) { mysqli_commit($link); }
    } catch (Throwable $erro) {
        if ($transacaoPropria) { mysqli_rollback($link); }
        throw $erro;
    }
}

function atualizacoesEmLotePrevia(array $previa)
{
    $usos = array();
    foreach ($previa['possiveis'] ?? array() as $registro) {
        if (isset($previa['atualizados'][$registro['linha']])) { continue; }
        foreach ($registro['animais_banco'] as $animal) {
            $id = (int)$animal['id'];
            $usos[$id] = ($usos[$id] ?? 0) + 1;
        }
    }
    $lote = array();
    foreach ($previa['possiveis'] ?? array() as $registro) {
        if (isset($previa['atualizados'][$registro['linha']]) || count($registro['animais_banco']) !== 1) { continue; }
        $animal = $registro['animais_banco'][0];
        if ($usos[(int)$animal['id']] !== 1) { continue; }
        try { $novos = dadosAtualizacaoPrevia($registro['dados']); }
        catch (RuntimeException $erro) { continue; }
        $lote[] = array('linha' => $registro['linha'], 'anterior' => $animal, 'novos' => $novos);
    }
    return $lote;
}

function atualizarLoteDaPrevia($link, array $lote)
{
    if (!$lote) { throw new RuntimeException('Nenhum animal disponível para atualização em lote.'); }
    mysqli_begin_transaction($link);
    try {
        foreach ($lote as $item) {
            atualizarAnimalDaPrevia($link, $item['anterior'], $item['novos'], false);
        }
        mysqli_commit($link);
    } catch (Throwable $erro) {
        mysqli_rollback($link);
        throw $erro;
    }
}

function selecionarCamposAtualizacaoPrevia(array $novos, $campos)
{
    if (!is_array($campos) || !$campos) { throw new RuntimeException('Selecione pelo menos um campo para atualizar.'); }
    $selecionados = array();
    foreach ($campos as $campo) {
        if (!is_string($campo) || !array_key_exists($campo, $novos)) { throw new RuntimeException('Campo de atualização inválido. Abra a confirmação novamente.'); }
        $selecionados[$campo] = $novos[$campo];
    }
    return $selecionados;
}

function excluirCamposLotePrevia(array $lote, array $exclusoes, array $globais = array()) {
    $permitidos = array();
    foreach ($lote as $item) $permitidos += $item['novos'];
    foreach ($globais as $campo) {
        if (!is_string($campo) || !array_key_exists($campo, $permitidos)) throw new RuntimeException('Seleção de campos inválida.');
    }
    $linhas = array_column($lote, 'linha');
    foreach ($exclusoes as $linha => $campos) {
        if (!in_array((int)$linha, $linhas, true) || !is_array($campos)) throw new RuntimeException('Seleção de campos inválida.');
    }
    $selecionados = array();
    foreach ($lote as $item) {
        $campos = $exclusoes[$item['linha']] ?? array();
        foreach ($campos as $campo) {
            if (!is_string($campo) || !array_key_exists($campo, $item['novos'])) throw new RuntimeException('Seleção de campos inválida.');
        }
        $diferentes = camposDiferentesPrevia($item['anterior'] ?? array(), $item['novos']);
        $novos = array_diff_key($diferentes, array_flip(array_merge($campos, $globais)));
        if (!$novos) continue;
        $item['completa'] = count($novos) === count($diferentes);
        $item['novos'] = $novos;
        $selecionados[] = $item;
    }
    if (!$selecionados) throw new RuntimeException('Selecione pelo menos um campo para atualizar.');
    return $selecionados;
}

function camposDiferentesPrevia(array $anterior, array $novos) {
    return array_filter($novos, function ($valor, $campo) use ($anterior) {
        return (string)($anterior[$campo] ?? '') !== (string)$valor;
    }, ARRAY_FILTER_USE_BOTH);
}
