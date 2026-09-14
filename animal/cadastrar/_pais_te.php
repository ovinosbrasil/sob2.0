<?php
function paisDoLoteTe($lote)
{
    $pais = [];
    foreach (['', '_2'] as $sufixo) {
        $id = (int)($lote['id_pai' . $sufixo] ?? 0);
        $terceiro = empty($lote['terceiro_pai' . $sufixo]) ? 0 : 1;
        if ($id <= 0) {
            continue;
        }
        $animal = DBRead($terceiro ? 'terceiros' : 'animais', "WHERE id = '$id' AND sexo = 'Macho'");
        if (!empty($animal[0])) {
            $pais[$terceiro . ':' . $id] = [
                'id' => $id,
                'terceiro' => $terceiro,
                'nome' => $animal[0]['nome'],
            ];
        }
    }
    return $pais;
}
