<?
include "../../_config.php";
$tipo = $_GET['tipo'];
?>


<? if($tipo == 0){ ?>
<div class="form-group">
    <label for="exampleInputPassword1">Lote</label>
    <select class="form-control select" onchange="linkar_lote(this.value, 0)" name="profissional" id="profissional">
      <option value="">Selecionar</option>
      <option value=""></option>
      <?
      $lote = DBRead('monta', "ORDER BY id desc");
      foreach ($lote as $lote_) {
        $id_macho = $lote_['id_animal'];
        if($lote_['terceiro']){
          $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
        }else{
          $macho = DBRead('animais', "WHERE id = '$id_macho'");
        }
      ?>
        <option value="<?=$lote_['id']?>"><?=$lote_['codigo']?> - <?=$macho[0]['nome']?></option>
      <?}?>
    </select>
</div>
<? } ?>


<? if($tipo == 1){ ?>
<div class="form-group">
    <label for="exampleInputPassword1">Lote</label>
    <select class="form-control select" onchange="linkar_lote(this.value, 1)" name="profissional" id="profissional">
      <option value="">Selecionar</option>
      <option value=""></option>
      <?
      $lote = DBRead('inseminacao', "ORDER BY id desc");
      foreach ($lote as $lote_) {
        $id_macho = $lote_['id_macho'];
        if($lote_['terceiro']){
          $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
        }else{
          $macho = DBRead('animais', "WHERE id = '$id_macho'");
        }
      ?>
        <option value="<?=$lote_['id']?>"><?=$lote_['codigo']?> - <?=$macho[0]['nome']?></option>
      <?}?>
    </select>
</div>
<? } ?>


<? if($tipo == 2){ ?>
  <div class="form-group">
      <label for="exampleInputPassword1">Lote</label>
      <select class="form-control select" onchange="linkar_lote(this.value, 2)" name="profissional" id="profissional">
        <option value="">Selecionar</option>
        <option value=""></option>
        <?
        $lote = DBRead('transplante', "ORDER BY id desc");
        foreach ($lote as $lote_) {
          $id_macho = $lote_['id_pai'];
          if($lote_['terceiro_pai']){
            $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
          }else{
            $macho = DBRead('animais', "WHERE id = '$id_macho'");
          }

          $id_femea = $lote_['id_mae'];
          if($lote_['terceiro_mae']){
            $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
          }else{
            $femea = DBRead('animais', "WHERE id = '$id_femea'");
          }
        ?>
          <option value="<?=$lote_['id']?>"><?=$lote_['codigo']?> - <?=$macho[0]['nome']?> - <?=$femea[0]['nome']?></option>
        <?}?>
      </select>
  </div>
<? } ?>
