<?php
function identificadorAnimalPrevia($valor)
{
    return mb_strtoupper(textoConsultaRebanho((string)$valor), 'UTF-8');
}

function chaveAnimalPrevia($nome, $nascimento, $formato)
{
    $nome = identificadorAnimalPrevia($nome);
    $nascimento = (string)$nascimento;
    $data = DateTimeImmutable::createFromFormat('!' . $formato, $nascimento);
    if ($nome === '' || !$data || $data->format($formato) !== $nascimento) { return null; }
    return $nome . "\x1f" . $data->format('Y-m-d');
}

function divergenciasAnimalPrevia(array $dados, array $animal)
{
    $divergencias = array();
    foreach (array('nome' => 'Nome', 'fbb' => 'FBB/FBE', 'tatuagem' => 'Tat.') as $campo => $coluna) {
        $valor = identificadorAnimalPrevia($dados[$coluna] ?? '');
        $divergencias[$campo] = $valor === '' || $valor !== identificadorAnimalPrevia($animal[$campo] ?? '');
    }
    $dataPlanilha = chaveAnimalPrevia('data', $dados['Nasc.'] ?? '', 'd/m/Y');
    $dataBanco = chaveAnimalPrevia('data', $animal['data_de_nascimento'] ?? '', 'Y-m-d');
    $divergencias['data_de_nascimento'] = $dataPlanilha === null || $dataPlanilha !== $dataBanco;
    if (morteNascimentoNomePrevia($dados['Nome'] ?? '')) {
        $divergencias['causa_da_perda'] = ($animal['causa_da_perda'] ?? '') !== 'Nascimento';
        $saidaEsperada = chaveAnimalPrevia('data', $dados['Nasc.'] ?? '', 'd/m/Y');
        $divergencias['data_de_saida'] = $saidaEsperada === null || $saidaEsperada !== chaveAnimalPrevia('data', $animal['data_de_saida'] ?? '', 'Y-m-d');
    }
    return $divergencias;
}

function compararAnimaisPrevia(array $registros, iterable $animais, $buscarSemelhantes = true)
{
    $fbbs = $nomes = $tatuagens = array();
    $sequencia = 0;
    foreach ($animais as $animal) {
        $chaveBanco = isset($animal['id']) ? 'id:' . $animal['id'] : 'linha:' . $sequencia++;
        $fbb = identificadorAnimalPrevia($animal['fbb'] ?? '');
        if ($fbb !== '') { $fbbs[$fbb][$chaveBanco] = $animal; }
        if ($buscarSemelhantes) {
            $nome = identificadorAnimalPrevia($animal['nome'] ?? '');
            $tatuagem = identificadorAnimalPrevia($animal['tatuagem'] ?? '');
            if ($nome !== '') { $nomes[$nome][$chaveBanco] = $animal; }
            if ($tatuagem !== '') { $tatuagens[$tatuagem][$chaveBanco] = $animal; }
        }

    }
    $grupos = array('cadastrados' => array(), 'possiveis_atualizacoes' => array(), 'nao_cadastrados' => array());
    foreach ($registros as $registro) {
        $fbb = identificadorAnimalPrevia($registro['dados']['FBB/FBE'] ?? '');
        $fbbEncontrado = $fbb !== '' && isset($fbbs[$fbb]);
        $nome = identificadorAnimalPrevia($registro['dados']['Nome'] ?? '');
        $tatuagem = identificadorAnimalPrevia($registro['dados']['Tat.'] ?? '');
        $porNome = $buscarSemelhantes && $nome !== '' ? ($nomes[$nome] ?? array()) : array();
        $porTatuagem = $buscarSemelhantes && $tatuagem !== '' ? ($tatuagens[$tatuagem] ?? array()) : array();
        // FBB identifica o cadastro; nome/tatuagem sugerem candidatos quando o FBB não foi encontrado.
        $candidatos = $fbbEncontrado ? $fbbs[$fbb] : ($porNome + $porTatuagem);
        if ($candidatos) {
            $registro['animais_banco'] = array_values($candidatos);
            $cadastroIgual = false;
            foreach ($registro['animais_banco'] as $candidato) {
                if (!array_filter(divergenciasAnimalPrevia($registro['dados'], $candidato))) {
                    $cadastroIgual = true;
                    break;
                }
            }
            if ($cadastroIgual) {
                $registro['correspondencia'] = 'Nome, FBB/FBE, tatuagem e nascimento iguais';
                $grupos['cadastrados'][] = $registro;
                continue;
            }
            $criterios = array();
            if ($fbbEncontrado) { $criterios[] = 'FBB/FBE'; }
            else {
                if ($porNome) { $criterios[] = 'Nome'; }
                if ($porTatuagem) { $criterios[] = 'Tatuagem'; }
            }
            $registro['correspondencia'] = 'Coincidência no banco: ' . implode(', ', $criterios) . '. Cadastro a confirmar';
            $registro['problemas'][] = 'Possível cadastro existente. Confira os dados antes de atualizar.';
            $grupos['possiveis_atualizacoes'][] = $registro;
        } else {
            if (chaveAnimalPrevia($registro['dados']['Nome'] ?? '', $registro['dados']['Nasc.'] ?? '', 'd/m/Y') === null && chaveAnimalPrevia($registro['dados']['FBB/FBE'] ?? '', $registro['dados']['Nasc.'] ?? '', 'd/m/Y') === null) {
                $registro['problemas'][] = 'Não foi possível conferir o cadastro: revise a data de nascimento e informe nome ou FBB/FBE.';
            }
            $grupos['nao_cadastrados'][] = $registro;
        }
    }
    return $grupos;
}

/** Uma consulta de leitura, no mesmo banco usado pela página atual. */
function consultarAnimaisPrevia()
{
    $link = DBConnect();
    try {
        $resultado = mysqli_query($link, 'SELECT id, nome, fbb, tatuagem, data_de_nascimento, status, causa_da_perda, data_de_saida FROM animais ORDER BY id ASC');
        if (!$resultado) { throw new RuntimeException('Falha na consulta de animais.'); }
        try {
            while ($animal = mysqli_fetch_assoc($resultado)) { yield $animal; }
        } finally {
            mysqli_free_result($resultado);
        }
    } catch (Throwable $erro) {
        throw new RuntimeException('Não foi possível conferir os animais no banco. Tente enviar a planilha novamente.', 0, $erro);
    } finally {
        DBClose($link);
    }
}
