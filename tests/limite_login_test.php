<?php
require __DIR__ . '/../includes/limite_login.php';
function limiteExigir($ok, $erro) { if (!$ok) throw new RuntimeException($erro); }
$estado = array('inicio'=>1000,'falhas'=>0,'bloqueado_ate'=>0);
for ($i=1;$i<=4;$i++) $estado=falhaLimiteLogin($estado,1000+$i,5);
limiteExigir($estado['bloqueado_ate']===0,'Bloqueou antes da quinta falha');
$estado=falhaLimiteLogin($estado,1005,5);
limiteExigir($estado['bloqueado_ate']===1905,'Bloqueio deve durar 15 minutos');
limiteExigir(estadoLimiteLogin($estado,1904)===$estado,'Bloqueio expirou antes do limite');
limiteExigir(estadoLimiteLogin($estado,1905)['falhas']===0,'Bloqueio não expirou');
$estado=array('inicio'=>1000,'falhas'=>4,'bloqueado_ate'=>0);
limiteExigir(falhaLimiteLogin($estado,1900,5)['falhas']===1,'Janela não reiniciou');
$estado=array('inicio'=>1000,'falhas'=>0,'bloqueado_ate'=>0);
for($i=0;$i<49;$i++) $estado=falhaLimiteLogin($estado,1001,50);
limiteExigir($estado['bloqueado_ate']===0,'IP bloqueado antes de 50 falhas');
limiteExigir(falhaLimiteLogin($estado,1002,50)['bloqueado_ate']===1902,'IP não bloqueado');
echo "Limites de conta/IP, janela e expiração: OK\n";
