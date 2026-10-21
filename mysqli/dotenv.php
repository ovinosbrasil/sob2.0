<?php
/** Lê configuração de banco sem executar/interpolar o arquivo ou usar putenv. */
function carregarEnvBancoSob($arquivo)
{
    if (!is_file($arquivo) || !is_readable($arquivo)) {
        throw new RuntimeException('Arquivo de configuração de banco indisponível.');
    }
    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES);
    if ($linhas === false) throw new RuntimeException('Não foi possível ler a configuração de banco.');
    $configuracao = array();
    foreach ($linhas as $linha) {
        $linha = trim(preg_replace('/^\xEF\xBB\xBF/', '', $linha));
        if ($linha === '' || $linha[0] === '#') continue;
        if (!preg_match('/^(DB_HOST|DB_USER|DB_PASSWORD|DB_DATABASE|DB_PREFIX|DB_CHARSET)\s*=\s*(.*)$/', $linha, $partes)) continue;
        $valor = trim($partes[2]);
        if (strlen($valor) >= 2 && (($valor[0] === '"' && substr($valor, -1) === '"') || ($valor[0] === "'" && substr($valor, -1) === "'"))) {
            $valor = substr($valor, 1, -1);
        }
        $configuracao[$partes[1]] = $valor;
    }
    return $configuracao;
}
function valorEnvBancoSob($nome, $padrao = null)
{
    // Ambiente do container, quando existir, tem prioridade; produção usa .env.
    $ambiente = getenv($nome);
    if ($ambiente !== false) return $ambiente;
    return $GLOBALS['sobConfiguracaoBanco'][$nome] ?? $padrao;
}
try {
    $sobArquivoEnv = dirname(__DIR__) . '/.env';
    $sobConfiguracaoBanco = is_file($sobArquivoEnv) ? carregarEnvBancoSob($sobArquivoEnv) : array();
} catch (Throwable $erro) {
    error_log('Não foi possível carregar o .env do sistema.');
    http_response_code(503);
    exit('Serviço temporariamente indisponível.');
}
unset($sobArquivoEnv);
