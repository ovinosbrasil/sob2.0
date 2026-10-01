<?php
set_error_handler(function ($nivel, $mensagem) { throw new RuntimeException($mensagem); });
$consultas = 0;
$fixtureMortes = false;
function DBRead($tabela, $filtro) {
    global $consultas, $fixtureMortes;
    $consultas++;
    if (strpos($filtro, "2026-09-01") === false || strpos($filtro, "2026-09-30") === false) throw new RuntimeException('Período incorreto.');
    return $fixtureMortes;
}
function renderMortes($parametros) {
    $_GET = $parametros;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    ob_start();
    include __DIR__ . '/../relatorios/relatorio_mortes.php';
    return ob_get_clean();
}
function verificarMortes($ok) { if (!$ok) throw new RuntimeException('Falha no relatório de mortes.'); }
$filtros = array('data_inicial' => '01/09/2026', 'data_final' => '30/09/2026');
$html = renderMortes($filtros);
verificarMortes(strpos($html, 'Nenhuma morte encontrada') !== false && strpos($html, '0 a 0 de 0') !== false);
$fixtureMortes = array();
for ($i = 1; $i <= 21; $i++) $fixtureMortes[] = array('id' => $i, 'status' => $i === 1 ? 5 : 1, 'nome' => '<script>Animal</script>', 'data_de_saida' => '2026-09-10', 'causa_da_perda' => $i === 2 ? '' : 'Nascimento', 'observacoes_de_saida' => '<b>Observação</b>');
$html = renderMortes($filtros + array('pag' => 3));
verificarMortes(strpos($html, '21 a 21 de 21') !== false && strpos($html, 'Abate') !== false && strpos($html, 'Não informado') !== false);
verificarMortes(strpos($html, '<script>Animal') === false && strpos($html, '&lt;script&gt;Animal') !== false);
verificarMortes(strpos($html, '90,48%') !== false && strpos($html, 'data_inicial=01%2F09%2F2026') !== false);
$html = renderMortes($filtros + array('tipo' => 'Abate'));
verificarMortes(strpos($html, '1 a 1 de 1') !== false && strpos($html, 'tipo=Abate') !== false);
$html = renderMortes($filtros + array('tipo' => 'Não informado'));
verificarMortes(strpos($html, '1 a 1 de 1') !== false);
$html = renderMortes($filtros + array('tipo' => 'Nascimento', 'pag' => 2));
verificarMortes(strpos($html, '11 a 19 de 19') !== false);
$html = renderMortes($filtros + array('tipo' => 'Acidente'));
verificarMortes(strpos($html, 'Nenhuma morte encontrada') !== false);
$antes = $consultas;
$html = renderMortes(array('data_inicial' => '31/02/2026', 'data_final' => '30/09/2026'));
verificarMortes($consultas === $antes && strpos($html, 'Informe datas válidas') !== false);
$html = renderMortes(array('data_inicial' => '30/09/2026', 'data_final' => '01/09/2026'));
verificarMortes($consultas === $antes && strpos($html, 'posterior à data inicial') !== false);
echo "Relatório: vazio sem avisos, período, paginação, totais, escape e datas inválidas: OK\n";
