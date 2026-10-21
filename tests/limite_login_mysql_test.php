<?php
// Executar no container app: usa apenas tabelas TEMPORARY nesta conexão.
require __DIR__ . '/../mysqli/environment.php';
require __DIR__ . '/../includes/limite_login.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$db->set_charset(DB_CHARSET);
function verificarLimiteMysql($ok, $mensagem) { if (!$ok) throw new RuntimeException($mensagem); }
try {
    $db->query('CREATE TEMPORARY TABLE user (id INT PRIMARY KEY, login VARCHAR(100) COLLATE utf8mb4_general_ci) ENGINE=InnoDB');
    $db->query("INSERT INTO user VALUES (1, 'ContaTeste'), (2, 'OutraConta')");
    $schema=file_get_contents(__DIR__.'/../docker/mysql/migrations/030_limite_tentativas_login.sql');
    $inicio=strpos($schema,'CREATE TABLE');
    $fim=strpos($schema,';', $inicio);
    $schema=substr($schema,$inicio,$fim-$inicio);
    $schema=str_replace('CREATE TABLE IF NOT EXISTS','CREATE TEMPORARY TABLE',$schema);
    $db->query($schema);
    $chamadas=0;
    $callback=function($link,$login,$senha) use (&$chamadas) { $chamadas++; return $senha==='correta' ? array('id'=>1) : false; };
    $segundos = 0;
    for($i=0;$i<5;$i++) {
        verificarLimiteMysql(!autenticarComLimite($db,'ContaTeste','errada','ip-'.$i,$callback,$segundos),'Falha aceita');
        verificarLimiteMysql($i === 4 ? $segundos === 900 : $segundos === 0,'Prazo incorreto na falha que bloqueia');
    }
    verificarLimiteMysql(!autenticarComLimite($db,'CONTATESTE','correta','novo-ip',$callback,$segundos),'Alias contornou bloqueio');
    verificarLimiteMysql($segundos > 0 && $segundos <= 900,'Prazo de bloqueio ausente');
    verificarLimiteMysql($chamadas===5,'Executou autenticação durante bloqueio');
    $db->query('UPDATE auth_login_limits SET bloqueado_ate=UNIX_TIMESTAMP()-1');
    verificarLimiteMysql((bool)autenticarComLimite($db,'ContaTeste','correta','novo-ip',$callback,$segundos),'Expiração não liberou');
    verificarLimiteMysql($segundos === 0,'Prazo mantido após expiração');
    $chave=hash('sha256','conta:id:1');
    $r=$db->query("SELECT falhas FROM auth_login_limits WHERE chave='$chave'")->fetch_assoc();
    verificarLimiteMysql((int)$r['falhas']===0,'Sucesso não limpou conta');
    $db->query('DELETE FROM auth_login_limits');
    for($i=0;$i<50;$i++) autenticarComLimite($db,'inexistente-'.$i,'errada','ip-compartilhado',$callback);
    verificarLimiteMysql(!autenticarComLimite($db,'OutraConta','correta','ip-compartilhado',$callback),'IP não bloqueou contas distintas');
    verificarLimiteMysql((bool)autenticarComLimite($db,'OutraConta','correta','ip-livre',$callback),'Bloqueio de IP afetou outro IP');
    $chaveIp=hash('sha256','ip:ip-livre');
    $db->query("UPDATE auth_login_limits SET falhas=10 WHERE chave='$chaveIp'");
    autenticarComLimite($db,'OutraConta','correta','ip-livre',$callback);
    $r=$db->query("SELECT falhas FROM auth_login_limits WHERE chave='$chaveIp'")->fetch_assoc();
    verificarLimiteMysql((int)$r['falhas']===10,'Sucesso apagou falhas do IP');
    echo "MySQL: bloqueio persistente, alias, conta/IP, sucesso e expiração: OK\n";
} finally { $db->close(); }
