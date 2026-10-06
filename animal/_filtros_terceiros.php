<?php
function filtrosTerceiros(array $entrada)
{
    $sexo = $entrada['sexo'] ?? '';
    $situacao = $entrada['situacao'] ?? 'todos';
    return array(
        'sexo' => in_array($sexo, array('', 'Macho', 'Fêmea'), true) ? $sexo : '',
        'situacao' => in_array($situacao, array('todos', 'ativos', 'inativos'), true) ? $situacao : 'todos',
    );
}

function condicaoTerceiros(array $filtros)
{
    $condicoes = array();
    if ($filtros['sexo'] !== '') { $condicoes[] = "sexo = '" . $filtros['sexo'] . "'"; }
    if ($filtros['situacao'] !== 'todos') {
        $condicoes[] = 'ativo = ' . ($filtros['situacao'] === 'ativos' ? '1' : '0');
    }
    return $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';
}
