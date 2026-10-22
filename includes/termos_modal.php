<?php
if (empty($_SESSION['termos_csrf'])) $_SESSION['termos_csrf'] = bin2hex(random_bytes(32));
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Termos de uso — Sistema Ovinos Brasil</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#fff;font:16px/1.5 Arial,sans-serif;color:#263238}.marca{padding:20px;background:#367fa9;color:white;font-size:22px}.fundo{position:fixed;inset:0;background:#fff;display:flex;align-items:center;justify-content:center;padding:16px}.termos{background:white;border-radius:8px;width:850px;max-width:100%;max-height:100%;display:flex;flex-direction:column;box-shadow:0 12px 40px #0005}.cabecalho,.rodape{padding:18px 24px}h1{font-size:22px;margin:0}.cabecalho p{margin:8px 0 0}.conteudo{overflow-y:auto;padding:0 24px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;min-height:0}.conteudo h3{font-size:17px;margin-top:24px}.rodape{flex-shrink:0}.rodape label{display:block;margin-bottom:16px;cursor:pointer}input{width:18px;height:18px;vertical-align:middle;margin-right:8px}button{font-size:16px;padding:10px 18px;border:0;border-radius:4px;background:#008d4c;color:white;cursor:pointer}button:disabled{background:#ddd;color:#666;cursor:not-allowed}.sair{margin-left:16px;color:#365d77}a:focus,button:focus,input:focus,.conteudo:focus{outline:3px solid #367fa9;outline-offset:2px}@media(max-width:480px){.cabecalho,.rodape{padding:12px 16px}.conteudo{padding:0 16px}h1{font-size:19px}}
</style>
</head>
<body>
<div class="marca">Sistema Ovinos Brasil</div>
<div class="fundo">
<section class="termos" role="dialog" aria-modal="true" aria-labelledby="titulo-termos" aria-describedby="instrucao-termos">
<header class="cabecalho"><h1 id="titulo-termos">TERMOS DE USO – SISTEMA OVINOS BRASIL</h1><p id="instrucao-termos">Leia os termos e confirme seu aceite para utilizar o sistema.</p></header>
<div class="conteudo" tabindex="0" aria-label="Texto completo dos termos de uso"><?php require __DIR__ . '/termos_conteudo.html'; ?></div>
<form class="rodape" method="post" action="<?=htmlspecialchars(caminhoSessaoSob() . '_aceitar_termos.php', ENT_QUOTES, 'UTF-8')?>">
<input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['termos_csrf'], ENT_QUOTES, 'UTF-8')?>">
<label><input type="checkbox" name="termos_lidos" id="termos-lidos" value="1" required>Li e aceito os Termos de Uso do Sistema Ovinos Brasil.</label>
<button type="submit" id="aceitar-termos" disabled>Aceitar e continuar</button>
<a class="sair" href="<?=htmlspecialchars(caminhoSessaoSob() . '_logout.php', ENT_QUOTES, 'UTF-8')?>">Sair</a>
<noscript><p>Ative o JavaScript para confirmar a leitura e aceitar os termos.</p></noscript>
</form>
</section>
</div>
<script>
(function () {
    var check = document.getElementById('termos-lidos');
    var botao = document.getElementById('aceitar-termos');
    function atualizar() { botao.disabled = !check.checked; }
    check.addEventListener('change', atualizar);
    window.addEventListener('pageshow', atualizar);
    atualizar();
}());
</script>
</body>
</html>
