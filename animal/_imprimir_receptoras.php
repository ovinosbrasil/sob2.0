<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_consulta_receptoras.php';
require_once __DIR__ . '/_relatorio_receptoras_pdf.php';

$busca = isset($_GET['busca']) && is_string($_GET['busca']) ? trim($_GET['busca']) : '';
$pdf = new RelatorioReceptorasPDF();
$pdf->AddPage();
$numero = 0;
$pagina = 1;
do {
    $dados = consultarReceptoras(array('busca' => $busca, 'por_pagina' => 100, 'pag' => $pagina));
    if ($dados['erroReceptoras'] !== '') {
        http_response_code(503);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Não foi possível gerar o PDF. Tente novamente.';
        exit;
    }
    foreach ($dados['receptoras'] as $receptora) {
        $receptora['media_peso'] = $dados['mediasPorParto'][$receptora['id']] ?? null;
        $pdf->animal($receptora, ++$numero);
    }
    ++$pagina;
} while ($pagina <= $dados['totalPaginas']);
if ($numero === 0) { $pdf->vazio(); }
$pdf->Output('D', 'receptoras-' . (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His') . '.pdf');
exit;
