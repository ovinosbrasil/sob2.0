<?php
/** Estado persistente; horários em epoch para não depender do fuso do MySQL. */
function estadoLimiteLogin(array $estado, $agora)
{
    if ((int)$estado['bloqueado_ate'] > $agora) return $estado;
    if ((int)$estado['bloqueado_ate'] > 0 || $agora - (int)$estado['inicio'] >= 900) {
        return array('inicio'=>$agora, 'falhas'=>0, 'bloqueado_ate'=>0);
    }
    return $estado;
}

function falhaLimiteLogin(array $estado, $agora, $limite)
{
    $estado = estadoLimiteLogin($estado, $agora);
    $estado['falhas']++;
    if ($estado['falhas'] >= $limite) $estado['bloqueado_ate'] = $agora + 900;
    return $estado;
}

function autenticarComLimite($link, $login, $senha, $ip, callable $autenticar, &$segundosBloqueio = 0)
{
    $segundosBloqueio = 0;
    // Limpeza limitada por requisição; executar também diariamente se necessário.
    mysqli_query($link, 'DELETE FROM auth_login_limits WHERE atualizado_em < UNIX_TIMESTAMP() - 86400 LIMIT 100');
    // O ID canônico cobre variações de maiúsculas/acentos aceitas pela collation.
    $stmt = mysqli_prepare($link, 'SELECT id FROM user WHERE login = ? ORDER BY id LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $login);
    mysqli_stmt_execute($stmt);
    $conta = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    $chaves = array(
        hash('sha256', 'conta:' . ($conta ? 'id:' . $conta['id'] : 'login:' . strtolower(trim($login)))) => 5,
        hash('sha256', 'ip:' . $ip) => 50,
    );
    // Ordem comum impede deadlocks entre acessos que compartilham conta ou IP.
    ksort($chaves);
    mysqli_begin_transaction($link);
    try {
        $estados = array();
        foreach ($chaves as $chave => $limite) {
            $stmt = mysqli_prepare($link, 'INSERT IGNORE INTO auth_login_limits (chave, inicio, falhas, bloqueado_ate, atualizado_em) VALUES (?, UNIX_TIMESTAMP(), 0, 0, UNIX_TIMESTAMP())');
            mysqli_stmt_bind_param($stmt, 's', $chave);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $stmt = mysqli_prepare($link, 'SELECT inicio, falhas, bloqueado_ate FROM auth_login_limits WHERE chave = ? FOR UPDATE');
            mysqli_stmt_bind_param($stmt, 's', $chave);
            mysqli_stmt_execute($stmt);
            $estados[$chave] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
        }
        // Obter horário depois de aguardar os locks.
        $agora = (int)mysqli_fetch_row(mysqli_query($link, 'SELECT UNIX_TIMESTAMP()'))[0];
        foreach ($estados as $estado) {
            $segundosBloqueio = max($segundosBloqueio, (int)$estado['bloqueado_ate'] - $agora);
        }
        if ($segundosBloqueio > 0) {
            mysqli_commit($link);
            error_log('Autenticacao: tentativa bloqueada temporariamente.');
            return false;
        }
        $usuario = $autenticar($link, $login, $senha);
        foreach ($chaves as $chave => $limite) {
            $estado = estadoLimiteLogin($estados[$chave], $agora);
            if (!$usuario) {
                $estado = falhaLimiteLogin($estado, $agora, $limite);
                $segundosBloqueio = max($segundosBloqueio, (int)$estado['bloqueado_ate'] - $agora);
            } elseif ($limite === 5) {
                // Sucesso limpa a conta; preserva falhas de outras contas no mesmo IP.
                $estado = array('inicio'=>$agora, 'falhas'=>0, 'bloqueado_ate'=>0);
            }
            $stmt = mysqli_prepare($link, 'UPDATE auth_login_limits SET inicio = ?, falhas = ?, bloqueado_ate = ?, atualizado_em = ? WHERE chave = ?');
            mysqli_stmt_bind_param($stmt, 'iiiis', $estado['inicio'], $estado['falhas'], $estado['bloqueado_ate'], $agora, $chave);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        mysqli_commit($link);
        error_log($usuario ? 'Autenticacao: login concluido.' : 'Autenticacao: credenciais recusadas.');
        return $usuario;
    } catch (Throwable $erro) {
        mysqli_rollback($link);
        throw $erro;
    }
}
