<?php
function consultarReceptoras(array $entrada)
{
    $receptoras = array();
    $mediasPorParto = array();
    $historicosReceptoras = array();
    $criasPorParto = array();
    $buscaReceptora = isset($entrada['busca']) && is_string($entrada['busca']) ? trim($entrada['busca']) : '';
    $filtroPaginacao = '&amp;busca=' . rawurlencode($buscaReceptora);
    $totalReceptoras = 0;
    $porPagina = filter_var($entrada['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
    if (!in_array($porPagina, array(10, 20, 50, 100), true)) { $porPagina = 10; }
    $offsetReceptoras = 0;
    $paginaReceptoras = filter_var($entrada['pag'] ?? 1, FILTER_VALIDATE_INT);
    $paginaReceptoras = max(1, (int) $paginaReceptoras);
    $totalPaginas = 1;
    $erroReceptoras = '';
    $linkReceptoras = DBConnect();
    try {
        if (!mysqli_set_charset($linkReceptoras, 'utf8mb4')) {
            throw new RuntimeException('Falha ao configurar conexão.');
        }
        $filtroNome = $buscaReceptora !== '' ? " WHERE nome LIKE ? ESCAPE '!'" : '';
        $padraoBusca = '%' . strtr($buscaReceptora, array('!' => '!!', '%' => '!%', '_' => '!_')) . '%';
        $consultaTotal = mysqli_prepare($linkReceptoras, 'SELECT COUNT(*) AS total FROM receptora' . $filtroNome);
        if (!$consultaTotal) {
            throw new RuntimeException('Falha ao preparar pesquisa.');
        }
        if ($filtroNome !== '') {
            mysqli_stmt_bind_param($consultaTotal, 's', $padraoBusca);
        }
        if (!mysqli_stmt_execute($consultaTotal)) {
            throw new RuntimeException('Falha ao pesquisar receptoras.');
        }
        $resultado = mysqli_stmt_get_result($consultaTotal);
        mysqli_stmt_close($consultaTotal);
        if (!$resultado) {
            throw new RuntimeException('Falha ao consultar receptoras.');
        }
        $totalReceptoras = (int) mysqli_fetch_assoc($resultado)['total'];
        $totalPaginas = max(1, (int) ceil($totalReceptoras / $porPagina));
        $paginaReceptoras = min($paginaReceptoras, $totalPaginas);
        $offsetReceptoras = ($paginaReceptoras - 1) * $porPagina;
        $consultaLista = mysqli_prepare($linkReceptoras, "SELECT r.id, r.nome, r.ativo,
                COUNT(tc.id) AS lotes,
                COALESCE(SUM(tc.ultrassom = 1), 0) AS ultrassom_positivo,
                COALESCE(SUM(tc.ultrassom = 2), 0) AS ultrassom_negativo,
                COALESCE(SUM(tc.ultrassom = 0), 0) AS ultrassom_nao_informado,
                COALESCE(SUM(tc.status_nascimento = 1), 0) AS nascidos,
                COALESCE(SUM(tc.status_nascimento = 0), 0) AS nao_nascidos,
                COALESCE(
                    (SELECT historico.ultrassom
                     FROM transplante_controle AS historico
                     INNER JOIN transplante AS lote ON lote.id = historico.id_lote
                     WHERE historico.id_receptora = r.id
                     ORDER BY lote.id DESC, historico.id DESC LIMIT 1) = 2
                    AND
                    (SELECT historico.ultrassom
                     FROM transplante_controle AS historico
                     INNER JOIN transplante AS lote ON lote.id = historico.id_lote
                     WHERE historico.id_receptora = r.id
                     ORDER BY lote.id DESC, historico.id DESC LIMIT 1 OFFSET 1) = 2,
                    0
                ) AS duas_ultimas_negativas
            FROM (SELECT id, nome, ativo FROM receptora $filtroNome ORDER BY id DESC LIMIT $offsetReceptoras, $porPagina) AS r
            LEFT JOIN transplante_controle AS tc ON tc.id_receptora = r.id
            GROUP BY r.id, r.nome, r.ativo
            ORDER BY r.id DESC");
        if (!$consultaLista) {
            throw new RuntimeException('Falha ao preparar listagem.');
        }
        if ($filtroNome !== '') {
            mysqli_stmt_bind_param($consultaLista, 's', $padraoBusca);
        }
        if (!mysqli_stmt_execute($consultaLista)) {
            throw new RuntimeException('Falha ao pesquisar receptoras.');
        }
        $resultado = mysqli_stmt_get_result($consultaLista);
        mysqli_stmt_close($consultaLista);
        if (!$resultado) {
            throw new RuntimeException('Falha ao listar receptoras.');
        }
        while ($linha = mysqli_fetch_assoc($resultado)) {
            $receptoras[] = $linha;
        }
        // Agrupa os animais vinculados diretamente à receptora por data do parto.
        if ($receptoras) {
            $idsReceptoras = implode(',', array_map('intval', array_column($receptoras, 'id')));
            $resultadoCrias = mysqli_query($linkReceptoras, "SELECT cria.id_receptora, cria.data_de_nascimento,
                    cria.nome AS cria, CASE WHEN cria.terceiro_pai = 1 THEN externo.nome ELSE pai.nome END AS pai
                FROM animais AS cria
                LEFT JOIN animais AS pai ON pai.id = cria.pai AND COALESCE(cria.terceiro_pai, 0) = 0
                LEFT JOIN terceiros AS externo ON externo.id = cria.pai AND cria.terceiro_pai = 1
                WHERE cria.id_receptora IN ($idsReceptoras)
                  AND cria.data_de_nascimento IS NOT NULL
                  AND CAST(cria.data_de_nascimento AS CHAR) <> '0000-00-00'
                ORDER BY cria.nome, cria.id");
            if (!$resultadoCrias) {
                throw new RuntimeException('Falha ao consultar crias e pais das receptoras.');
            }
            while ($cria = mysqli_fetch_assoc($resultadoCrias)) {
                $criasPorParto[(int) $cria['id_receptora']][$cria['data_de_nascimento']][] = $cria;
            }
            $resultadoHistorico = mysqli_query($linkReceptoras, "SELECT tc.id_receptora, tc.id_lote,
                    tc.ultrassom, tc.status_nascimento, t.codigo, t.data,
                    partos.data_de_nascimento, partos.peso_parto
                FROM transplante_controle AS tc
                LEFT JOIN transplante AS t ON t.id = tc.id_lote
                LEFT JOIN (
                    SELECT a.id_receptora, a.data_de_nascimento,
                        CASE WHEN COUNT(*) = COUNT(CASE WHEN a.peso2 > 0 THEN 1 END)
                             THEN SUM(a.peso2) ELSE NULL END AS peso_parto
                    FROM animais AS a
                    WHERE a.id_receptora IN ($idsReceptoras)
                      AND a.data_de_nascimento IS NOT NULL
                      AND CAST(a.data_de_nascimento AS CHAR) <> '0000-00-00'
                    GROUP BY a.id_receptora, a.data_de_nascimento
                ) AS partos ON partos.id_receptora = tc.id_receptora
                    AND partos.data_de_nascimento BETWEEN DATE_ADD(t.data, INTERVAL 146 DAY)
                                                       AND DATE_ADD(t.data, INTERVAL 161 DAY)
                WHERE tc.id_receptora IN ($idsReceptoras)
                ORDER BY tc.id_lote DESC, tc.id DESC, partos.data_de_nascimento");
            if (!$resultadoHistorico) {
                throw new RuntimeException('Falha ao consultar histórico das receptoras.');
            }
            while ($historico = mysqli_fetch_assoc($resultadoHistorico)) {
                $historicosReceptoras[(int) $historico['id_receptora']][] = $historico;
            }
            $resultadoMedias = mysqli_query($linkReceptoras, "SELECT partos.id_receptora, AVG(partos.peso_parto) AS media_peso
                FROM (
                    SELECT a.id_receptora, a.data_de_nascimento, SUM(a.peso2) AS peso_parto
                    FROM animais AS a
                    WHERE a.id_receptora IN ($idsReceptoras)
                      AND a.data_de_nascimento IS NOT NULL
                      AND CAST(a.data_de_nascimento AS CHAR) <> '0000-00-00'
                    GROUP BY a.id_receptora, a.data_de_nascimento
                    HAVING COUNT(*) = COUNT(CASE WHEN a.peso2 > 0 THEN 1 END)
                ) AS partos
                GROUP BY partos.id_receptora");
            if (!$resultadoMedias) {
                throw new RuntimeException('Falha ao calcular a média por parto.');
            }
            while ($mediaParto = mysqli_fetch_assoc($resultadoMedias)) {
                $mediasPorParto[(int) $mediaParto['id_receptora']] = (float) $mediaParto['media_peso'];
            }
        }
    } catch (Exception $e) {
        $erroReceptoras = 'Não foi possível carregar as receptoras. Verifique se as migrações de receptoras foram aplicadas.';
    }
    DBClose($linkReceptoras);
    return compact('receptoras', 'mediasPorParto', 'historicosReceptoras', 'criasPorParto', 'buscaReceptora', 'filtroPaginacao', 'totalReceptoras', 'porPagina', 'offsetReceptoras', 'paginaReceptoras', 'totalPaginas', 'erroReceptoras');
}
