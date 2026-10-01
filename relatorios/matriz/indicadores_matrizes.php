<?php
/** Agrega separadamente crias e vendas para não multiplicar crias por venda. */
function consultarIndicadoresMatrizes()
{
    $joins = "LEFT JOIN (
        SELECT mae, COUNT(*) AS qtd_crias,
            SUM(CASE WHEN tipo > 0 THEN 1 ELSE 0 END) AS qtd_avaliadas,
            AVG(CASE WHEN tipo > 0 THEN tipo END) AS nota,
            SUM(tipo = 2) AS tipo2, SUM(tipo = 3) AS tipo3, SUM(tipo = 4) AS tipo4, SUM(tipo = 5) AS tipo5,
            100.0 * SUM(CASE WHEN causa_da_perda = 'Nascimento' THEN 1 ELSE 0 END) / COUNT(*) AS qtd_mortes,
            AVG(CASE WHEN peso3 > 0 AND peso_inicial > 0
                AND data_de_nascimento >= '1000-01-01' AND data3 >= '1000-01-01'
                AND DATEDIFF(data3, data_de_nascimento) > 0
                THEN (peso3 - peso_inicial) / NULLIF(DATEDIFF(data3, data_de_nascimento), 0) END) AS gmd
        FROM animais cria WHERE mae > 0 AND COALESCE(terceiro_mae, 0) = 0 GROUP BY mae
    ) c ON c.mae = a.id
    LEFT JOIN (
        SELECT f.mae, AVG(v.preco_de_venda) AS venda_geral,
            COUNT(v.preco_de_venda) AS qtd_vendas, SUM(v.preco_de_venda) AS total_vendas,
            AVG(CASE WHEN f.sexo = 'Macho' THEN v.preco_de_venda END) AS venda_macho,
            AVG(CASE WHEN f.sexo = 'Fêmea' THEN v.preco_de_venda END) AS venda_femea
        FROM animais f INNER JOIN vendas v ON v.id_animal = f.id
        WHERE f.mae > 0 AND COALESCE(f.terceiro_mae, 0) = 0 AND v.data >= '2011-01-01'
        GROUP BY f.mae
    ) v ON v.mae = a.id
    LEFT JOIN (
        SELECT f.mae,
            COUNT(DISTINCT CASE WHEN f.data_de_nascimento >= '2011-01-01' THEN f.data_de_nascimento END) AS qtd_partos,
            DATEDIFF(MAX(CASE WHEN f.data_de_nascimento >= '2011-01-01' THEN f.data_de_nascimento END),
                     MIN(CASE WHEN f.data_de_nascimento >= '2011-01-01' THEN f.data_de_nascimento END)) /
                NULLIF(COUNT(DISTINCT CASE WHEN f.data_de_nascimento >= '2011-01-01' THEN f.data_de_nascimento END) - 1, 0) AS intervalo,
            SUM(CASE WHEN f.data_de_nascimento >= '2011-01-01' AND m.data_de_nascimento >= '1000-01-01'
                AND f.data_de_nascimento >= m.data_de_nascimento AND f.data_de_nascimento <= DATE_ADD(m.data_de_nascimento, INTERVAL 7 YEAR) THEN 1 ELSE 0 END) /
                NULLIF(COUNT(DISTINCT CASE WHEN f.data_de_nascimento >= '2011-01-01' AND m.data_de_nascimento >= '1000-01-01'
                AND f.data_de_nascimento >= m.data_de_nascimento AND f.data_de_nascimento <= DATE_ADD(m.data_de_nascimento, INTERVAL 7 YEAR) THEN f.data_de_nascimento END), 0) AS prolificidade,
            90 * SUM(CASE WHEN f.peso2 > 0 AND f.data_de_nascimento >= '1000-01-01' AND DATEDIFF(f.data2, f.data_de_nascimento) BETWEEN 1 AND 149
                THEN f.peso2 / NULLIF(DATEDIFF(f.data2, f.data_de_nascimento), 0) END) /
                NULLIF(COUNT(DISTINCT CASE WHEN f.peso2 > 0 AND f.data_de_nascimento >= '1000-01-01' AND DATEDIFF(f.data2, f.data_de_nascimento) BETWEEN 1 AND 149
                THEN f.data_de_nascimento END), 0) AS peso_apartacao
        FROM animais f INNER JOIN animais m ON m.id = f.mae
        WHERE f.mae > 0 AND COALESCE(f.terceiro_mae, 0) = 0 AND COALESCE(f.tipo_reproducao, '') <> 'Embrionagem'
        GROUP BY f.mae
    ) p ON p.mae = a.id
    WHERE c.mae IS NOT NULL
    ORDER BY a.id ASC";
    return DBRead('animais a', $joins, 'a.id, a.id AS id_femea, a.id AS animal_encontrado, a.nome AS nome_animal, a.status AS status_animal,
        COALESCE(c.qtd_crias, 0) AS qtd_crias, COALESCE(c.qtd_avaliadas, 0) AS qtd_avaliadas,
        COALESCE(c.nota, 0) AS nota, COALESCE(v.venda_geral, 0) AS venda_geral,
        COALESCE(c.qtd_mortes, 0) AS qtd_mortes, COALESCE(c.gmd, 0) AS gmd,
        COALESCE(c.tipo2, 0) AS tipo2, COALESCE(c.tipo3, 0) AS tipo3, COALESCE(c.tipo4, 0) AS tipo4, COALESCE(c.tipo5, 0) AS tipo5,
        COALESCE(v.qtd_vendas, 0) AS qtd_vendas, COALESCE(v.total_vendas, 0) AS total_vendas,
        COALESCE(v.venda_macho, 0) AS venda_macho, COALESCE(v.venda_femea, 0) AS venda_femea, COALESCE(p.qtd_partos, 0) AS qtd_partos, p.intervalo, p.prolificidade, p.peso_apartacao') ?: array();
}

/** Uma avaliação por cria: segunda avaliação prioritária, última versão por ID. */
function consultarTipificacaoMatrizes()
{
    $campos = array('tamanho', 'cabeca', 'pescoco', 'quarto_anterior', 'barril', 'quarto_posterior', 'comprimento', 'orgao', 'distribuicao', 'cobertura', 'cor', 'conformacao');
    $medias = array('f.mae AS id_femea', 'COUNT(*) AS qtd_avaliadas');
    foreach ($campos as $campo) $medias[] = "AVG(e.$campo) AS $campo";
    return DBRead('avaliacao e', "INNER JOIN animais f ON f.id = e.id_animal
        WHERE f.mae > 0 AND COALESCE(f.terceiro_mae, 0) = 0 AND e.avaliacao IN (1, 2)
        AND NOT EXISTS (SELECT 1 FROM avaliacao outra WHERE outra.id_animal = e.id_animal AND outra.avaliacao IN (1, 2)
            AND (outra.avaliacao > e.avaliacao OR (outra.avaliacao = e.avaliacao AND outra.id > e.id)))
        GROUP BY f.mae", implode(', ', $medias)) ?: array();
}

/** Duração média aproximada: ano de 365 dias e mês de 30 dias. */
function formatarIntervaloMatriz($dias)
{
    if ($dias === null) return 'Não informado';
    $dias = max(0, (int)floor((float)$dias));
    $anos = intdiv($dias, 365);
    $meses = min(11, intdiv($dias % 365, 30));
    return $anos . ($anos === 1 ? ' ano e ' : ' anos e ') . $meses . ($meses === 1 ? ' mês' : ' meses');
}
