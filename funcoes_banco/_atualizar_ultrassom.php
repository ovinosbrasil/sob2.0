<?
set_time_limit(9999999999999);
include "../_config.php";

$dados = array(
  'ultrassom' => 1,
);
$monta = DBUpdate('monta_controle', $dados, "status_nascimento = '1'");


$dados = array(
  'ultrassom' => 1,
);
$monta = DBUpdate('inseminacao_controle', $dados, "status_nascimento = '1'");

$dados = array(
  'ultrassom' => 1,
);
$monta = DBUpdate('transplante_controle', $dados, "status_nascimento = '1'");
?>
