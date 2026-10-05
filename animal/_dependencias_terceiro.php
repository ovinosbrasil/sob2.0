<?php
// Uma conexão por operação: a exclusão mantém as tabelas bloqueadas durante a conferência.
function consultarExclusaoTerceiro($link, $sql) {
    $resultado = mysqli_query($link, $sql);
    if ($resultado === false) { throw new RuntimeException('Não foi possível verificar os vínculos.'); }
    return $resultado;
}
function mapaExclusaoTerceiro() {
    return array(
        'animais' => array('Crias', array(array('pai', 'terceiro_pai'), array('mae', 'terceiro_mae'))),
        'monta' => array('Lotes de monta', array(array('id_animal', 'terceiro'))),
        'monta_controle' => array('Fêmeas em monta', array(array('id_animal', 'terceiro'))),
        'inseminacao' => array('Lotes de inseminação', array(array('id_macho', 'terceiro'))),
        'inseminacao_controle' => array('Fêmeas em inseminação', array(array('id_femea', 'terceiro'))),
        'transplante' => array('Transferência de embriões', array(array('id_pai', 'terceiro_pai'), array('id_mae', 'terceiro_mae'), array('id_pai_2', 'terceiro_pai_2'))),
        'semen' => array('Registros de sêmen', array(array('id_animal', 'terceiro'))),
        'embriao' => array('Registros de embriões', array(array('pai', 'terceiro'), array('mae', 'terceiro_mae'))),
    );
}
function esquemaExclusaoTerceiro($link) {
    $resultado = consultarExclusaoTerceiro($link, 'SHOW TABLES');
    $esquema = array();
    $mapa = mapaExclusaoTerceiro();
    while ($linha = mysqli_fetch_row($resultado)) {
        $tabela = $linha[0];
        if ($tabela !== 'terceiros' && !isset($mapa[$tabela])) { continue; }
        $colunas = consultarExclusaoTerceiro($link, "SHOW COLUMNS FROM `$tabela`");
        $esquema[$tabela] = array();
        while ($coluna = mysqli_fetch_assoc($colunas)) { $esquema[$tabela][] = $coluna['Field']; }
    }
    if (!isset($esquema['terceiros'], $esquema['animais'])) { throw new RuntimeException('Estrutura do cadastro indisponível.'); }
    return $esquema;
}
function dependenciasExclusaoTerceiro($link, $esquema, $id, $nome) {
    $vinculos = array();
    $nome = mysqli_real_escape_string($link, $nome);
    foreach (mapaExclusaoTerceiro() as $tabela => $config) {
        if (!isset($esquema[$tabela])) { continue; }
        $condicoes = array();
        foreach ($config[1] as $par) {
            if (in_array($par[0], $esquema[$tabela], true) && in_array($par[1], $esquema[$tabela], true)) {
                $condicoes[] = "(`{$par[0]}` = " . (int)$id . " AND `{$par[1]}` = 1)";
            }
        }
        // Lotes legados também podem conservar apenas o nome do macho.
        if ($tabela === 'monta' && in_array('macho', $esquema[$tabela], true)) {
            $semId = in_array('id_animal', $esquema[$tabela], true) ? ' AND COALESCE(id_animal, 0) = 0' : '';
            $condicoes[] = "(`macho` = '$nome'$semId)";
        }
        if (!$condicoes) { throw new RuntimeException('Estrutura de vínculos incompatível.'); }
        $resultado = consultarExclusaoTerceiro($link, "SELECT COUNT(*) AS total FROM `$tabela` WHERE " . implode(' OR ', $condicoes));
        $total = (int)mysqli_fetch_assoc($resultado)['total'];
        if ($total) { $vinculos[] = array('tipo' => $config[0], 'quantidade' => $total); }
    }
    return $vinculos;
}
