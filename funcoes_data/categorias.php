<?php
function calcularIdadeMesesDias($dataNascimento, $dataEvento){
    $nasc = DateTime::createFromFormat('Y-m-d', $dataNascimento);
    $evento = DateTime::createFromFormat('Y-m-d', $dataEvento);
    if (!$nasc || !$evento) return null;
    if ($nasc > $evento) return null;
    $diff = $nasc->diff($evento);
    $meses = $diff->y * 12 + $diff->m;
    $dias = $diff->d;
    return ['meses' => $meses, 'dias' => $dias, 'anos' => $diff->y];
}

function idadeEntraNaFaixa($meses, $dias, $minM, $minD, $maxM, $maxD){
    if ($meses > $minM && $meses < $maxM) return true;
    if ($meses == $minM && $dias >= $minD) return true;
    if ($meses == $maxM && $dias <= $maxD) return true;
    return false;
}

function determinarCategoriaPorIdade($meses, $dias){
    $faixas = [
        ['cat'=>1, 'minM'=>4,  'minD'=>0, 'maxM'=>5,  'maxD'=>0],
        ['cat'=>2, 'minM'=>5,  'minD'=>1, 'maxM'=>6,  'maxD'=>0],
        ['cat'=>3, 'minM'=>6,  'minD'=>1, 'maxM'=>7,  'maxD'=>0],
        ['cat'=>4, 'minM'=>7,  'minD'=>1, 'maxM'=>8,  'maxD'=>0],
        ['cat'=>5, 'minM'=>8,  'minD'=>1, 'maxM'=>9,  'maxD'=>0],
        ['cat'=>6, 'minM'=>9,  'minD'=>1, 'maxM'=>10, 'maxD'=>0],
        ['cat'=>7, 'minM'=>10, 'minD'=>1, 'maxM'=>11, 'maxD'=>0],
        ['cat'=>8, 'minM'=>11, 'minD'=>1, 'maxM'=>12, 'maxD'=>0],
        ['cat'=>9, 'minM'=>12, 'minD'=>1, 'maxM'=>14, 'maxD'=>0],
        ['cat'=>10,'minM'=>14, 'minD'=>1, 'maxM'=>16, 'maxD'=>0],
        ['cat'=>11,'minM'=>16, 'minD'=>1, 'maxM'=>18, 'maxD'=>0],
        ['cat'=>12,'minM'=>18, 'minD'=>1, 'maxM'=>21, 'maxD'=>0],
        ['cat'=>13,'minM'=>21, 'minD'=>1, 'maxM'=>24, 'maxD'=>0],
        ['cat'=>14,'minM'=>24, 'minD'=>1, 'maxM'=>30, 'maxD'=>0],
        ['cat'=>15,'minM'=>30, 'minD'=>1, 'maxM'=>36, 'maxD'=>0],
    ];

    foreach ($faixas as $f) {
        if (idadeEntraNaFaixa($meses, $dias, $f['minM'], $f['minD'], $f['maxM'], $f['maxD'])) {
            return $f['cat'];
        }
    }
    return 0;
}

function determinarCategoriaPorDatas($dataNascimento, $dataEvento){
    $idade = calcularIdadeMesesDias($dataNascimento, $dataEvento);
    if (!$idade) return 0;
    return determinarCategoriaPorIdade($idade['meses'], $idade['dias']);
}

?>
