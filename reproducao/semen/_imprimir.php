<?php
require_once __DIR__ . '/../../_config.php';
require_once __DIR__ . '/_relatorio_pdf.php';
$pdf = new RelatorioSemenPDF();
$pdf->AddPage();
$offset = 0;
do {
    $registros = DBRead(
        'semen s LEFT JOIN animais a ON a.id = s.id_animal AND s.terceiro = 0 LEFT JOIN terceiros t ON t.id = s.id_animal AND s.terceiro = 1',
        "WHERE s.qtd > 0 ORDER BY s.data DESC, s.id DESC LIMIT $offset,200",
        "s.*, COALESCE(NULLIF(a.nome,''), NULLIF(t.nome,''), 'Animal não encontrado') AS nome_macho"
    ) ?: array();
    foreach ($registros as $registro) $pdf->registro($registro);
    $offset += count($registros);
} while (count($registros) === 200);
if ($offset === 0) $pdf->vazio();
$pdf->Output('D', 'banco-semen-' . (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His') . '.pdf');
exit;
