<?php
function paginarRegistrosPrevia(array $registros, $pesquisa, $pagina, $limite) {
    $normalizar = function ($texto) {
        return mb_strtolower(strtr($texto, array('á'=>'a','à'=>'a','ã'=>'a','â'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ü'=>'u','ç'=>'c','Á'=>'a','À'=>'a','Ã'=>'a','Â'=>'a','É'=>'e','Ê'=>'e','Í'=>'i','Ó'=>'o','Ô'=>'o','Õ'=>'o','Ú'=>'u','Ü'=>'u','Ç'=>'c')), 'UTF-8');
    };
    $termo = $normalizar(trim($pesquisa));
    $filtrados = array();
    foreach ($registros as $registro) {
        $texto = implode(' ', array_intersect_key($registro['dados'], array_flip(array('Nome', 'FBB/FBE', 'Tat.'))));
        foreach ($registro['animais_banco'] ?? array() as $animal) {
            $texto .= ' ' . implode(' ', array_intersect_key($animal, array_flip(array('nome', 'fbb', 'tatuagem'))));
        }
        if ($termo === '' || strpos($normalizar($texto), $termo) !== false) $filtrados[] = $registro;
    }
    $limite = in_array((int)$limite, array(10,20,50,100), true) ? (int)$limite : 10;
    $total = count($filtrados);
    $paginas = max(1, (int)ceil($total / $limite));
    $pagina = max(1, min($paginas, (int)$pagina));
    return array('registros'=>array_slice($filtrados, ($pagina-1)*$limite, $limite), 'total'=>$total, 'pagina'=>$pagina, 'paginas'=>$paginas, 'limite'=>$limite);
}
