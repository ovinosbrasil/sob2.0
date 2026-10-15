<?php
$respostaJson = ($_POST['formato'] ?? '') === 'json';
$idRetornoComprador = 0;
if (function_exists('mysqli_report')) mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
function finalizarComprador($tipo, $mensagem, $id = 0, $status = 200, array $extra = array())
{
    global $respostaJson;
    if ($respostaJson) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($status);
        echo json_encode(array_merge(array('sucesso'=>$tipo === 'success', 'tipo'=>$tipo, 'mensagem'=>$mensagem), $extra), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    } else {
        $_SESSION['alerta_compradores'] = array('tipo'=>$tipo, 'titulo'=>$tipo === 'success' ? 'Sucesso!' : ($tipo === 'danger' ? 'Erro!' : 'Atenção!'), 'mensagem'=>$mensagem);
        if ($tipo === 'success') unset($_SESSION['campos_comprador']);
        $destino = $id > 0 ? 'pg=comprador&id_comprador=' . (int)$id : 'pg=compradores';
        header('Location: ../geral.php?' . $destino, true, 303);
    }
    exit;
}
function dadosFormularioComprador($id = 0)
{
    global $respostaJson;
    $campos = array();
    foreach (array('nome','celular','email','cpf','cod','cidade','estado') as $nome) $campos[$nome] = is_string($_POST[$nome] ?? null) ? trim($_POST[$nome]) : '';
    if (!$respostaJson) $_SESSION['campos_comprador'] = array('id'=>(int)$id, 'campos'=>$campos);
    if ($campos['nome'] === '') finalizarComprador('warning', 'Informe o nome completo do comprador.', $id, 422);
    if ($campos['email'] !== '' && !filter_var($campos['email'], FILTER_VALIDATE_EMAIL)) finalizarComprador('warning', 'Informe um e-mail válido.', $id, 422);
    return array('nome'=>$campos['nome'], 'celular1'=>$campos['celular'], 'email'=>$campos['email'], 'cpf'=>$campos['cpf'], 'cod_criador'=>$campos['cod'], 'cidade'=>$campos['cidade'], 'estado'=>$campos['estado']);
}
set_exception_handler(function ($erro) {
    global $idRetornoComprador;
    error_log('Falha no fluxo de compradores: ' . $erro->getMessage());
    finalizarComprador('danger', 'Não foi possível concluir a operação. Tente novamente.', $idRetornoComprador, 500);
});
