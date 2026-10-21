<?php
require __DIR__ . '/../includes/autenticacao.php';
function conferirSenha($condicao, $mensagem) {
    if (!$condicao) throw new RuntimeException($mensagem);
}
$senha = 'Senha de teste 123!';
$hash = password_hash($senha, PASSWORD_DEFAULT);
conferirSenha(verificarSenhaLogin($senha, $hash, false), 'Hash válido recusado');
conferirSenha(!verificarSenhaLogin('errada', $hash, true), 'Senha errada aceita');
conferirSenha(!verificarSenhaLogin($hash, $hash, true), 'Hash aceito como senha literal');
conferirSenha(verificarSenhaLogin($senha, $senha, true), 'Migração legada recusada');
conferirSenha(!verificarSenhaLogin($senha, $senha, false), 'Legada aceita após desativação');
conferirSenha(!verificarSenhaLogin('123', '0123', true), 'Comparação numérica insegura');
conferirSenha(!verificarSenhaLogin('', '', true), 'Senha vazia aceita');
conferirSenha(!verificarSenhaLogin(array(), $hash, true), 'Entrada inválida aceita');
conferirSenha(!verificarSenhaLogin('$2y$corrompido', '$2y$corrompido', true), 'Hash corrompido aceito');
echo "9 verificações de senha: OK\n";
