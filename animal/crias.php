
<div class="row">
<div class="col-md-12">
  <!-- general form elements -->
  <div class="box-body">
    <? if($animal[0]['sexo'] == "Fêmea"){ include "animal/crias/femea.php"; }?>
    <? if($animal[0]['sexo'] == "Macho"){ include "animal/crias/machos.php"; }?>
  </div>
</div>
</div>
