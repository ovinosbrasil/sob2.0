<?php
function validarFiltrosPrenhezAtual(array $entrada)
{
    $tipo = $entrada['reproducao'] ?? '';
    if (!is_string($tipo) || !in_array($tipo, array('', 'monta', 'inseminacao', 'te'), true)) {
        throw new InvalidArgumentException('Selecione um tipo de reprodução válido.');
    }
    $filtros = array('reproducao'=>$tipo);
    foreach (array('data_inicio', 'data_fim') as $campo) {
        $valor = $entrada[$campo] ?? '';
        $data = is_string($valor) && $valor !== '' ? DateTimeImmutable::createFromFormat('!d/m/Y', $valor) : false;
        if ($valor !== '' && (!$data || $data->format('d/m/Y') !== $valor)) {
            throw new InvalidArgumentException('Informe as datas do lote no formato dd/mm/aaaa.');
        }
        $filtros[$campo] = $data ? $data->format('Y-m-d') : '';
    }
    if ($filtros['data_inicio'] && $filtros['data_fim'] && $filtros['data_inicio'] > $filtros['data_fim']) {
        throw new InvalidArgumentException('A data inicial deve ser menor ou igual à data final.');
    }
    return $filtros;
}

function consultarPrenhezAtual(array $entrada = array())
{
    $filtros = validarFiltrosPrenhezAtual($entrada);
    $hoje = new DateTimeImmutable('today', new DateTimeZone('America/Bahia'));
    $limiteInicio = $hoje->modify('-200 days')->format('Y-m-d');
    $limiteFim = $hoje->format('Y-m-d');
    $configuracoes = array(
        array('controle'=>'monta_controle', 'lote'=>'monta', 'fk'=>'id_monta', 'femea'=>'id_animal', 'data'=>'data_fim', 'dias'=>160, 'tipo'=>'Monta natural', 'pagina'=>'monta'),
        array('controle'=>'inseminacao_controle', 'lote'=>'inseminacao', 'fk'=>'id_lote', 'femea'=>'id_femea', 'data'=>'data', 'dias'=>160, 'tipo'=>'Inseminação artificial', 'pagina'=>'inseminacao'),
        array('controle'=>'transplante_controle', 'lote'=>'transplante', 'fk'=>'id_lote', 'data'=>'data', 'dias'=>161, 'tipo'=>'Transplante de embriões', 'pagina'=>'te'),
    );
    $linhas = array();
    foreach ($configuracoes as $config) {
        if ($filtros['reproducao'] !== '' && $filtros['reproducao'] !== $config['pagina']) { continue; }
        $fonte = "{$config['controle']} c INNER JOIN {$config['lote']} l ON l.id = c.{$config['fk']}";
        if (isset($config['femea'])) {
            $fonte .= " LEFT JOIN animais a ON a.id = c.{$config['femea']} AND COALESCE(c.terceiro, 0) = 0
                LEFT JOIN terceiros t ON t.id = c.{$config['femea']} AND c.terceiro = 1";
            $nome = "COALESCE(a.nome, t.nome, '--') femea, COALESCE(c.terceiro, 0) terceiro";
        } else {
            $nome = "COALESCE(NULLIF(c.receptora, ''), '--') femea, 0 terceiro";
        }
        $campoMacho = $config['pagina'] === 'monta' ? 'id_animal' : ($config['pagina'] === 'te' ? 'id_pai' : 'id_macho');
        $campoTerceiroMacho = $config['pagina'] === 'te' ? 'terceiro_pai' : 'terceiro';
        $fonte .= " LEFT JOIN animais macho ON macho.id = l.$campoMacho AND COALESCE(l.$campoTerceiroMacho, 0) = 0
            LEFT JOIN terceiros macho_externo ON macho_externo.id = l.$campoMacho AND l.$campoTerceiroMacho = 1";
        $previsao = "DATE_ADD(l.{$config['data']}, INTERVAL {$config['dias']} DAY)";
        $embrioes = $config['pagina'] === 'te' ? 'c.n_embrioes' : 'NULL';
        $doadora = 'NULL';
        if ($config['pagina'] === 'te') {
            $fonte .= " LEFT JOIN animais doadora ON doadora.id = l.id_mae AND COALESCE(l.terceiro_mae, 0) = 0
                LEFT JOIN terceiros doadora_externa ON doadora_externa.id = l.id_mae AND l.terceiro_mae = 1";
            $doadora = "COALESCE(doadora.nome, doadora_externa.nome, '--')";
        }
        $periodo = '';
        $campoInicio = $config['pagina'] === 'monta' ? 'data_inicio' : 'data';
        if ($filtros['data_inicio'] !== '') { $periodo .= " AND l.$campoInicio >= '{$filtros['data_inicio']}'"; }
        if ($filtros['data_fim'] !== '') { $periodo .= " AND l.$campoInicio <= '{$filtros['data_fim']}'"; }
        $registros = DBRead($fonte,
            "WHERE c.ultrassom = 1 AND COALESCE(c.status_nascimento, 0) = 0
                AND l.{$config['data']} >= '1000-01-01'
                AND l.$campoInicio >= '$limiteInicio' AND l.$campoInicio <= '$limiteFim' $periodo",
            "c.id controle_id, l.id lote_id, l.codigo, COALESCE(macho.nome, macho_externo.nome, '--') nome_macho,
                $previsao previsao_fim, $nome, $embrioes n_embrioes, $doadora nome_doadora");
        foreach ($registros ?: array() as $registro) {
            $registro['tipo'] = $config['tipo'];
            $registro['pagina'] = $config['pagina'];
            $linhas[] = $registro;
        }
    }
    usort($linhas, function ($a, $b) use ($filtros) {
        if ($filtros['reproducao'] === 'te') {
            return strnatcasecmp($a['codigo'], $b['codigo']) ?: ($a['lote_id'] <=> $b['lote_id']) ?: strnatcasecmp($a['femea'], $b['femea']) ?: ($a['controle_id'] <=> $b['controle_id']);
        }
        return strcmp($a['previsao_fim'], $b['previsao_fim']) ?: strcmp($a['femea'], $b['femea']) ?: ($a['controle_id'] <=> $b['controle_id']);
    });
    return $linhas;
}
