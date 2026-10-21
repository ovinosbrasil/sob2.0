<?php
set_error_handler(function ($n, $m) { throw new RuntimeException($m); });
function DBRead($table, $where = '') {
    if ($table === 'animais' && strpos($where, 'status') === false) return array(array('pai'=>10, 'mae'=>20, 'terceiro_pai'=>0, 'terceiro_mae'=>0));
    if ($table === 'animais') return $GLOBALS['criasTeste'];
    if ($table === 'vendas') return $GLOBALS['semVendaTeste'] ? false : array(array('id'=>1, 'preco_de_venda'=>100));
    return false;
}
function DBUpdate($table, $dados, $where) { $GLOBALS['updatesTeste'][$table] = $dados; return true; }
function DBDelete($table, $where) { $GLOBALS['deletesTeste'][$table] = $where; return true; }
$source = file_get_contents(__DIR__ . '/../animal/vender/_desfazer_venda.php');
$source = str_replace('require_once __DIR__ . "/../../_config.php";', '', $source);
$source = str_replace('__DIR__',var_export(realpath(__DIR__.'/../animal/vender'),true),$source);
foreach (array(false, array(array('id'=>2, 'sexo'=>'Macho')), array(array('id'=>3, 'sexo'=>'Fêmea'))) as $criasTeste) {
    foreach (array(false, true) as $semVendaTeste) {
        $updatesTeste = $deletesTeste = array();
        $_GET = array('id_animal'=>42);
        $_POST=array('id_animal'=>42,'_csrf_sob'=>'token-teste');
        $_SESSION=array('_csrf_sob'=>'token-teste');
        $_SERVER['REQUEST_METHOD']='POST';
        ob_start(); eval('?>'.$source); ob_end_clean();
        if (isset($updatesTeste['reprodutor'], $updatesTeste['matriz']) || count($updatesTeste) !== 1) throw new RuntimeException('Não deve atualizar médias legadas.');
        if ($updatesTeste['animais']['status'] !== 0 || count($deletesTeste) !== 2 || !isset($deletesTeste['controle_financeiro'], $deletesTeste['vendas'])) throw new RuntimeException('Não concluiu o desfazimento.');
    }
}
echo "Desfazer venda: retorna animal ao rebanho, remove venda e financeiro sem atualizar médias legadas OK\n";
