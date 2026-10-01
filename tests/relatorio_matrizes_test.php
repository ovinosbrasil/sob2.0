<?php
set_error_handler(function ($nivel, $mensagem) { throw new RuntimeException($mensagem); });
$registros = false; $consultas = 0;
function DBRead($tabela, $filtro = '', $campos = '*') { global $registros, $consultas; $consultas++; return $registros; }
function renderRep($filtros) { $_GET = $filtros; ob_start(); include __DIR__ . '/../relatorios/matriz/relatorio_matrizes.php'; return ob_get_clean(); }
function exigirRep($ok) { if (!$ok) throw new RuntimeException('Falha no relatório de matrizes.'); }
$html = renderRep(array());
exigirRep(strpos($html, 'Nenhuma matriz encontrada') !== false && strpos($html, '0 a 0 de 0') !== false);
$registros = array();
for ($i = 1; $i <= 21; $i++) $registros[] = array('id' => $i, 'id_femea' => $i, 'animal_encontrado' => $i, 'nome_animal' => '<b>Animal ' . $i . '</b>', 'status_animal' => $i === 1 ? 1 : 0, 'qtd_crias' => $i, 'qtd_avaliadas' => 0, 'nota' => 0, 'venda_geral' => 0, 'qtd_mortes' => 0, 'gmd' => 0);
$html = renderRep(array('pag' => 3));
exigirRep($consultas === 2 && strpos($html, '21 a 21 de 21') !== false && strpos($html, '&lt;b&gt;Animal 1&lt;/b&gt;') !== false);
$html = renderRep(array('filtro2' => 'Mortos/Vendidos', 'filtro' => 5));
exigirRep(strpos($html, '1 a 1 de 1') !== false && strpos($html, 'aria-sort="ascending"') !== false && strpos($html, 'filtro2=Mortos%2FVendidos') !== false);
$html = renderRep(array('filtro2' => 'Rebanho', 'por_pagina' => 100));
exigirRep(strpos($html, '1 a 20 de 20') !== false);
$html = renderRep(array('femea' => 'animal 21'));
exigirRep(strpos($html, '1 a 1 de 1') !== false && strpos($html, 'femea=animal+21') !== false);
exigirRep(strpos($html, 'aria-label="Dicas sobre os indicadores"') !== false);
$html = renderRep(array('femea' => 'inexistente'));
exigirRep(strpos($html, 'Nenhuma matriz encontrada') !== false);
$registros[0]['animal_encontrado'] = null;
$html = renderRep(array('filtro2' => 'Mortos/Vendidos'));
exigirRep(strpos($html, 'Nenhuma matriz encontrada') !== false);
$html = renderRep(array('filtro' => 'inválido', 'filtro2' => array(), 'por_pagina' => 999));
exigirRep(strpos($html, '1 a 10 de 20') !== false);
echo "Matrizes: vazio, zeros, ordenação, filtros, paginação e vínculos ausentes: OK\n";
