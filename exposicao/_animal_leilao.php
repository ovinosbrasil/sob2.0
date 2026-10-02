<?php
require __DIR__ . '/../_config.php';

$idAnimal = filter_input(INPUT_GET, 'id_animal', FILTER_VALIDATE_INT);
$idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
if ($idAnimal && $idEvento) {
    DBUpdate('animais_evento', array('leilao' => 1), "id_animal = '" . (int)$idAnimal . "' AND id_julgamento = '" . (int)$idEvento . "'");
}

header('Location: ../geral.php?pg=exposicao&id_exposicao=' . (int)$idEvento, true, 303);
exit;
