<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
$dados = dadosEventoExposicao();
$nome = DBEscape($dados['nome']);
if (DBRead('julgamento', "WHERE nome = '$nome'")) {
    avisarExposicao('warning', 'Já existe uma exposição com esse nome.');
}
$idEvento = gravarExposicao('DBCreate', 'julgamento', $dados, true);
if (!$idEvento) avisarExposicao('danger', 'Não foi possível cadastrar a exposição. Tente novamente.');
unset($_SESSION['campos_exposicao']);
avisarExposicao('success', 'Exposição cadastrada com sucesso.', $idEvento);
