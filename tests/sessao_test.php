<?php
require __DIR__ . '/../includes/sessao.php';
set_error_handler(function ($nivel, $mensagem) { throw new RuntimeException($mensagem); });
$diretorioSessao = sys_get_temp_dir() . '/sob-sessao-' . bin2hex(random_bytes(8));
mkdir($diretorioSessao, 0700);
session_save_path($diretorioSessao);
register_shutdown_function(function () use ($diretorioSessao) {
    foreach (glob($diretorioSessao . '/*') as $arquivo) unlink($arquivo);
    rmdir($diretorioSessao);
});
function exigirTesteSessao($ok, $mensagem) { if (!$ok) throw new RuntimeException($mensagem); }
$agora = 100000;
$s = array('login'=>'teste', 'banco'=>'fazenda', 'autenticado_em'=>$agora-2000, 'ultima_atividade'=>$agora-1799);
exigirTesteSessao(sessaoSobValida($s,$agora),'Sessão válida recusada');
$s['ultima_atividade']=$agora-1800;
exigirTesteSessao(!sessaoSobValida($s,$agora),'Limite de inatividade ignorado');
$s['ultima_atividade']=$agora;
$s['autenticado_em']=$agora-28800;
exigirTesteSessao(!sessaoSobValida($s,$agora),'Limite absoluto ignorado');
$s['autenticado_em']=$agora-28799;
exigirTesteSessao(sessaoSobValida($s,$agora),'Atividade renovou prazo absoluto incorretamente');
unset($s['autenticado_em']);
exigirTesteSessao(!sessaoSobValida($s,$agora),'Sessão antiga aceita sem prazo');
$_SERVER['SCRIPT_FILENAME']=dirname(__DIR__).'/animal/_pesquisar_animal.php';
$_SERVER['SCRIPT_NAME']='/sob2.0/animal/_pesquisar_animal.php';
exigirTesteSessao(caminhoSessaoSob()==='/sob2.0/','Subdiretório incorreto');
$_SERVER['SCRIPT_NAME']='/animal/_pesquisar_animal.php';
exigirTesteSessao(caminhoSessaoSob()==='/','Raiz incorreta');
$_SERVER['HTTPS']='on';
iniciarSessaoSob();
exigirTesteSessao(session_status()===PHP_SESSION_ACTIVE,'Sessão não iniciou');
$cookie=session_get_cookie_params();
exigirTesteSessao($cookie['secure'] && $cookie['httponly'] && $cookie['samesite']==='Lax','Flags de cookie incorretas');
$_SESSION=array('login'=>'teste','banco'=>'fazenda','outro'=>'valor');
encerrarSessaoSob();
exigirTesteSessao($_SESSION===array() && session_status()!==PHP_SESSION_ACTIVE,'Logout não destruiu sessão');
echo "Sessões: limites, subdiretório, cookie HTTPS e logout: OK\n";
