<?php
set_error_handler(function ($nivel, $mensagem) { throw new RuntimeException($mensagem); });
$consultas = 0;
$registros = false;
function DBRead($tabela, $filtro) {
    global $consultas, $registros;
    $consultas++;
    if (strpos($filtro, "data_de_nascimento >= '2026-09-01'") === false || strpos($filtro, "data_de_nascimento <= '2026-09-30'") === false) throw new RuntimeException('Período incorreto.');
    return $registros;
}
function renderNascimentos($filtros, $metodo = 'GET') {
    $_SERVER['REQUEST_METHOD'] = $metodo;
    $_GET = $metodo === 'GET' ? $filtros : array();
    $_POST = $metodo === 'POST' ? $filtros : array();
    ob_start();
    include __DIR__ . '/../relatorios/relatorio_nascimentos.php';
    return ob_get_clean();
}
function verificarNascimentos($condicao) { if (!$condicao) throw new RuntimeException('Falha no relatório de nascimentos.'); }
$filtros = array('data_inicial' => '01/09/2026', 'data_final' => '30/09/2026');
$html = renderNascimentos($filtros);
verificarNascimentos($consultas === 1 && strpos($html, 'Nenhum nascimento encontrado') !== false && strpos($html, '0 a 0 de 0') !== false);
$registros = array();
for ($i = 1; $i <= 21; $i++) $registros[] = array('id' => $i, 'nome' => '<b>Animal</b>', 'data_de_nascimento' => '2026-09-10', 'tipo_reproducao' => $i === 1 ? 'Embrionagem' : ($i === 2 ? '' : 'Monta Natural'), 'causa_da_perda' => $i === 1 ? 'Nascimento' : '');
$html = renderNascimentos($filtros + array('pag' => 3));
verificarNascimentos($consultas === 2 && strpos($html, '21 a 21 de 21') !== false && strpos($html, '90,48%') !== false);
verificarNascimentos(strpos($html, '<b>Animal</b>') === false && strpos($html, '&lt;b&gt;Animal&lt;/b&gt;') !== false);
$html = renderNascimentos($filtros + array('tipo' => 'Embrionagem'));
verificarNascimentos(strpos($html, '1 a 1 de 1') !== false && strpos($html, '<td>Sim</td>') !== false && strpos($html, 'tipo=Embrionagem') !== false);
$html = renderNascimentos($filtros + array('tipo' => 'Não informado'), 'POST');
verificarNascimentos(strpos($html, '1 a 1 de 1') !== false && strpos($html, '<td>Não</td>') !== false);
$antes = $consultas;
$html = renderNascimentos(array('data_inicial' => '31/02/2026', 'data_final' => '30/09/2026'));
verificarNascimentos($consultas === $antes && strpos($html, 'Informe datas válidas') !== false);
$html = renderNascimentos(array('data_inicial' => '30/09/2026', 'data_final' => '01/09/2026'));
verificarNascimentos($consultas === $antes && strpos($html, 'posterior à data inicial') !== false);
echo "Nascimentos: vazio sem avisos, consulta única, totais, reprodução, mortes no nascimento, paginação e datas: OK\n";
