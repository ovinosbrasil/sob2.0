<?
$id_animal = (int)($_GET['id_animal'] ?? 0);
if ($id_animal < 1) { http_response_code(400); exit('Animal inválido.'); }
require_once __DIR__ . "/../../_config.php";
$dados = array(
  'status' => 0
);
DBUpdate('animais', $dados, "id = '$id_animal'");

DBDelete('vendas', "id_animal = '$id_animal'");
DBDelete('controle_financeiro', "id_animal = '$id_animal'");
echo "<META HTTP-EQUIV=REFRESH CONTENT='0; URL=../../geral.php?pg=animal&id_animal=$id_animal&aba=vender'>";
?>
