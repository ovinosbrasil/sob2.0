<?php
/** Leitura da exportação HTML .xls de Consulta de Rebanho, sem persistência. */
function colunasConsultaRebanho()
{
    return array('FBB/FBE', 'Nome', 'Tat.', 'Nasc.', 'Sexo', 'Pai', 'Mãe', 'Situação');
}

function colunasArquivoRebanho()
{
    return array('FBB', 'Nome', 'Nasc.', 'Sexo', 'Pai', 'Mãe');
}

function tatuagemNomeRebanho($nome)
{
    $partes = explode(' ', textoConsultaRebanho($nome));
    return end($partes);
}

function textoConsultaRebanho($texto)
{
    return trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $texto));
}

function lerConsultaRebanho($conteudo, $permitirVazia = false)
{
    if ($conteudo === '' || strlen($conteudo) > 5 * 1024 * 1024) {
        throw new RuntimeException('Envie um arquivo não vazio de até 5 MB.');
    }
    if (substr($conteudo, 0, 2) === 'PK' || substr($conteudo, 0, 8) === "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
        throw new RuntimeException('Este formato de Excel ainda não é aceito. Use o arquivo .xls exportado pela Consulta de Rebanho, como o modelo desta etapa.');
    }
    if (!preg_match('//u', $conteudo)) {
        $conteudo = iconv('Windows-1252', 'UTF-8', $conteudo);
        if ($conteudo === false) {
            throw new RuntimeException('Não foi possível identificar a codificação do arquivo.');
        }
    }
    if (preg_match('/<!\s*(DOCTYPE|ENTITY)\b/i', $conteudo)) {
        throw new RuntimeException('O arquivo contém uma declaração não permitida. Envie a exportação original da Consulta de Rebanho.');
    }
    $errosAnteriores = libxml_use_internal_errors(true);
    try {
        $documento = new DOMDocument();
        $carregado = $documento->loadHTML('<?xml encoding="UTF-8">' . $conteudo, LIBXML_NONET);
        if (!$carregado) {
            throw new RuntimeException('Não foi possível ler a tabela do arquivo.');
        }
        $xpath = new DOMXPath($documento);
        $colunas = colunasArquivoRebanho();
        $tabela = null;
        foreach ($xpath->query('//table') as $candidata) {
            $primeira = $xpath->query('./tr|./thead/tr|./tbody/tr', $candidata)->item(0);
            if (!$primeira) { continue; }
            $cabecalho = array();
            foreach ($xpath->query('./th|./td', $primeira) as $celula) {
                $cabecalho[] = textoConsultaRebanho($celula->textContent);
            }
            if (count(array_unique($cabecalho)) === count($cabecalho) && !array_diff($colunas, $cabecalho)) {
                if ($tabela !== null) {
                    throw new RuntimeException('O arquivo possui mais de uma tabela de rebanho. Envie uma exportação por vez.');
                }
                $tabela = $candidata;
                $ordem = $cabecalho;
            }
        }
        if ($tabela === null) {
            throw new RuntimeException('Tabela não reconhecida. As colunas esperadas são: ' . implode(', ', $colunas) . '.');
        }
        $registros = array();
        $identificadores = array();
        foreach ($xpath->query('./tr|./thead/tr|./tbody/tr', $tabela) as $indice => $linha) {
            if ($indice === 0) { continue; }
            $valores = array();
            foreach ($xpath->query('./td|./th', $linha) as $celula) {
                $valores[] = textoConsultaRebanho($celula->textContent);
            }
            if (!array_filter($valores, function ($valor) { return $valor !== ''; })) { continue; }
            // Uma linha incompleta deve aparecer para revisão, nunca ser descartada silenciosamente.
            $problemas = array();
            if (count($valores) !== count($ordem)) {
                $problemas[] = 'Quantidade de colunas diferente do cabeçalho.';
            }
            $celulas = array_combine($ordem, array_slice(array_pad($valores, count($ordem), ''), 0, count($ordem)));
            // Somente as colunas utilizadas entram na prévia e na sessão; avós são descartados.
            $dados = array(
                'FBB/FBE' => $celulas['FBB'],
                'Nome' => $celulas['Nome'],
                'Tat.' => tatuagemNomeRebanho($celulas['Nome']),
                'Nasc.' => $celulas['Nasc.'],
                'Sexo' => $celulas['Sexo'],
                'Pai' => $celulas['Pai'],
                'Mãe' => $celulas['Mãe']
            );
            foreach (array('FBB/FBE', 'Nome', 'Tat.') as $campo) {
                if ($dados[$campo] === '') { $problemas[] = $campo . ' não informado.'; }
            }
            $data = DateTimeImmutable::createFromFormat('!d/m/Y', $dados['Nasc.']);
            if (!$data || $data->format('d/m/Y') !== $dados['Nasc.']) {
                $problemas[] = 'Data de nascimento inválida.';
            } elseif ($data > new DateTimeImmutable('today')) {
                $problemas[] = 'Data de nascimento no futuro.';
            }
            if (!in_array($dados['Sexo'], array('Macho', 'Fêmea'), true)) {
                $problemas[] = 'Sexo não reconhecido.';
            }
            $registros[] = array('linha' => $indice + 1, 'dados' => $dados, 'problemas' => $problemas);
            if ($dados['FBB/FBE'] !== '') {
                $chave = strtoupper($dados['FBB/FBE']);
                $identificadores[$chave][] = count($registros) - 1;
            }
        }
        if (!$registros && !$permitirVazia) { throw new RuntimeException('A tabela não contém animais para visualizar.'); }
        foreach ($identificadores as $indices) {
            if (count($indices) < 2) { continue; }
            foreach ($indices as $indice) {
                $registros[$indice]['problemas'][] = 'FBB/FBE repetido neste arquivo.';
            }
        }
        return $registros;
    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($errosAnteriores);
    }
}

function situacaoAnimalPrevia($status)
{
    $situacoes = array(0 => 'Vivo', 1 => 'Morto', 2 => 'Vendido', 3 => 'Empréstimo', 4 => 'Doação', 5 => 'Abate');
    return $status === null ? 'Não informado' : ($situacoes[(int)$status] ?? 'Não informado');
}

function unirPlanilhasRebanho(array $vivos, array $mortos, $prefixo = '')
{
    $registros = $identidades = array();
    foreach (array('Vivo' => $vivos, 'Morto' => $mortos) as $situacao => $linhas) {
        foreach ($linhas as $registro) {
            $registro['linha_origem'] = $registro['linha'];
            $registro['origem'] = $situacao === 'Morto' ? 'mortos' : 'vivos';
            $registro['linha'] = count($registros) + 1;
            $registro['dados']['Situação'] = $situacao;
            $registros[] = $registro;
        }
    }
    if (!$registros) { throw new RuntimeException('As duas planilhas estão sem animais.'); }
    $registros = numerarNomesMortosPrevia($registros, $prefixo);
    foreach ($registros as $registro) {
        foreach (array('FBB/FBE') as $campo) {
            $valor = mb_strtoupper(textoConsultaRebanho($registro['dados'][$campo] ?? ''), 'UTF-8');
            if ($valor === '') { continue; }
            $chave = $campo . ':' . $valor;
            if (isset($identidades[$chave]) && $identidades[$chave] !== $registro['origem']) {
                throw new RuntimeException('Animal presente nas listas de vivos e mortos (' . $campo . ': ' . $valor . '). Corrija as planilhas antes de verificar.');
            }
            $identidades[$chave] = $registro['origem'];
        }
    }
    return $registros;
}

function numerarNomesMortosPrevia(array $registros, $prefixo = '')
{
    $prefixo = mb_strtoupper(textoConsultaRebanho($prefixo), 'UTF-8');
    $contador = 0;
    $nomes = array();
    foreach ($registros as &$registro) {
        $nome = $registro['nome_original'] ?? $registro['dados']['Nome'];
        if (mb_stripos($nome, 'morto', 0, 'UTF-8') === false) { continue; }
        $registro['nome_original'] = $nome;
        $registro['dados']['Nome'] = ($prefixo !== '' ? $prefixo . ' ' : '') . 'MORTO' . ++$contador;
        $registro['dados']['Tat.'] = tatuagemNomeRebanho($registro['dados']['Nome']);
        $registro['dados']['Situação'] = 'Morto';
        $chave = mb_strtoupper(textoConsultaRebanho($nome), 'UTF-8');
        $nomes[$chave][] = $registro['dados']['Nome'];
    }
    unset($registro);
    // Mantém vínculos pelo nome original apenas quando há uma correspondência única.
    foreach ($registros as &$registro) {
        foreach (array('Pai', 'Mãe') as $campo) {
            $nome = $registro['parentes_originais'][$campo] ?? ($registro['dados'][$campo] ?? '');
            $chave = mb_strtoupper(textoConsultaRebanho($nome), 'UTF-8');
            if (!isset($nomes[$chave])) { continue; }
            $registro['parentes_originais'][$campo] = $nome;
            if (count($nomes[$chave]) === 1) {
                $registro['dados'][$campo] = $nomes[$chave][0];
            } else {
                $aviso = $campo . ' ambíguo: ' . $nome . ' identifica mais de um animal numerado. Corrija o parentesco na planilha.';
                if (!in_array($aviso, $registro['problemas'], true)) { $registro['problemas'][] = $aviso; }
            }
        }
    }
    unset($registro);
    return $registros;
}

function lerArquivosRebanho(array $arquivos, $prefixo = '')
{
    // Confere ambos antes de começar a leitura, mesmo se o navegador for contornado.
    foreach (array('vivos', 'mortos') as $tipo) {
        $arquivo = $arquivos['planilha_' . $tipo] ?? null;
        if (!is_array($arquivo) || !isset($arquivo['error']) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException('Envie as duas planilhas: animais vivos e animais mortos.');
        }
    }
    $listas = $nomes = array();
    foreach (array('vivos', 'mortos') as $tipo) {
        $arquivo = $arquivos['planilha_' . $tipo];
        if (in_array($arquivo['error'], array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE), true)) {
            throw new RuntimeException('A planilha de ' . $tipo . ' excedeu o limite de 5 MB.');
        }
        if ($arquivo['error'] !== UPLOAD_ERR_OK) { throw new RuntimeException('Falha no envio da planilha de ' . $tipo . '. Envie os dois arquivos novamente.'); }
        if (!is_string($arquivo['name'] ?? null) || strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION)) !== 'xls') {
            throw new RuntimeException('A planilha de ' . $tipo . ' deve ser uma exportação .xls.');
        }
        if (!is_string($arquivo['tmp_name'] ?? null) || !is_uploaded_file($arquivo['tmp_name'])) {
            throw new RuntimeException('Não foi possível validar o envio da planilha de ' . $tipo . '.');
        }
        if (filesize($arquivo['tmp_name']) > 5 * 1024 * 1024) { throw new RuntimeException('A planilha de ' . $tipo . ' excedeu o limite de 5 MB.'); }
        $conteudo = file_get_contents($arquivo['tmp_name']);
        if ($conteudo === false) { throw new RuntimeException('Não foi possível ler a planilha de ' . $tipo . '.'); }
        $listas[$tipo] = lerConsultaRebanho($conteudo, true);
        $nomes[$tipo] = basename($arquivo['name']);
    }
    return array('registros' => unirPlanilhasRebanho($listas['vivos'], $listas['mortos'], $prefixo), 'arquivos' => $nomes);
}

function morteNascimentoNomePrevia($nome)
{
    return mb_stripos((string)$nome, 'morto', 0, 'UTF-8') !== false;
}
