<?php
require_once __DIR__ . '/../../_config.php';
$idTe = filter_var($_GET['id_te'] ?? 0, FILTER_VALIDATE_INT);
$idControle = filter_var($_GET['id_lote'] ?? 0, FILTER_VALIDATE_INT);
$valor = filter_var($_GET['valor'] ?? null, FILTER_VALIDATE_INT);
$valido = $idTe > 0 && $idControle > 0 && $valor !== false && $valor !== null && $valor >= 0;
$condicao = "id = '" . (int)$idControle . "' AND id_lote = '" . (int)$idTe . "'";
if ($valido && DBRead('transplante_controle', 'WHERE ' . $condicao) && DBUpdate('transplante_controle', array('n_embrioes'=>$valor), $condicao)) {
    $_SESSION['alerta_te'] = array('tipo'=>'success', 'titulo'=>'Sucesso!', 'mensagem'=>'Quantidade de embriões alterada com sucesso.');
} else {
    $_SESSION['alerta_te'] = array('tipo'=>'warning', 'titulo'=>'Atenção!', 'mensagem'=>'Informe uma quantidade inteira de embriões maior ou igual a zero para uma receptora válida.');
}
header('Location: ../../geral.php?pg=' . ($idTe > 0 ? 'te&id_lote=' . (int)$idTe : 'lista_te'));
exit;
