<?php
require_once __DIR__ . '/dotenv.php';
$sobDadosConexao = array(
    'DB_HOSTNAME' => valorEnvBancoSob('DB_HOST', 'localhost'),
    'DB_USERNAME' => valorEnvBancoSob('DB_USER'),
    'DB_PASSWORD' => valorEnvBancoSob('DB_PASSWORD'),
    'DB_DATABASE' => isset($databaseName) ? $databaseName : valorEnvBancoSob('DB_DATABASE'),
    'DB_CHARSET' => valorEnvBancoSob('DB_CHARSET', 'latin1'),
);
foreach ($sobDadosConexao as $sobNome => $sobValor) {
    if (!is_string($sobValor) || $sobValor === '') {
        error_log('Configuração de banco incompleta: ' . $sobNome);
        http_response_code(503);
        exit('Serviço temporariamente indisponível.');
    }
}
foreach ($sobDadosConexao as $sobNome => $sobValor) define($sobNome, $sobValor);
unset($sobDadosConexao, $sobNome, $sobValor);
