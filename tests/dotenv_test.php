<?php
require __DIR__ . '/../mysqli/dotenv.php';
$arquivo=tempnam(sys_get_temp_dir(),'sob-env-');
$hostAnterior=getenv('DB_HOST');
try {
    file_put_contents($arquivo, 'APP_PORT=8000'."\n".'DB_PASSWORD=abc#123=$literal'."\n".'DB_DATABASE=central'."\n".'DB_PREFIX=fazenda_'."\n\n".'DB_HOST=localhost'."\n\n".'DB_USER=usuario_teste'."\n");
    $config=carregarEnvBancoSob($arquivo);
    if($config['DB_PASSWORD']!=='abc#123=$literal'||$config['DB_USER']!=='usuario_teste'||isset($config['APP_PORT']))throw new RuntimeException('Leitura incorreta');
    $GLOBALS['sobConfiguracaoBanco']=$config;
    putenv('DB_HOST');
    if(valorEnvBancoSob('DB_HOST')!=='localhost'||valorEnvBancoSob('DB_PREFIX')!=='fazenda_')throw new RuntimeException('Arquivo ignorado');
    putenv('DB_HOST=host-container');
    if(valorEnvBancoSob('DB_HOST')!=='host-container')throw new RuntimeException('Prioridade incorreta');
    echo "Formato de produção, senha literal, prefixo e prioridade do ambiente: OK\n";
} finally {
    unlink($arquivo);
    putenv($hostAnterior===false?'DB_HOST':'DB_HOST='.$hostAnterior);
}
