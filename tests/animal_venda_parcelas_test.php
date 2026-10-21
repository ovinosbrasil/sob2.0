<?php
function DBRead($table, $where = '') {
    if ($table === 'mercado') return array(array('id'=>7));
    if ($table === 'animais') return array(array('nome'=>'BURIA TE 9489', 'pai'=>0, 'mae'=>0, 'terceiro_pai'=>1, 'terceiro_mae'=>1));
    return false;
}
$gravacoes = array();
function DBCreate($table, $dados) { $GLOBALS['gravacoes'][$table][] = $dados; return true; }
function DBUpdate($table, $dados, $where) { return true; }
function DBDelete($table, $where) { return true; }
$_GET = array('id_animal'=>42);
$_SERVER['REQUEST_METHOD']='POST';
$_SESSION=array('_csrf_sob'=>'token-teste');
$_POST = array('comprador'=>'Cliente', 'data_venda'=>'31/01/2026', 'parcelas'=>'3', 'tipo_venda'=>'Fazenda', 'forma'=>'Pix', 'valor'=>'100.00', 'observacoes'=>'');
$_POST['_csrf_sob']='token-teste';
$source = file_get_contents(__DIR__ . '/../animal/vender/_vender.php');
$source = str_replace('require __DIR__ . "/../../_config.php";', '', $source);
$source = str_replace('__DIR__', var_export(realpath(__DIR__ . '/../animal/vender'), true), $source);
ob_start(); eval('?>'.$source); ob_end_clean();
$financeiro = $gravacoes['controle_financeiro'];
if (array_column($financeiro, 'valor') !== array('33.34','33.33','33.33')) throw new RuntimeException('Divisão ou arredondamento incorreto.');
if (array_column($financeiro, 'data') !== array('2026-01-31','2026-02-28','2026-03-31')) throw new RuntimeException('Vencimentos inválidos.');
if (array_column($financeiro, 'titulo') !== array('Venda animal - BURIA TE 9489 1/3','Venda animal - BURIA TE 9489 2/3','Venda animal - BURIA TE 9489 3/3')) throw new RuntimeException('Descrição incorreta.');
if (array_unique(array_column($financeiro, 'forma_de_pagamento')) !== array('Pix')) throw new RuntimeException('Forma de pagamento incorreta.');
if ($gravacoes['vendas'][0]['preco_de_venda'] !== '100.00') throw new RuntimeException('Venda deve manter o valor total.');
echo "Venda na ficha do animal: total, Pix, parcelas, datas e descrições OK\n";
