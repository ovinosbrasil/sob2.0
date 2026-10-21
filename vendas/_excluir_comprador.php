<?php
require_once __DIR__ . '/../_config.php';
require_once __DIR__ . '/../includes/csrf.php';
exigirPostCsrfSob(true);
require_once __DIR__ . '/_alertas_compradores.php';
$respostaJson = true;
$id = filter_var($_POST['id_comprador'] ?? 0, FILTER_VALIDATE_INT);
if (!$id || $id < 1) finalizarComprador('warning', 'Comprador inválido.', 0, 400);
if (!DBRead('mercado', "WHERE id = '$id'")) finalizarComprador('warning', 'Comprador não encontrado.', 0, 404);
foreach (array('vendas'=>'comprador','venda_semen'=>'comprador','venda_embriao'=>'comprador','controle_financeiro'=>'id_comprador') as $tabela=>$coluna) {
    if (DBRead($tabela, "WHERE $coluna = '$id' LIMIT 1")) finalizarComprador('warning', 'Este comprador não pode ser excluído porque possui vendas ou lançamentos financeiros relacionados.', 0, 409);
}
if (!DBDelete('mercado', "id = '$id'")) finalizarComprador('danger', 'Não foi possível excluir o comprador. Tente novamente.', 0, 500);
// O aviso será exibido depois de recarregar a lista atual.
$_SESSION['alerta_compradores'] = array('tipo'=>'success','titulo'=>'Sucesso!','mensagem'=>'Comprador excluído com sucesso.');
finalizarComprador('success', 'Comprador excluído com sucesso.');
