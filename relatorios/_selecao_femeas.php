<?php
function filtrosSelecaoFemeas(array $entrada)
{
    $tipo = $entrada['tipo'] ?? 'sem_cria';
    if (!in_array($tipo, array('sem_cria','sem_lote_reproducao','crias_tipo_2','cria_morte_nascimento','duas_crias_morte_nascimento','gmd_acima_media','gmd_abaixo_media'), true)) throw new InvalidArgumentException('Selecione um tipo válido.');
    $filtros = array('tipo'=>$tipo);
    foreach (array('idade_inicio','idade_fim') as $campo) {
        $valor = $entrada[$campo] ?? '';
        if ($valor === '') { $filtros[$campo] = null; continue; }
        $idade = filter_var($valor, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>0)));
        if ($idade === false) throw new InvalidArgumentException('Informe as idades em meses inteiros, a partir de zero.');
        $filtros[$campo] = $idade;
    }
    if ($filtros['idade_inicio'] !== null && $filtros['idade_fim'] !== null && $filtros['idade_inicio'] > $filtros['idade_fim']) {
        throw new InvalidArgumentException('A idade inicial deve ser menor ou igual à idade final.');
    }
    return $filtros;
}

function consultarSelecaoFemeas(array $entrada, DateTimeImmutable $hoje = null)
{
    $filtros = filtrosSelecaoFemeas($entrada);
    $limite = filter_var($entrada['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
    if (!in_array($limite, array(10,20,50,100), true)) $limite = 10;
    $resultado = array('filtros'=>$filtros, 'pronto'=>false, 'registros'=>array(), 'total'=>0, 'pagina'=>1, 'paginas'=>1, 'inicio'=>0, 'por_pagina'=>$limite, 'media_rebanho'=>null);
    if ($filtros['idade_inicio'] === null) return $resultado;
    $hoje = $hoje ?? new DateTimeImmutable('today', new DateTimeZone('America/Bahia'));
    $data = $hoje->format('Y-m-d');
    $idade = "TIMESTAMPDIFF(MONTH, a.data_de_nascimento, '$data')";
    $limiteFinal = $filtros['idade_fim'] !== null ? " AND $idade <= {$filtros['idade_fim']}" : '';
    $condicao = "WHERE a.sexo = 'Fêmea' AND a.status = 0
        AND a.data_de_nascimento >= '1000-01-01' AND a.data_de_nascimento <= '$data'
        AND $idade >= {$filtros['idade_inicio']} $limiteFinal";
    if ($filtros['tipo'] === 'sem_cria') {
        $condicao .= " AND NOT EXISTS (SELECT 1 FROM animais cria WHERE cria.mae = a.id AND COALESCE(cria.terceiro_mae, 0) = 0)";
    } elseif (in_array($filtros['tipo'], array('gmd_acima_media','gmd_abaixo_media'), true)) {
        $expressaoGmd = function ($alias) use ($data) {
            return "CASE WHEN $alias.peso2 > 0 AND $alias.peso_inicial > 0
                AND $alias.data_de_nascimento >= '1000-01-01'
                AND $alias.data2 <= '$data' AND DATEDIFF($alias.data2, $alias.data_de_nascimento) > 0
                THEN ($alias.peso2 - $alias.peso_inicial) END";
        };
        $mediaRebanho = DBRead('animais rebanho', 'WHERE rebanho.status = 0', 'AVG(' . $expressaoGmd('rebanho') . ') AS media') ?: array();
        $resultado['media_rebanho'] = isset($mediaRebanho[0]['media']) ? (float)$mediaRebanho[0]['media'] : null;
        // Média dos ganhos totais individuais de todo o rebanho ativo, sem o filtro de idade.
        $operadorGmd = $filtros['tipo'] === 'gmd_abaixo_media' ? '<' : '>';
        $condicao .= ' AND (' . $expressaoGmd('a') . ') ' . $operadorGmd . ' (SELECT AVG(' . $expressaoGmd('rebanho') . ') FROM animais rebanho WHERE rebanho.status = 0)';
    } elseif ($filtros['tipo'] === 'duas_crias_morte_nascimento') {
        // Ordena todas as crias antes de avaliar as mortes, incluindo vivas e vendidas.
        // Em nascimentos na mesma data, o maior ID fica primeiro.
        foreach (array(0, 1) as $posicao) {
            $condicao .= " AND (SELECT CASE WHEN cria.status = 1 AND LOWER(TRIM(COALESCE(cria.causa_da_perda, ''))) = 'nascimento' THEN 1 ELSE 0 END
                FROM animais cria WHERE cria.mae = a.id AND COALESCE(cria.terceiro_mae, 0) = 0
                ORDER BY cria.data_de_nascimento DESC, cria.id DESC LIMIT $posicao,1) = 1";
        }
    } elseif ($filtros['tipo'] === 'cria_morte_nascimento') {
        $condicao .= " AND EXISTS (SELECT 1 FROM animais cria WHERE cria.mae = a.id AND COALESCE(cria.terceiro_mae, 0) = 0 AND cria.status = 1 AND LOWER(TRIM(COALESCE(cria.causa_da_perda, ''))) = 'nascimento')";
    } elseif ($filtros['tipo'] === 'crias_tipo_2') {
        $condicao .= " AND EXISTS (SELECT 1 FROM animais cria WHERE cria.mae = a.id AND COALESCE(cria.terceiro_mae, 0) = 0 AND cria.tipo = 2)";
    } else {
        $condicao .= " AND NOT EXISTS (
            SELECT 1 FROM monta_controle mc INNER JOIN monta m ON m.id = mc.id_monta
            WHERE mc.id_animal = a.id AND COALESCE(mc.terceiro, 0) = 0)
            AND NOT EXISTS (
            SELECT 1 FROM inseminacao_controle ic INNER JOIN inseminacao i ON i.id = ic.id_lote
            WHERE ic.id_femea = a.id AND COALESCE(ic.terceiro, 0) = 0)
            AND NOT EXISTS (
            SELECT 1 FROM transplante te WHERE te.id_mae = a.id AND COALESCE(te.terceiro_mae, 0) = 0)";
    }
    $contagem = DBRead('animais a', $condicao, 'COUNT(*) AS total') ?: array();
    $resultado['pronto'] = true;
    $resultado['total'] = (int)($contagem[0]['total'] ?? 0);
    $resultado['paginas'] = max(1, (int)ceil($resultado['total'] / $limite));
    $pagina = filter_var($entrada['pag'] ?? 1, FILTER_VALIDATE_INT);
    $resultado['pagina'] = min($resultado['paginas'], max(1, $pagina ?: 1));
    $resultado['inicio'] = ($resultado['pagina'] - 1) * $limite;
    if ($resultado['total']) {
        $campoGmd = in_array($filtros['tipo'], array('gmd_acima_media','gmd_abaixo_media'), true) ? ', (' . $expressaoGmd('a') . ') AS gmd_medio' : '';
        $resultado['registros'] = DBRead('animais a', "$condicao ORDER BY idade_meses DESC, a.nome ASC, a.id ASC LIMIT {$resultado['inicio']},$limite", "a.id, a.nome, a.fbb, a.tatuagem, a.data_de_nascimento, $idade AS idade_meses$campoGmd") ?: array();
    }
    return $resultado;
}
