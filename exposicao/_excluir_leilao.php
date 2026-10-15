<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
list($idAnimal, $idEvento) = validarAnimalExposicao();
gravarExposicao('DBUpdate', 'animais_evento', array('leilao' => 0), "id_animal = '$idAnimal' AND id_julgamento = '$idEvento'");
avisarExposicao('success', 'Animal removido do leilão com sucesso.', $idEvento);
