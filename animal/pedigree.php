<?
$avo1 = ($pai[0]['pai'] ?? '');
if(($pai[0]['terceiro_pai'] ?? '')){ $avo1 = DBRead('terceiros', "WHERE id = '$avo1'"); }else{ $avo1 = DBRead('animais', "WHERE id = '$avo1'"); }
$avo2= ($pai[0]['mae'] ?? '');
if(($pai[0]['terceiro_mae'] ?? '')){ $avo2 = DBRead('terceiros', "WHERE id = '$avo2'"); }else{ $avo2 = DBRead('animais', "WHERE id = '$avo2'"); }
$avo3 = ($mae[0]['pai'] ?? '');
if(($mae[0]['terceiro_pai'] ?? '')){ $avo3 = DBRead('terceiros', "WHERE id = '$avo3'"); }else{ $avo3 = DBRead('animais', "WHERE id = '$avo3'"); }
$avo4= ($mae[0]['mae'] ?? '');
if(($mae[0]['terceiro_mae'] ?? '')){ $avo4 = DBRead('terceiros', "WHERE id = '$avo4'"); }else{ $avo4 = DBRead('animais', "WHERE id = '$avo4'"); }


$avo5 = ($avo1[0]['pai'] ?? '');
if(($avo1[0]['terceiro_pai'] ?? '')){ $avo5 = DBRead('terceiros', "WHERE id = '$avo5'"); }else{ $avo5 = DBRead('animais', "WHERE id = '$avo5'"); }
$avo6 = ($avo1[0]['mae'] ?? '');
if(($avo1[0]['terceiro_mae'] ?? '')){ $avo6 = DBRead('terceiros', "WHERE id = '$avo6'"); }else{ $avo6 = DBRead('animais', "WHERE id = '$avo6'"); }

$avo7 = ($avo2[0]['pai'] ?? '');
if(($avo2[0]['terceiro_pai'] ?? '')){ $avo7 = DBRead('terceiros', "WHERE id = '$avo7'"); }else{ $avo7 = DBRead('animais', "WHERE id = '$avo7'"); }
$avo8 = ($avo2[0]['mae'] ?? '');
if(($avo2[0]['terceiro_mae'] ?? '')){ $avo8 = DBRead('terceiros', "WHERE id = '$avo8'"); }else{ $avo8 = DBRead('animais', "WHERE id = '$avo8'"); }

$avo9 = ($avo3[0]['pai'] ?? '');
if(($avo3[0]['terceiro_pai'] ?? '')){ $avo9 = DBRead('terceiros', "WHERE id = '$avo9'"); }else{ $avo9 = DBRead('animais', "WHERE id = '$avo9'"); }
$avo10 = ($avo3[0]['mae'] ?? '');
if(($avo3[0]['terceiro_mae'] ?? '')){ $avo10 = DBRead('terceiros', "WHERE id = '$avo10'"); }else{ $avo10 = DBRead('animais', "WHERE id = '$avo10'"); }

$avo11 = ($avo4[0]['pai'] ?? '');
if(($avo4[0]['terceiro_pai'] ?? '')){ $avo11 = DBRead('terceiros', "WHERE id = '$avo11'"); }else{ $avo11 = DBRead('animais', "WHERE id = '$avo11'"); }
$avo12 = ($avo4[0]['mae'] ?? '');
if(($avo4[0]['terceiro_mae'] ?? '')){ $avo12 = DBRead('terceiros', "WHERE id = '$avo12'"); }else{ $avo12 = DBRead('animais', "WHERE id = '$avo12'"); }

$linhagensPedigree = array(
  array(
    array(array('Pai', $pai)),
    array(array('Avô paterno', $avo1), array('Avó paterna', $avo2)),
    array(array('Bisavô paterno', $avo5), array('Bisavó paterna', $avo6), array('Bisavô paterno', $avo7), array('Bisavó paterna', $avo8)),
  ),
  array(
    array(array('Mãe', $mae)),
    array(array('Avô materno', $avo3), array('Avó materna', $avo4)),
    array(array('Bisavô materno', $avo9), array('Bisavó materna', $avo10), array('Bisavô materno', $avo11), array('Bisavó materna', $avo12)),
  ),
);
$pedigreeH = static function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
?>
<style>
  #pagina-animal .dados-gerais-animal, #pagina-animal .pedigree-animal {
    background:#fff; border:1px solid #e3eaf5; border-radius:8px; padding:20px;
  }
  #pagina-animal .pedigree-animal { margin-top:24px; }
  #pagina-animal .dados-gerais-animal h3, #pagina-animal .pedigree-animal h3 {
    font-size:18px; margin:0 0 20px; padding-bottom:12px; border-bottom:1px solid #e3eaf5;
  }
  .pedigree-animal__linhagem { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:12px; }
  .pedigree-animal__linhagem + .pedigree-animal__linhagem { margin-top:36px; }
  .pedigree-animal__item { margin-bottom:4px; }
  .pedigree-animal__parentesco { display:block; margin-bottom:5px; }
  .pedigree-animal__dados { background:#f0f0f0; border-radius:4px; padding:10px; overflow-wrap:anywhere; }
  .pedigree-animal__nome { display:block; min-height:20px; }
  @media (max-width:767px) {
    #pagina-animal .dados-gerais-animal, #pagina-animal .pedigree-animal { padding:15px; }
    .pedigree-animal__linhagem { grid-template-columns:1fr; gap:12px; }
    .pedigree-animal__linhagem + .pedigree-animal__linhagem { margin-top:24px; }
  }
</style>
<section class="pedigree-animal" aria-labelledby="pedigree-animal-titulo">
  <h3 id="pedigree-animal-titulo">Pedigree</h3>
  <?php foreach ($linhagensPedigree as $linhagemPedigree): ?>
  <div class="pedigree-animal__linhagem">
    <?php foreach ($linhagemPedigree as $geracaoPedigree): ?>
    <div>
      <?php foreach ($geracaoPedigree as $itemPedigree):
        $ascendente = $itemPedigree[1][0] ?? array(); ?>
      <div class="pedigree-animal__item">
        <strong class="pedigree-animal__parentesco"><?=$pedigreeH($itemPedigree[0])?></strong>
        <div class="pedigree-animal__dados">
          <span class="pedigree-animal__nome"><?=$pedigreeH($ascendente['nome'] ?? '')?></span>
          <div>FBB: <?=$pedigreeH($ascendente['fbb'] ?? $ascendente['FBB'] ?? '')?></div>
          <div>Tipo: <?=$pedigreeH($ascendente['tipo'] ?? '')?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>
</section>
