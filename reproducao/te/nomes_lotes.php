<?php
/** Resolve os nomes pelos IDs e pela origem, sem confiar nos textos antigos do lote. */
function nomesLotesTransplante(array $lotes)
{
    $campos = array('pai'=>array('id_pai','terceiro_pai','pai'), 'mae'=>array('id_mae','terceiro_mae','mae'), 'pai_complementar'=>array('id_pai_2','terceiro_pai_2','macho_complementar'));
    $ids = array('animais'=>array(), 'terceiros'=>array());
    foreach ($lotes as $lote) foreach ($campos as $config) {
        $id = (int)($lote[$config[0]] ?? 0);
        if ($id > 0) $ids[!empty($lote[$config[1]]) ? 'terceiros' : 'animais'][$id] = $id;
    }
    $nomes = array();
    foreach ($ids as $tabela=>$lista) {
        $nomes[$tabela] = $lista ? array_column(DBRead($tabela, 'WHERE id IN (' . implode(',', $lista) . ')', 'id, nome') ?: array(), 'nome', 'id') : array();
    }
    foreach ($lotes as &$lote) foreach ($campos as $campo=>$config) {
        $id = (int)($lote[$config[0]] ?? 0);
        $tabela = !empty($lote[$config[1]]) ? 'terceiros' : 'animais';
        $nome = trim((string)($nomes[$tabela][$id] ?? ''));
        $legado = trim((string)($lote[$config[2]] ?? ''));
        if ($nome === '' && $legado !== '' && !is_numeric($legado) && !preg_match('/^[-\s]+$/u', $legado)) $nome = $legado;
        $lote['nome_' . $campo] = $nome !== '' ? $nome : ($id > 0 ? 'Animal não encontrado' : 'Não informado');
    }
    unset($lote);
    return $lotes;
}
