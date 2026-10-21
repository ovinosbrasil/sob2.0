<?php
/** Compatibilidade temporária com senhas antigas; nunca trate hashes como texto. */
function verificarSenhaLogin($digitada, $armazenada, $permitirLegada)
{
    if (!is_string($digitada) || !is_string($armazenada) || $digitada === '' || $armazenada === '') {
        return false;
    }
    $info = password_get_info($armazenada);
    if ($info['algoName'] !== 'unknown') {
        return password_verify($digitada, $armazenada);
    }
    // Hash desconhecido ou corrompido não pode virar uma senha literal.
    if (isset($armazenada[0]) && $armazenada[0] === '$') {
        return false;
    }
    return $permitirLegada && hash_equals($armazenada, $digitada);
}

function autenticarUsuario($link, $login, $senha, $permitirLegada)
{
    $stmt = mysqli_prepare($link, 'SELECT id, login, senha, cookie, log_fim FROM user WHERE login = ? LIMIT 2');
    mysqli_stmt_bind_param($stmt, 's', $login);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);
    $unico = mysqli_num_rows($resultado) === 1;
    mysqli_stmt_close($stmt);
    if (!$unico || !verificarSenhaLogin($senha, $usuario['senha'], $permitirLegada)) {
        return false;
    }

    $anterior = $usuario['senha'];
    $hash = $anterior;
    if (password_needs_rehash($anterior, PASSWORD_DEFAULT)) {
        $estrutura = mysqli_query($link, "SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'senha'");
        $coluna = mysqli_fetch_assoc($estrutura);
        if (!$coluna || (int)$coluna['CHARACTER_MAXIMUM_LENGTH'] < 255) {
            throw new RuntimeException('Migração da coluna de senha pendente.');
        }
        // PASSWORD_DEFAULT usa bcrypt nos runtimes atuais: impedir truncamento.
        if (strlen($senha) > 72 || strpos($senha, "\0") !== false) {
            return false;
        }
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        if (!is_string($hash) || $hash === '') {
            throw new RuntimeException('Falha ao proteger credencial.');
        }
    }
    $acesso = (new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');
    // Comparação binária evita sobrescrever uma senha alterada concorrentemente.
    $stmt = mysqli_prepare($link, 'UPDATE user SET senha = ?, acesso = ? WHERE id = ? AND BINARY senha = BINARY ?');
    mysqli_stmt_bind_param($stmt, 'ssis', $hash, $acesso, $usuario['id'], $anterior);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    // Confirmar persistência: coluna curta/SQL não estrito não pode liberar o login.
    $stmt = mysqli_prepare($link, 'SELECT senha FROM user WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $usuario['id']);
    mysqli_stmt_execute($stmt);
    $atual = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$atual || !hash_equals($hash, $atual['senha'])) {
        throw new RuntimeException('Credencial não foi persistida integralmente.');
    }
    unset($usuario['senha']);
    return $usuario;
}
