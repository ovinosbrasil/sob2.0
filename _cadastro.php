<?php
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Utilize o formulário de cadastro.');
}
$dados = array();
foreach (array('nome'=>'nome', 'email'=>'email', 'telefone'=>'celular') as $campo=>$coluna) {
    $valor = $_POST[$campo] ?? null;
    if (!is_string($valor) || trim($valor) === '') {
        http_response_code(422);
        exit('Preencha nome completo, e-mail e WhatsApp. <a href="index.php?cadastro=1">Voltar ao cadastro</a>');
    }
    $dados[$coluna] = trim($valor);
}
if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit('Informe um e-mail válido. <a href="index.php?cadastro=1">Voltar ao cadastro</a>');
}
require __DIR__ . '/mysqli/environment.php';
require __DIR__ . '/mysqli/_conexao.php';
require __DIR__ . '/mysqli/_database.php';
DBCreate('user', $dados);
header('Location: index.php', true, 303);
exit;
