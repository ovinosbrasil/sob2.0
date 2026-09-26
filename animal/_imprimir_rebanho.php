<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_filtros_rebanho.php';
require_once __DIR__ . '/_relatorio_rebanho_pdf.php';

$filtros = filtrosRebanho($_GET);
list($fonte, $condicao) = fonteRebanho($filtros);
$pdf = new RelatorioRebanhoPDF();
$pdf->AddPage();
$numero = 0;
// Leitura em lotes para não carregar todos os animais de uma vez.
do {
    $animais = DBRead($fonte, "$condicao ORDER BY id DESC, origem ASC LIMIT $numero,200") ?: array();
    foreach ($animais as $animal) {
        $pdf->animal($animal, ++$numero);
    }
} while (count($animais) === 200);
if ($numero === 0) { $pdf->vazio(); }
$pdf->Output('D', 'rebanho-' . (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His') . '.pdf');
exit;
