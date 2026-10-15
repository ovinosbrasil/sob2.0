<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
require_once __DIR__ . '/participantes.php';
$idEvento = filter_var($_GET['id_evento'] ?? 0, FILTER_VALIDATE_INT);
$idAnimal = filter_var($_GET['id_animal'] ?? 0, FILTER_VALIDATE_INT);
$aba = '';
try {
    $alterado = alterarParticipanteExposicao($idEvento, $idAnimal, 'adicionar');
} catch (DomainException $erro) {
    avisarExposicao('warning', $erro->getMessage(), $idEvento, $aba);
}
avisarExposicao($alterado ? 'success' : 'warning', $alterado ? 'Animal adicionado à exposição com sucesso.' : 'Animal já cadastrado nesta exposição.', $idEvento, $aba);
