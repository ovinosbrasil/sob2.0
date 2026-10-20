<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas.php';
require_once __DIR__ . '/registro.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') avisarFinanceiro('warning', 'Envie o formulário para alterar o lançamento.');
$registro = registroFinanceiro();
$vendaFinanceira = vendaRegistroFinanceiro($registro);
if ($vendaFinanceira) $_POST['descricao_alterar'] = textoRegistroFinanceiro($registro['titulo']);
$dados = dadosFinanceiro('alterar', (int)$registro['tipo']);
unset($dados['parcelas']);
if ($vendaFinanceira) unset($dados['titulo']);
if (!$registro['tipo']) unset($dados['categoria']);
// DBUpdate legado recebe os valores já escapados.
$dados = array_map('DBEscape', $dados);
$id = (int)$registro['id'];
gravarFinanceiro('DBUpdate', 'controle_financeiro', $dados, "id = '$id'");
unset($_SESSION['campos_financeiro']);
avisarFinanceiro('success', 'Lançamento alterado com sucesso.');
