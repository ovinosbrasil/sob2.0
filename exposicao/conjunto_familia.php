<?php
/** A idade da mãe é avaliada na data da exposição, como nas categorias da pista. */
function listarConjuntosFamiliaExposicao($idEvento)
{
    return DBRead(
        'julgamento_controle jc
         INNER JOIN julgamento evento ON evento.id = jc.id_julgamento
         INNER JOIN animais_evento ae ON ae.id_julgamento = jc.id_julgamento AND ae.id_animal = jc.id_animal
         INNER JOIN animais filha ON filha.id = jc.id_animal
         INNER JOIN animais mae ON mae.id = filha.mae AND COALESCE(filha.terceiro_mae, 0) = 0
         INNER JOIN animais avo ON avo.id = mae.mae AND COALESCE(mae.terceiro_mae, 0) = 0',
        "WHERE jc.id_julgamento = " . (int)$idEvento . "
           AND filha.sexo = 'Fêmea' AND mae.sexo = 'Fêmea' AND avo.sexo = 'Fêmea'
           AND mae.status = 0 AND avo.status = 0
           AND filha.mae REGEXP '^[0-9]+$' AND mae.mae REGEXP '^[0-9]+$'
           AND filha.id <> mae.id AND filha.id <> avo.id AND mae.id <> avo.id
           AND YEAR(mae.data_de_nascimento) > 0
           AND mae.data_de_nascimento <= evento.data
           AND TIMESTAMPDIFF(MONTH, mae.data_de_nascimento, evento.data) < 36
         ORDER BY filha.nome ASC, filha.id ASC",
        'DISTINCT filha.id filha_id, filha.nome filha_nome, filha.tipo filha_tipo,
         mae.id mae_id, mae.nome mae_nome, mae.data_de_nascimento mae_nascimento,
         avo.id avo_id, avo.nome avo_nome'
    ) ?: array();
}
