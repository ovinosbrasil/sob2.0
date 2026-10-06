<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_filtros_terceiros.php';
require_once __DIR__ . '/_relatorio_terceiros_pdf.php';

$condicao = condicaoTerceiros(filtrosTerceiros($_GET));
$pdf = new RelatorioTerceirosPDF();
$pdf->AddPage();
$numero = 0;
// Exporta todos os resultados dos filtros, independentemente da paginação da tela.
do {
    $animais = DBRead('terceiros', "$condicao ORDER BY id DESC LIMIT $numero,200") ?: array();
    foreach ($animais as $animal) { $pdf->animal($animal, ++$numero); }
} while (count($animais) === 200);
if ($numero === 0) { $pdf->vazio(); }
$pdf->Output('D', 'terceiros-' . (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His') . '.pdf');
exit;
