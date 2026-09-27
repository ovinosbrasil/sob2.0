<?php
require_once __DIR__ . '/../../_config.php';
require_once __DIR__ . '/_relatorio_monta_pdf.php';

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
if (!$idLote) {
    header('Location: ../../geral.php?pg=lista_monta');
    exit;
}
$idLote = (int)$idLote;
$lotes = DBRead('monta', "WHERE id = '$idLote'") ?: array();
if (!$lotes) {
    header('Location: ../../geral.php?pg=lista_monta');
    exit;
}
$lote = $lotes[0];
$usuarios = DBRead('admin') ?: array();
$usuario = $usuarios[0] ?? array();
$idMacho = (int)$lote['id_animal'];
$machos = DBRead(!empty($lote['terceiro']) ? 'terceiros' : 'animais', "WHERE id = '$idMacho'") ?: array();
$macho = $machos[0] ?? array();

$pdf = new RelatorioMontaPDF($usuario, $lote, $macho);
$controles = DBRead('monta_controle', "WHERE id_monta = '$idLote' ORDER BY id ASC") ?: array();
$ovelhas = array();
foreach ($controles as $controle) {
    $idAnimal = (int)$controle['id_animal'];
    $terceiro = !empty($controle['terceiro']);
    $animais = DBRead($terceiro ? 'terceiros' : 'animais', "WHERE id = '$idAnimal'") ?: array();
    if ($animais) {
        $ovelhas[] = array('animal' => $animais[0], 'terceiro' => $terceiro);
    }
}
$paginas = $ovelhas ? array_chunk($ovelhas, 30) : array(array());
foreach ($paginas as $pagina) {
    $pdf->paginaOvelhas($pagina);
}

$codigoArquivo = preg_replace('/[^a-zA-Z0-9_-]+/', '-', (string)$lote['codigo']);
$momento = (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His');
$pdf->Output('D', 'monta-natural-' . trim($codigoArquivo, '-') . '-' . $momento . '.pdf');
exit;
