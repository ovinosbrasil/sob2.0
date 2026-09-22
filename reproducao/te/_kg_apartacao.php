<?php
// Soma apenas animais da receptora nascidos na janela prevista do lote (146 a 161 dias).
// A média considera apenas as receptoras com pesagem informada.
function kgApartacaoDoLote($idLote)
{
    $idLote = (int) $idLote;
    $totais = DBRead('animais AS a', "WHERE a.id_receptora IS NOT NULL AND a.peso2 > 0
          AND EXISTS (SELECT 1 FROM transplante_controle AS tc
                      INNER JOIN transplante AS t ON t.id = tc.id_lote
                      WHERE tc.id_lote = $idLote AND tc.id_receptora = a.id_receptora
                        AND a.data_de_nascimento BETWEEN DATE_ADD(t.data, INTERVAL 146 DAY)
                                                     AND DATE_ADD(t.data, INTERVAL 161 DAY))
        GROUP BY a.id_receptora", 'a.id_receptora, SUM(a.peso2) AS kg_apartacao');
    $somas = array();
    foreach (($totais ?: array()) as $cria) {
        $idReceptora = (int) $cria['id_receptora'];
        $somas[$idReceptora] = (float) $cria['kg_apartacao'];
    }
    return array('somas' => $somas, 'media' => $somas ? array_sum($somas) / count($somas) : null);
}
