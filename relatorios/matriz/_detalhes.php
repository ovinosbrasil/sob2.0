<?php
require_once __DIR__ . '/../../_config.php';
header('Content-Type: text/html; charset=utf-8');
$id_animal = filter_var($_GET['id_animal'] ?? 0, FILTER_VALIDATE_INT);
$filtro = filter_var($_GET['filtro'] ?? 1, FILTER_VALIDATE_INT);
$tabelas = array(1 => 'tabela_venda.php', 2 => 'tabela_qualidade.php', 3 => 'tabela_intervalo.php', 4 => 'tabela_gmd.php', 5 => 'tabela_tipificacao.php', 6 => 'tabela_prolificidade.php');
if (!$id_animal || $id_animal < 1 || !isset($tabelas[$filtro])) { http_response_code(400); exit('Relatório inválido.'); }
$animal = DBRead('animais', "WHERE id = '$id_animal'") ?: array();
if (!$animal) { http_response_code(404); exit('Animal não encontrado.'); }
require __DIR__ . '/' . $tabelas[$filtro];
