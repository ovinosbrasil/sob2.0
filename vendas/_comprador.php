<?php
require_once __DIR__ . "/../_config.php";
$respostaJson = ($_POST['formato'] ?? '') === 'json';
header('Content-Type: ' . ($respostaJson ? 'application/json' : 'text/html') . '; charset=UTF-8');

$nomeInformado = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
if ($nomeInformado === '') {
  if ($respostaJson) {
    http_response_code(422);
    echo json_encode(array('sucesso' => false, 'mensagem' => 'Informe o nome completo do comprador.'));
    exit;
  }
  echo "<script>alert('Informe o nome completo do comprador.'); history.back();</script>";
  exit;
}
$nome = DBEscape($nomeInformado);
$comprador = DBRead('mercado', "WHERE nome = '$nome'");

// TESTE EVENTO
if(($comprador[0]['id'] ?? 0) > 0){
  if ($respostaJson) {
    http_response_code(409);
    echo json_encode(array('sucesso' => false, 'mensagem' => 'Este comprador já está cadastrado. Selecione-o na pesquisa.'));
    exit;
  }
  echo "<script type=\"text/javascript\"> alert(\"Comprador já existe.Tente novamente\"); </script>
  <script language='javascript'>history.back()</script>";
}else{

$dados = array(
	'nome'	=> $nomeInformado,
	'celular1'	=> ($_POST['celular'] ?? ''),
  'telefone2' => '',
  'email'	=> ($_POST['email'] ?? ''),
  'cpf'  => ($_POST['cpf'] ?? ''),
  'cod_criador'  => ($_POST['cod'] ?? ''),
  'cidade'  => ($_POST['cidade'] ?? ''),
  'estado'  => ($_POST['estado'] ?? '')
);

$idNovoComprador = DBcreate('mercado', $dados, true);
if ($respostaJson) {
  if (!$idNovoComprador) {
    http_response_code(500);
    echo json_encode(array('sucesso' => false, 'mensagem' => 'Não foi possível cadastrar o comprador. Tente novamente.'));
  } else {
    echo json_encode(array('sucesso' => true, 'comprador' => array('id' => (int)$idNovoComprador, 'nome' => $nomeInformado)));
  }
  exit;
}
$comprador = DBRead('mercado', "WHERE nome = '$nome'");
$id_comprador = $comprador[0]['id'];

echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../geral.php?pg=comprador&id_comprador=$id_comprador'>";
}
?>
