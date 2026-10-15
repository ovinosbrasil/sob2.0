<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
list($idAnimal, $idEvento) = validarAnimalExposicao();
gravarExposicao('DBUpdate', 'animais_evento', array('leilao' => 1), "id_animal = '$idAnimal' AND id_julgamento = '$idEvento'");
avisarExposicao('success', 'Animal adicionado ao leilão com sucesso.', $idEvento);
