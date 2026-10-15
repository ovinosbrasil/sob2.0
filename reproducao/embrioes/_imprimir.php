<?php
require_once __DIR__ . '/../../_config.php';
require_once __DIR__ . '/_relatorio_pdf.php';
$pdf = new RelatorioEmbrioesPDF();
$pdf->AddPage();
$offset = 0;
do {
    $registros = DBRead(
        'embriao s LEFT JOIN animais a ON a.id = s.pai AND s.terceiro = 0 LEFT JOIN terceiros t ON t.id = s.pai AND s.terceiro = 1 LEFT JOIN animais ma ON ma.id = s.mae AND s.terceiro_mae = 0 LEFT JOIN terceiros mt ON mt.id = s.mae AND s.terceiro_mae = 1',
        "WHERE s.qtd > 0 ORDER BY s.data DESC, s.id DESC LIMIT $offset,200",
        "s.*, COALESCE(NULLIF(a.nome,''), NULLIF(t.nome,''), 'Animal não encontrado') AS nome_macho, COALESCE(NULLIF(ma.nome,''), NULLIF(mt.nome,''), 'Animal não encontrado') AS nome_femea"
    ) ?: array();
    foreach ($registros as $registro) $pdf->registro($registro);
    $offset += count($registros);
} while (count($registros) === 200);
if ($offset === 0) $pdf->vazio();
$pdf->Output('D', 'banco-embrioes-' . (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His') . '.pdf');
exit;
