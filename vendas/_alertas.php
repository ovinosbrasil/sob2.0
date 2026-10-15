<?php
function avisarVenda($tipo, $mensagem, $pagina = 'vender', $idComprador = 0)
{
    $_SESSION['alerta_vendas'] = array('tipo'=>$tipo, 'titulo'=>$tipo === 'success' ? 'Sucesso!' : ($tipo === 'danger' ? 'Erro!' : 'Atenção!'), 'mensagem'=>$mensagem);
    $parametros = array('pg' => in_array($pagina, array('vender','relatorio_venda','comprador'), true) ? $pagina : 'vender');
    if ($parametros['pg'] === 'comprador' && (int)$idComprador > 0) $parametros['id_comprador'] = (int)$idComprador;
    header('Location: ../geral.php?' . http_build_query($parametros), true, 303);
    exit;
}
function gravarVenda($funcao, ...$argumentos)
{
    $resultado = $funcao(...$argumentos);
    if ($resultado === false) throw new RuntimeException('Falha ao gravar a venda.');
    return $resultado;
}
set_exception_handler(function ($erro) {
    error_log('Falha no fluxo de vendas: ' . $erro->getMessage());
    avisarVenda('danger', 'Não foi possível concluir a operação. Confira os registros antes de tentar novamente.');
});
