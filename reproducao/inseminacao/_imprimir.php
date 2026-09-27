<?php
require_once __DIR__ . '/../../_config.php';
require_once __DIR__ . '/_relatorio_inseminacao_pdf.php';

$idLote = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
if (!$idLote) {
    header('Location: ../../geral.php?pg=lista_inseminacao');
    exit;
}
$idLote = (int)$idLote;
$lotes = DBRead('inseminacao', "WHERE id = '$idLote'") ?: array();
if (!$lotes) {
    header('Location: ../../geral.php?pg=lista_inseminacao');
    exit;
}

$lote = $lotes[0];
$usuarios = DBRead('admin') ?: array();
$usuario = $usuarios[0] ?? array();
$idMacho = (int)$lote['id_macho'];
$machos = DBRead(!empty($lote['terceiro']) ? 'terceiros' : 'animais', "WHERE id = '$idMacho'") ?: array();
$macho = $machos[0] ?? array();

$controles = DBRead('inseminacao_controle', "WHERE id_lote = '$idLote' ORDER BY id ASC") ?: array();
$ovelhas = array();
foreach ($controles as $controle) {
    $idAnimal = (int)$controle['id_femea'];
    $terceiro = !empty($controle['terceiro']);
    $animais = DBRead($terceiro ? 'terceiros' : 'animais', "WHERE id = '$idAnimal'") ?: array();
    if ($animais) {
        $ovelhas[] = array('animal' => $animais[0], 'terceiro' => $terceiro);
    }
}

$pdf = new RelatorioInseminacaoPDF($usuario, $lote, $macho);
$paginas = $ovelhas ? array_chunk($ovelhas, 20) : array(array());
foreach ($paginas as $indice => $pagina) {
    $pdf->paginaOvelhas($pagina, $indice + 1);
}

$codigoArquivo = preg_replace('/[^a-zA-Z0-9_-]+/', '-', (string)$lote['codigo']);
$momento = (new DateTimeImmutable('now', new DateTimeZone('America/Bahia')))->format('Y-m-d-His');
$pdf->Output('D', 'inseminacao-artificial-' . trim($codigoArquivo, '-') . '-' . $momento . '.pdf');
exit;
