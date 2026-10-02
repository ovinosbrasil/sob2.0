<?php
$abaExposicao = $abaExposicao ?? 'animais';
$abasExposicao = array(
    'animais' => array('Animais no evento', 'exposicao'),
    'julgamento' => array('Pista de Julgamento', 'julgamento'),
    'progenie_pai' => array('Progênie de pai', 'exposicao'),
    'progenie_mae' => array('Progênie de mãe', 'exposicao'),
    'vendas' => array('Relatório de vendas', 'relatorio_vendas_exposicao')
);
?>
<ul class="nav nav-tabs abas-exposicao" role="tablist" aria-label="Áreas da exposição">
  <?php foreach ($abasExposicao as $chaveAbaExposicao => $dadosAbaExposicao): ?>
  <li role="presentation" class="<?=$abaExposicao === $chaveAbaExposicao ? 'active' : ''?>"><a role="tab" data-aba-exposicao="<?=$chaveAbaExposicao?>" href="geral.php?pg=<?=$dadosAbaExposicao[1]?>&amp;id_exposicao=<?=(int)$id_evento?>" <?=$abaExposicao === $chaveAbaExposicao ? 'aria-current="page"' : ''?>><?=htmlspecialchars($dadosAbaExposicao[0], ENT_QUOTES, 'UTF-8')?></a></li>
  <?php endforeach; ?>
</ul>
<style>
.abas-exposicao { display:flex; flex-wrap:wrap; overflow:visible; margin-bottom:20px; }
.abas-exposicao > li { flex-shrink:0; }
.abas-exposicao > li > a { color:inherit; }
.abas-exposicao > li.active > a,
.abas-exposicao > li.active > a:hover,
.abas-exposicao > li.active > a:focus { border-top:3px solid #00a65a; color:#008d4c; }
</style>
