<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas_compradores.php';
$id = filter_var($_GET['id_comprador'] ?? 0, FILTER_VALIDATE_INT);
if (!$id || $id < 1 || !DBRead('mercado', "WHERE id = '$id'")) finalizarComprador('warning', 'Comprador não encontrado. Atualize a página e tente novamente.', 0, 404);
$idRetornoComprador = $id;
$dados = dadosFormularioComprador($id);
$nome = DBEscape($dados['nome']);
if (DBRead('mercado', "WHERE nome = '$nome' AND id != '$id'")) finalizarComprador('warning', 'Já existe outro comprador com esse nome.', $id, 409);
if (!DBUpdate('mercado', $dados, "id = '$id'")) finalizarComprador('danger', 'Não foi possível atualizar o comprador. Tente novamente.', $id, 500);
finalizarComprador('success', 'Comprador atualizado com sucesso.', $id);
