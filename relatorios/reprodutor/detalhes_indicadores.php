<?php
require_once __DIR__ . '/indicadores_reprodutores.php';
function renderDetalhesReprodutor($id, $aba)
{
    $todos = consultarIndicadoresReprodutores();
    $atual = array(); $rebanho = array();
    foreach ($todos as $item) {
        if ((int)$item['id_macho'] === (int)$id) $atual = $item;
        if ((int)$item['status_animal'] === 0) $rebanho[(int)$item['id_macho']] = $item;
    }
    if (!$atual) { echo '<p class="text-muted">Nenhuma cria vinculada a este reprodutor.</p>'; return; }
    $media = function ($campo) use (&$rebanho) {
        $valores = array();
        foreach ($rebanho as $item) if (isset($item[$campo])) $valores[] = (float)$item[$campo];
        return $valores ? array_sum($valores) / count($valores) : null;
    };
    $formatar = function ($valor, $campo) {
        if ($valor === null) return 'Não informado';
        if ($campo === 'gmd') return number_format($valor * 1000, 2, ',', '.') . ' g/dia';
        if (strpos($campo, 'venda') !== false) return 'R$ ' . number_format($valor, 2, ',', '.');
        return number_format($valor, $campo === 'qtd_mortes' ? 1 : 2, ',', '.') . ($campo === 'qtd_mortes' ? '%' : '');
    };
    $colunas = array();
    if ($aba === 1) $colunas = array('venda_macho'=>'Média de venda — machos', 'venda_femea'=>'Média de venda — fêmeas', 'venda_geral'=>'Média geral de venda');
    if ($aba === 2) $colunas = array('nota'=>'Qualidade das crias');
    if ($aba === 3) $colunas = array('qtd_mortes'=>'Mortalidade no nascimento');
    if ($aba === 4) $colunas = array('gmd'=>'Ganho médio diário');
    if ($aba === 5) {
        $avaliacoes = consultarTipificacaoReprodutores();
        $porPai = array_column($avaliacoes, null, 'id_macho');
        foreach ($rebanho as $pai => &$item) $item = $porPai[$pai] ?? array();
        unset($item);
        $atual = $porPai[$id] ?? array();
        $colunas = array('tamanho'=>'Pesagem', 'cabeca'=>'Cabeça', 'pescoco'=>'Pescoço', 'quarto_anterior'=>'Quarto anterior', 'barril'=>'Barril', 'quarto_posterior'=>'Quarto posterior', 'comprimento'=>'Comprimento', 'orgao'=>'Órgão sexual', 'distribuicao'=>'Gordura', 'cobertura'=>'Cobertura', 'cor'=>'Cor', 'conformacao'=>'Conformação');
    }
    $h = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
    echo '<table class="table table-bordered table-striped"><thead><tr><th>Indicador</th><th>Reprodutor</th><th>Média do rebanho</th></tr></thead><tbody>';
    foreach ($colunas as $campo => $titulo) {
        $valor = isset($atual[$campo]) ? (float)$atual[$campo] : null;
        $referencia = $media($campo);
        $cor = 'inherit';
        if ($valor !== null && $referencia !== null && abs($valor - $referencia) > 0.000001) $cor = ($campo === 'qtd_mortes' ? $valor < $referencia : $valor > $referencia) ? '#008d4c' : '#dd4b39';
        echo '<tr><td>' . $h($titulo) . '</td><td style="color:' . $cor . ';">' . $h($formatar($valor, $campo)) . '</td><td>' . $h($formatar($referencia, $campo)) . '</td></tr>';
    }
    echo '</tbody></table>';
    if ($aba === 1) echo '<p class="text-muted">' . (int)$atual['qtd_vendas'] . ' venda(s) desde 01/01/2011. Valor total: ' . $h($formatar($atual['total_vendas'], 'total_vendas')) . '.</p>';
    if ($aba === 2) {
        echo '<p class="text-muted">' . (int)$atual['qtd_crias'] . ' crias, ' . (int)$atual['qtd_avaliadas'] . ' avaliadas. Qualidade: média do tipo maior que zero.</p>';
        echo '<table class="table table-bordered table-striped"><thead><tr><th>Tipo</th><th>Crias</th><th>Percentual das avaliadas</th></tr></thead><tbody>';
        foreach (array(2,3,4,5) as $tipo) echo '<tr><td>' . $tipo . '</td><td>' . (int)$atual['tipo'.$tipo] . '</td><td>' . number_format($atual['qtd_avaliadas'] ? $atual['tipo'.$tipo] * 100 / $atual['qtd_avaliadas'] : 0, 1, ',', '.') . '%</td></tr>';
        echo '</tbody></table>';
    }
    if ($aba === 3) echo '<p class="text-muted">Percentual de todas as crias com causa da perda “Nascimento”.</p>';
    if ($aba === 4) echo '<p class="text-muted">Média de (peso3 − peso inicial) / dias entre nascimento e data3. Apenas pesos positivos e intervalos válidos.</p>';
    if ($aba === 5) echo '<p class="text-muted">Uma avaliação por cria: segunda avaliação quando disponível; caso contrário, primeira avaliação. Registros repetidos usam o maior ID.</p>';
}
