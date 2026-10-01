<?php
set_error_handler(function ($nivel, $mensagem) { throw new RuntimeException($mensagem); });
$registros = false;
$consultas = 0;
function DBRead($tabela, $filtro = '', $campos = '*') {
    global $registros, $consultas;
    if ($tabela === 'doenca') return array(array('id' => 1, 'nome' => 'Pneumonia'), array('id' => 2, 'nome' => 'Verminose'));
    $consultas++;
    if (strpos($filtro, "d.data >= '2026-09-01'") === false || strpos($filtro, 'LEFT JOIN animais') === false) throw new RuntimeException('Consulta incorreta.');
    return $registros;
}
function renderDoencas($filtros) {
    $_GET = $filtros; $_SERVER['REQUEST_METHOD'] = 'GET';
    ob_start(); include __DIR__ . '/../relatorios/relatorio_doencas.php'; return ob_get_clean();
}
function verificarDoencas($ok) { if (!$ok) throw new RuntimeException('Falha no relatório de doenças.'); }
$filtros = array('data_inicial' => '01/09/2026', 'data_final' => '30/09/2026');
$html = renderDoencas($filtros);
verificarDoencas(strpos($html, 'Nenhuma ocorrência') !== false && strpos($html, '0 a 0 de 0') !== false);
$registros = array();
for ($i = 1; $i <= 21; $i++) $registros[] = array('id' => $i, 'id_animal' => 7, 'animal_encontrado' => 7, 'nome_animal' => '<b>Animal</b>', 'id_doenca' => $i === 1 ? 2 : 1, 'nome_doenca' => 'Pneumonia', 'data' => '2026-09-10', 'obs' => '<script>observação</script>');
$html = renderDoencas($filtros + array('pag' => 3));
verificarDoencas(strpos($html, 'Resumo do período') !== false && strpos($html, '95,24%') !== false && strpos($html, '4,76%') !== false);
verificarDoencas($consultas === 2 && strpos($html, '21 a 21 de 21') !== false && strpos($html, '21 ocorrência(s) em 1 animal(is)') !== false);
verificarDoencas(strpos($html, '&lt;b&gt;Animal&lt;/b&gt;') !== false && strpos($html, '<script>observação') === false);
$html = renderDoencas($filtros + array('tipo' => '2'));
verificarDoencas(strpos($html, '1 a 1 de 1') !== false && strpos($html, 'tipo=2') !== false);
verificarDoencas(strpos($html, '100,00%') !== false);
$registros[0]['animal_encontrado'] = null; $registros[0]['id_doenca'] = 99;
$html = renderDoencas($filtros + array('tipo' => '99'));
verificarDoencas(strpos($html, 'Animal não encontrado') !== false && strpos($html, 'código 99') !== false);
$antes = $consultas;
$html = renderDoencas(array('data_inicial' => '31/02/2026', 'data_final' => '30/09/2026'));
verificarDoencas($consultas === $antes && strpos($html, 'Informe datas válidas') !== false);
$html = renderDoencas(array('data_inicial' => '30/09/2026', 'data_final' => '01/09/2026'));
verificarDoencas($consultas === $antes && strpos($html, 'posterior à data inicial') !== false);
echo "Doenças: vazio, filtro, paginação, totais, vínculos ausentes, escape e datas: OK\n";
