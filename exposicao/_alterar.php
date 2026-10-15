<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
$id_evento = filter_var($_GET['id_evento'] ?? 0, FILTER_VALIDATE_INT);
if (!$id_evento || $id_evento < 1 || !DBRead('julgamento', "WHERE id = '$id_evento'")) {
    avisarExposicao('warning', 'Exposição não encontrada. Atualize a página e tente novamente.');
}
$dados = dadosEventoExposicao($id_evento);
$data = $dados['data'];
$nome = DBEscape($dados['nome']);
if (DBRead('julgamento', "WHERE nome = '$nome' AND id != '$id_evento'")) {
    avisarExposicao('warning', 'Já existe outra exposição com esse nome.', $id_evento);
}
gravarExposicao('DBUpdate', 'julgamento', $dados, "id = '$id_evento'");


$evento = DBRead('julgamento_controle', "WHERE id_julgamento ='$id_evento'");
require_once __DIR__ . "/../funcoes_data/categorias.php";
foreach (($evento ?: array()) as $evento_) {
  $id_animal = $evento_['id_animal'];
  $animal = DBRead('animais',"WHERE id = '$id_animal'");
  $idade = calcularIdadeMesesDias($animal[0]['data_de_nascimento'], $data);
  if (!$idade) {
    $meses = 0; $dias_final = 0; $categoria = 0;
  } else {
    $meses = $idade['meses'];
    $dias_final = $idade['dias'];
    $categoria = determinarCategoriaPorIdade($meses, $dias_final);
  }

  $dados = array(
      'categoria'	=> $categoria,
      'meses'  => $meses,
  );

  gravarExposicao('DBUpdate', 'julgamento_controle', $dados, "id_animal = '$id_animal' AND id_julgamento = '$id_evento'");
}

unset($_SESSION['campos_exposicao']);
avisarExposicao('success', 'Exposição atualizada com sucesso.', $id_evento);
