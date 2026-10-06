<?php
require_once __DIR__ . '/../../_config.php';
require_once __DIR__ . '/_prenhez_atual.php';
require_once __DIR__ . '/_relatorio_prenhez_pdf.php';

$entrada = array();
foreach (array('reproducao', 'data_inicio', 'data_fim') as $campo) {
    $entrada[$campo] = isset($_GET[$campo]) && is_string($_GET[$campo]) ? trim($_GET[$campo]) : '';
}
try { $linhas = consultarPrenhezAtual($entrada); }
catch (InvalidArgumentException $erro) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $erro->getMessage();
    exit;
}
$pdf = new RelatorioPrenhezPDF($entrada);
$pdf->gerar($linhas);
$momento = (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His');
$pdf->Output('D', 'prenhez-atual-' . $momento . '.pdf');
exit;
