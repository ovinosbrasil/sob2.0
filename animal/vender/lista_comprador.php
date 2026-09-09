<?
include "../../_config.php";
$nome = $_GET['nome'];

$comprador = DBRead('mercado',"WHERE nome LIKE '%$nome%' ORDER BY nome asc LIMIT 10");
foreach ($comprador as $comprador_) {
  ?>
  <a href="javascript:linkar_comprador('<?=$comprador_['nome']?>');" style="color:#2d2c2c;">
     <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%;">
       <span style="font-weight:bold;"> <?=$comprador_['nome']?></span><br/>Cidade: <?=$comprador_['cidade']?> - Cidade: <?=$comprador_['cidade']?>
     </div>
</a>
<? } ?>
  <a href="javascript:fechar_lista_comprador();" style="color:#2d2c2c;">
    <div id="nome" style="cursor:pointer; padding:0.8%; padding-left:1%;"> <span style="color:red;"> Fechar Pesquisa </span></div>
  </a>
