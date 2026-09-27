<?php
require_once __DIR__ . '/../../_config.php';
require_once __DIR__ . '/_relatorio_te_pdf.php';

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
if (!$idLote) {
    header('Location: ../../geral.php?pg=lista_te');
    exit;
}
$idLote = (int)$idLote;
$lotes = DBRead('transplante', "WHERE id = '$idLote'") ?: array();
if (!$lotes) {
    header('Location: ../../geral.php?pg=lista_te');
    exit;
}

$lote = $lotes[0];
$usuarios = DBRead('admin') ?: array();
$usuario = $usuarios[0] ?? array();

$idMacho = (int)$lote['id_pai'];
$machos = DBRead(!empty($lote['terceiro_pai']) ? 'terceiros' : 'animais', "WHERE id = '$idMacho'") ?: array();
$macho = $machos[0] ?? array();

$idMachoComplementar = (int)($lote['id_pai_2'] ?? 0);
$machosComplementares = $idMachoComplementar
    ? (DBRead(!empty($lote['terceiro_pai_2']) ? 'terceiros' : 'animais', "WHERE id = '$idMachoComplementar'") ?: array())
    : array();
$machoComplementar = $machosComplementares[0] ?? array();

$idFemea = (int)$lote['id_mae'];
$femeas = DBRead(!empty($lote['terceiro_mae']) ? 'terceiros' : 'animais', "WHERE id = '$idFemea'") ?: array();
$femea = $femeas[0] ?? array();
$receptoras = DBRead('transplante_controle', "WHERE id_lote = '$idLote' ORDER BY id ASC") ?: array();

$pdf = new RelatorioTransplantePDF($usuario, $lote, $macho, $machoComplementar, $femea);
$paginas = $receptoras ? array_chunk($receptoras, 72) : array(array());
foreach ($paginas as $indice => $pagina) {
    $pdf->paginaReceptoras($pagina, $indice + 1);
}

$codigoArquivo = preg_replace('/[^a-zA-Z0-9_-]+/', '-', (string)$lote['codigo']);
$momento = (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His');
$pdf->Output('D', 'transplante-embrioes-' . trim($codigoArquivo, '-') . '-' . $momento . '.pdf');
exit;
