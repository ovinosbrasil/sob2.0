<?php
/** Agrega separadamente crias e vendas para não multiplicar crias por venda. */
function consultarIndicadoresReprodutores()
{
    $joins = "LEFT JOIN (
        SELECT pai, COUNT(*) AS qtd_crias,
            SUM(CASE WHEN tipo > 0 THEN 1 ELSE 0 END) AS qtd_avaliadas,
            AVG(CASE WHEN tipo > 0 THEN tipo END) AS nota,
            SUM(tipo = 2) AS tipo2, SUM(tipo = 3) AS tipo3, SUM(tipo = 4) AS tipo4, SUM(tipo = 5) AS tipo5,
            100.0 * SUM(CASE WHEN causa_da_perda = 'Nascimento' THEN 1 ELSE 0 END) / COUNT(*) AS qtd_mortes,
            AVG(CASE WHEN peso3 > 0 AND peso_inicial > 0
                AND data_de_nascimento >= '1000-01-01' AND data3 >= '1000-01-01'
                AND DATEDIFF(data3, data_de_nascimento) > 0
                THEN (peso3 - peso_inicial) / NULLIF(DATEDIFF(data3, data_de_nascimento), 0) END) AS gmd
        FROM animais cria WHERE pai > 0 AND COALESCE(terceiro_pai, 0) = 0 GROUP BY pai
    ) c ON c.pai = a.id
    LEFT JOIN (
        SELECT f.pai, AVG(v.preco_de_venda) AS venda_geral,
            COUNT(v.preco_de_venda) AS qtd_vendas, SUM(v.preco_de_venda) AS total_vendas,
            AVG(CASE WHEN f.sexo = 'Macho' THEN v.preco_de_venda END) AS venda_macho,
            AVG(CASE WHEN f.sexo = 'Fêmea' THEN v.preco_de_venda END) AS venda_femea
        FROM animais f INNER JOIN vendas v ON v.id_animal = f.id
        WHERE f.pai > 0 AND COALESCE(f.terceiro_pai, 0) = 0 AND v.data >= '2011-01-01'
        GROUP BY f.pai
    ) v ON v.pai = a.id
    WHERE c.pai IS NOT NULL
    ORDER BY a.id ASC";
    return DBRead('animais a', $joins, 'a.id, a.id AS id_macho, a.id AS animal_encontrado, a.nome AS nome_animal, a.status AS status_animal,
        COALESCE(c.qtd_crias, 0) AS qtd_crias, COALESCE(c.qtd_avaliadas, 0) AS qtd_avaliadas,
        COALESCE(c.nota, 0) AS nota, COALESCE(v.venda_geral, 0) AS venda_geral,
        COALESCE(c.qtd_mortes, 0) AS qtd_mortes, COALESCE(c.gmd, 0) AS gmd,
        COALESCE(c.tipo2, 0) AS tipo2, COALESCE(c.tipo3, 0) AS tipo3, COALESCE(c.tipo4, 0) AS tipo4, COALESCE(c.tipo5, 0) AS tipo5,
        COALESCE(v.qtd_vendas, 0) AS qtd_vendas, COALESCE(v.total_vendas, 0) AS total_vendas,
        COALESCE(v.venda_macho, 0) AS venda_macho, COALESCE(v.venda_femea, 0) AS venda_femea') ?: array();
}

/** Uma avaliação por cria: segunda avaliação prioritária, última versão por ID. */
function consultarTipificacaoReprodutores()
{
    $campos = array('tamanho', 'cabeca', 'pescoco', 'quarto_anterior', 'barril', 'quarto_posterior', 'comprimento', 'orgao', 'distribuicao', 'cobertura', 'cor', 'conformacao');
    $medias = array('f.pai AS id_macho', 'COUNT(*) AS qtd_avaliadas');
    foreach ($campos as $campo) $medias[] = "AVG(e.$campo) AS $campo";
    return DBRead('avaliacao e', "INNER JOIN animais f ON f.id = e.id_animal
        WHERE f.pai > 0 AND COALESCE(f.terceiro_pai, 0) = 0 AND e.avaliacao IN (1, 2)
        AND NOT EXISTS (SELECT 1 FROM avaliacao outra WHERE outra.id_animal = e.id_animal AND outra.avaliacao IN (1, 2)
            AND (outra.avaliacao > e.avaliacao OR (outra.avaliacao = e.avaliacao AND outra.id > e.id)))
        GROUP BY f.pai", implode(', ', $medias)) ?: array();
}
