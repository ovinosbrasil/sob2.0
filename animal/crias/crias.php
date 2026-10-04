<?php require_once __DIR__ . '/../../includes/paginacao_crias.php'; ?>
<style>
  #crias-animal > .col-md-12 > .box-body { padding:0; }
  #crias-animal .cabecalho-crias { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px; }
  #crias-animal h3, #crias-animal h4.box-title { font-size:16px; margin:0; }
  #crias-animal h4.box-title { margin:20px 0 15px; }
  #crias-animal .table td, #crias-animal .table th { vertical-align:middle; }
</style>


<div class="row" id="crias-animal">
<div class="col-md-12">
  <!-- general form elements -->
  <div class="box-body">
    <? if($animal[0]['sexo'] == "Fêmea"){ include "animal/crias/femea.php"; }?>
    <? if($animal[0]['sexo'] == "Macho"){ include "animal/crias/machos.php"; }?>
  </div>
</div>
</div>
