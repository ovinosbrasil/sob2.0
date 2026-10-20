<?php
// Executa as ações reais em processos isolados com banco simulado, sem acessar dados reais.
if (isset($argv[1])) {
    $caso = json_decode(base64_decode($argv[1]), true);
    $_GET = array('id_evento' => 7, 'id_animal' => 3, 'tipo' => 1);
    $_POST = array('evento' => 'Feira', 'data' => '15/10/2026', 'cidade' => 'Cidade', 'local' => 'Parque', 'comprador' => 'Cliente', 'valor' => '100', 'tipo_venda' => 'Exposição', 'forma' => 'Dinheiro', 'parcelas' => 1, 'observacoes' => '');
    $_GET = array_merge($_GET, $caso['get'] ?? array());
    $_POST = array_merge($_POST, $caso['post'] ?? array());
    $_SESSION = array(); $gravacoes = array(); $financeiroTeste = array();
    set_error_handler(function ($n, $m) { throw new RuntimeException($m); });
    function DBEscape($valor) { return addslashes($valor); }
    function DBRead($tabela, $params = '', $campos = '*') {
        global $caso;
        if (($caso['modo'] ?? '') === 'exposicao_ausente' && $tabela === 'julgamento') return false;
        if ($tabela === 'julgamento') {
            if (strpos($params, 'WHERE nome') !== false) return ($caso['modo'] ?? '') === 'duplicado' ? array(array('id' => 7)) : false;
            return array(array('id' => 7, 'data' => '2026-10-15'));
        }
        if ($tabela === 'animais') return array(array('id' => 3, 'nome' => 'Animal', 'pai' => 1, 'mae' => 2, 'terceiro_pai' => 1, 'terceiro_mae' => 1, 'data_de_nascimento' => '2026-01-01'));
        if ($tabela === 'animais_evento') return ($caso['modo'] ?? '') === 'novo_animal' ? false : array(array('id' => 1));
        if ($tabela === 'julgamento_controle') return ($caso['modo'] ?? '') === 'julgado' ? array(array('id' => 1, 'id_animal' => 3)) : false;
        if ($tabela === 'mercado') return ($caso['modo'] ?? '') === 'comprador_ausente' ? false : array(array('id' => 5));
        if ($tabela === 'vendas') return ($caso['modo'] ?? '') === 'vendido' ? array(array('id' => 9)) : false;
        return false;
    }
    function DBCreate($tabela, $dados, $id = false) {
        global $gravacoes, $caso; $gravacoes[] = 'create:' . $tabela;
        if ($tabela === 'controle_financeiro') $GLOBALS['financeiroTeste'][] = $dados;
        return ($caso['modo'] ?? '') === 'falha' ? false : ($id ? 7 : true);
    }
    function DBUpdate($tabela, $dados, $where) { global $gravacoes; $gravacoes[] = 'update:' . $tabela; return true; }
    function DBDelete($tabela, $where) { global $gravacoes; $gravacoes[] = 'delete:' . $tabela; return true; }
    // Contrato das ações; a implementação transacional é testada no MySQL em
    // exposicao_participantes_test.php.
    function alterarParticipanteExposicao($evento, $animal, $acao) {
        global $caso, $gravacoes;
        if (!$evento || !$animal || ($caso['modo'] ?? '') === 'exposicao_ausente') throw new DomainException('Exposição ou animal inválido.');
        $julgado = ($caso['modo'] ?? '') === 'julgado';
        if ($acao === 'adicionar' && ($caso['modo'] ?? '') !== 'novo_animal') return false;
        if (in_array($acao, array('julgamento','adicionar_julgamento'), true) && $julgado) return false;
        if ($acao === 'remover_julgamento' && !$julgado) return false;
        if ($acao === 'adicionar') $gravacoes[] = 'create:animais_evento';
        elseif (in_array($acao, array('julgamento','adicionar_julgamento'), true)) $gravacoes[] = 'create:julgamento_controle';
        elseif ($acao === 'remover') $gravacoes[] = 'delete:animais_evento';
        else $gravacoes[] = 'delete:julgamento_controle';
        return true;
    }
    register_shutdown_function(function () {
        global $gravacoes;
        echo json_encode(array('alerta' => $_SESSION['alerta_exposicao'] ?? null, 'campos' => $_SESSION['campos_exposicao'] ?? null, 'financeiro' => $GLOBALS['financeiroTeste'], 'gravacoes' => $gravacoes));
    });
    $codigo = file_get_contents(__DIR__ . '/../exposicao/' . $caso['arquivo']);
    $codigo = str_replace("require_once __DIR__ . '/../_config.php';", '', $codigo);
    $codigo = str_replace("require_once __DIR__ . '/participantes.php';", '', $codigo);
    $codigo = str_replace('__DIR__', var_export(realpath(__DIR__ . '/../exposicao'), true), $codigo);
    eval('?>' . $codigo);
    exit;
}
$casos = array(
    array('arquivo'=>'_cadastrar.php', 'tipo'=>'success', 'grava'=>'create:julgamento'),
    array('arquivo'=>'_cadastrar.php', 'modo'=>'duplicado', 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_cadastrar.php', 'post'=>array('evento'=>''), 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_cadastrar.php', 'post'=>array('data'=>'31/02/2026'), 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_cadastrar.php', 'modo'=>'falha', 'tipo'=>'danger'),
    array('arquivo'=>'_alterar.php', 'tipo'=>'success', 'grava'=>'update:julgamento'),
    array('arquivo'=>'_alterar.php', 'modo'=>'duplicado', 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_cadastrar_animal.php', 'modo'=>'novo_animal', 'tipo'=>'success', 'grava'=>'create:animais_evento'),
    array('arquivo'=>'_cadastrar_animal.php', 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_animal_julgamento.php', 'tipo'=>'success', 'grava'=>'create:julgamento_controle'),
    array('arquivo'=>'_animal_julgamento.php', 'modo'=>'julgado', 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_cadastrar_animal_completo.php', 'modo'=>'novo_animal', 'tipo'=>'success', 'grava'=>'create:julgamento_controle'),
    array('arquivo'=>'_cadastrar_animal_completo.php', 'modo'=>'julgado', 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_excluir_animal.php', 'tipo'=>'success', 'grava'=>'delete:animais_evento'),
    array('arquivo'=>'_excluir_julgamento.php', 'modo'=>'julgado', 'tipo'=>'success', 'grava'=>'delete:julgamento_controle'),
    array('arquivo'=>'_excluir_julgamento.php', 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_animal_leilao.php', 'tipo'=>'success', 'grava'=>'update:animais_evento'),
    array('arquivo'=>'_excluir_leilao.php', 'tipo'=>'success', 'grava'=>'update:animais_evento'),
    array('arquivo'=>'_animal_leilao.php', 'get'=>array('id_animal'=>0), 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_excluir_animal.php', 'modo'=>'exposicao_ausente', 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_vender_animal.php', 'tipo'=>'success', 'grava'=>'create:controle_financeiro'),
    array('arquivo'=>'_vender_animal.php', 'post'=>array('valor'=>'100.00', 'parcelas'=>3, 'forma'=>'Pix', 'data'=>'31/01/2026'), 'tipo'=>'success', 'parcelamento'=>true),
    array('arquivo'=>'_vender_animal.php', 'modo'=>'comprador_ausente', 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_vender_animal.php', 'post'=>array('valor'=>''), 'tipo'=>'warning', 'semGravacao'=>true),
    array('arquivo'=>'_excluir_venda.php', 'modo'=>'vendido', 'tipo'=>'success', 'grava'=>'delete:vendas'),
    array('arquivo'=>'_excluir_venda.php', 'tipo'=>'warning', 'semGravacao'=>true)
);
foreach ($casos as $caso) {
    $comando = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . escapeshellarg(base64_encode(json_encode($caso)));
    $resultado = json_decode(shell_exec($comando), true);
    if (!empty($caso['parcelamento'])) {
        $f = $resultado['financeiro'];
        if (array_column($f, 'valor') !== array('33.34','33.33','33.33') || array_column($f, 'data') !== array('2026-01-31','2026-02-28','2026-03-31') || array_column($f, 'titulo') !== array('Venda animal - Animal 1/3','Venda animal - Animal 2/3','Venda animal - Animal 3/3') || $f[0]['forma_de_pagamento'] !== 'Pix') throw new RuntimeException('Parcelamento da exposição incorreto.');
    }
    if (($resultado['alerta']['tipo'] ?? null) !== $caso['tipo']) throw new RuntimeException('Alerta incorreto: '.json_encode($caso).' '.json_encode($resultado));
    if (!empty($caso['semGravacao']) && $resultado['gravacoes']) throw new RuntimeException('Gravação indevida.');
    if (isset($caso['grava']) && !in_array($caso['grava'], $resultado['gravacoes'], true)) throw new RuntimeException('Gravação esperada ausente.');
    if ($caso['arquivo']==='_cadastrar.php' && $caso['tipo']==='warning' && !is_array($resultado['campos'])) throw new RuntimeException('Campos não preservados.');
}
$_SESSION = array('alerta_exposicao'=>array('tipo'=>'warning','mensagem'=>'</script><script>teste</script>'),'campos_exposicao'=>array('evento'=>'Tentativa'));
$pg='exposicao'; ob_start(); require __DIR__.'/../includes/alertas_exposicao.php'; $html=ob_get_clean();
if (isset($_SESSION['alerta_exposicao']) || isset($_SESSION['campos_exposicao']) || strpos($html,'</script><script>teste')!==false || $camposExposicao['evento']!=='Tentativa') throw new RuntimeException('Flash inseguro ou não consumido.');
echo count($casos)." cenários de exposição: sucesso, duplicidade, validação, falha, flash único e texto seguro: OK\n";
