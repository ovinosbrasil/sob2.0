<?php
$raiz=sys_get_temp_dir().'/sob-prod-env-'.bin2hex(random_bytes(6));
mkdir($raiz,0700);
function conferirAmbienteProducao($condicao,$mensagem){if(!$condicao)throw new RuntimeException($mensagem);}
try {
    foreach(array('mysqli','mysqliprd') as $pasta){
        mkdir($raiz.'/'.$pasta,0700);
        foreach(array('dotenv.php','environment.php') as $arquivo)copy(__DIR__.'/../'.$pasta.'/'.$arquivo,$raiz.'/'.$pasta.'/'.$arquivo);
    }
    // Mesmo conjunto e disposição das chaves de .envprd, com valores sintéticos.
    $env="APP_PORT=8000\nDB_PASSWORD='senha#com=simbolos'\nDB_DATABASE=central_teste\nDB_PREFIX=cliente_\n\nDB_HOST=localhost\n\nDB_USER=usuario_teste\n";
    file_put_contents($raiz.'/.env',$env);
    foreach(array('mysqli','mysqliprd') as $pasta)foreach(array('login','fazenda') as $modo){
        $codigo='<?php require __DIR__ . "/'.$pasta.'/dotenv.php";';
        if($modo==='fazenda')$codigo.='$databaseName=valorEnvBancoSob("DB_PREFIX")."fazenda_a";';
        $codigo.='require __DIR__ . "/'.$pasta.'/environment.php"; echo json_encode(array("ok"=>DB_HOSTNAME==="localhost" && DB_USERNAME==="usuario_teste" && DB_PASSWORD==="senha#com=simbolos", "banco"=>DB_DATABASE));';
        file_put_contents($raiz.'/teste.php',$codigo);
        $cmd='env';foreach(array('DB_HOST','DB_USER','DB_PASSWORD','DB_DATABASE','DB_PREFIX','DB_CHARSET') as $nome)$cmd.=' -u '.$nome;
        $cmd.=' '.escapeshellarg(PHP_BINARY).' -d disable_functions=putenv '.escapeshellarg($raiz.'/teste.php');
        $r=json_decode(shell_exec($cmd),true);
        conferirAmbienteProducao($r&&$r['ok']&&$r['banco']===($modo==='login'?'central_teste':'cliente_fazenda_a'),'Configuração de produção incorreta: '.$pasta.'/'.$modo);
    }
    // Verifica o arquivo de referência apenas em memória, sem conectar ou imprimir valores.
    if(is_file(__DIR__.'/../.envprd')){
        require_once __DIR__.'/../mysqli/dotenv.php';
        $config=carregarEnvBancoSob(__DIR__.'/../.envprd');
        foreach(array('DB_HOST','DB_USER','DB_PASSWORD','DB_DATABASE','DB_PREFIX') as $chave)conferirAmbienteProducao(isset($config[$chave])&&$config[$chave]!=='','Campo não lido no arquivo de referência: '.$chave);
    }
    echo "Produção sem Docker/putenv: login central, banco da fazenda e formato .envprd: OK\n";
} finally {
    foreach(array('mysqli','mysqliprd') as $pasta){foreach(glob($raiz.'/'.$pasta.'/*') as $f)unlink($f);rmdir($raiz.'/'.$pasta);}
    foreach(glob($raiz.'/*') as $f)unlink($f);
    if(is_file($raiz.'/.env'))unlink($raiz.'/.env');
    rmdir($raiz);
}
