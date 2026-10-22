<?php
require_once __DIR__ . '/../includes/termos.php';
function verificarTermos($condicao, $mensagem) {
    if (!$condicao) throw new RuntimeException($mensagem);
}
$token = str_repeat('a', 64);
verificarTermos(aceiteTermosValidoSob(array('termos_lidos' => '1', 'csrf_token' => $token), $token), 'Aceite válido recusado');
foreach (array(
    array('csrf_token' => $token),
    array('termos_lidos' => '0', 'csrf_token' => $token),
    array('termos_lidos' => array('1'), 'csrf_token' => $token),
    array('termos_lidos' => '1'),
    array('termos_lidos' => '1', 'csrf_token' => 'incorreto'),
    array('termos_lidos' => '1', 'csrf_token' => array($token)),
) as $dados) verificarTermos(!aceiteTermosValidoSob($dados, $token), 'Aceite inválido permitido');
verificarTermos(!aceiteTermosValidoSob(array('termos_lidos' => '1', 'csrf_token' => ''), ''), 'Token vazio permitido');
$_SESSION = array();
function caminhoSessaoSob() { return '/'; }
ob_start();
require __DIR__ . '/../includes/termos_modal.php';
$html = ob_get_clean();
verificarTermos(substr_count($html, '<h3>') === 16, 'Termos incompletos');
verificarTermos(strpos($html, 'id="aceitar-termos" disabled') !== false, 'Botão inicialmente habilitado');
verificarTermos(strpos($html, 'botao.disabled = !check.checked') !== false, 'Checkbox não controla botão');
verificarTermos(strlen($_SESSION['termos_csrf']) === 64, 'Token não gerado');
echo "OK: validação do aceite, CSRF e modal completo\n";
