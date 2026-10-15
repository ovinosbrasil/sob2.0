<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/_alertas_compradores.php';
$dados = dadosFormularioComprador();
$nome = DBEscape($dados['nome']);
if (DBRead('mercado', "WHERE nome = '$nome'")) finalizarComprador('warning', 'Este comprador já está cadastrado. Selecione-o na pesquisa.', 0, 409);
$dados['telefone2'] = '';
$id = DBCreate('mercado', $dados, true);
if (!$id) finalizarComprador('danger', 'Não foi possível cadastrar o comprador. Tente novamente.', 0, 500);
finalizarComprador('success', 'Comprador cadastrado com sucesso.', $id, 200, array('comprador'=>array('id'=>(int)$id, 'nome'=>$dados['nome'])));
