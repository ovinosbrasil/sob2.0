<?php
// Mesma seleção para a listagem, a contagem e o relatório.
function filtrosRebanho(array $entrada)
{
    $opcoes = array(
        'tipo' => array('Todos', 'Rebanho', 'Terceiros'),
        'sexo' => array('', 'Macho', 'Fêmea'),
        'situacao' => array('Todos', 'Vivo', 'Morto', 'Vendido'),
    );
    $filtros = array();
    foreach ($opcoes as $campo => $valores) {
        $valor = $entrada[$campo] ?? $valores[0];
        $filtros[$campo] = in_array($valor, $valores, true) ? $valor : $valores[0];
    }
    return $filtros;
}

function fonteRebanho(array $filtros)
{
    $condicoes = array();
    if ($filtros['sexo'] !== '') {
        $condicoes[] = "sexo = '" . $filtros['sexo'] . "'";
    }
    $situacoes = array('Vivo' => 0, 'Morto' => 1, 'Vendido' => 2);
    if (isset($situacoes[$filtros['situacao']])) {
        $condicoes[] = 'status = ' . $situacoes[$filtros['situacao']];
    }
    if ($filtros['tipo'] !== 'Todos') {
        $condicoes[] = "origem = '" . $filtros['tipo'] . "'";
    }
    $fonte = "(SELECT id, nome, sexo, data_de_nascimento, entrada, tipo, status, tatuagem,
                     'Rebanho' AS origem FROM animais
               UNION ALL
               SELECT id, nome, sexo, NULL, NULL, NULL, NULL, NULL,
                      'Terceiros' AS origem FROM terceiros) AS rebanho_filtrado";
    return array($fonte, $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '');
}

function nascimentoRebanho($valor)
{
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$valor, 0, 10));
    if (!$data || $data->format('Y-m-d') !== substr((string)$valor, 0, 10)) {
        return array('--', '--');
    }
    $idade = $data->diff(new DateTimeImmutable('today'));
    return array($data->format('d/m/Y'), $idade->invert ? '--' : $idade->y . ' Anos ' . $idade->m . ' Meses');
}
