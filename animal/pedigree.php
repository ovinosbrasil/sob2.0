<?
$avo1 = $pai[0]['pai'];
if($pai[0]['terceiro_pai']){ $avo1 = DBRead('terceiros', "WHERE id = '$avo1'"); }else{ $avo1 = DBRead('animais', "WHERE id = '$avo1'"); }
$avo2= $pai[0]['mae'];
if($pai[0]['terceiro_mae']){ $avo2 = DBRead('terceiros', "WHERE id = '$avo2'"); }else{ $avo2 = DBRead('animais', "WHERE id = '$avo2'"); }
$avo3 = $mae[0]['pai'];
if($mae[0]['terceiro_pai']){ $avo3 = DBRead('terceiros', "WHERE id = '$avo3'"); }else{ $avo3 = DBRead('animais', "WHERE id = '$avo3'"); }
$avo4= $mae[0]['mae'];
if($mae[0]['terceiro_mae']){ $avo4 = DBRead('terceiros', "WHERE id = '$avo4'"); }else{ $avo4 = DBRead('animais', "WHERE id = '$avo4'"); }


$avo5 = $avo1[0]['pai'];
if($avo1[0]['terceiro_pai']){ $avo5 = DBRead('terceiros', "WHERE id = '$avo5'"); }else{ $avo5 = DBRead('animais', "WHERE id = '$avo5'"); }
$avo6 = $avo1[0]['mae'];
if($avo1[0]['terceiro_mae']){ $avo6 = DBRead('terceiros', "WHERE id = '$avo6'"); }else{ $avo6 = DBRead('animais', "WHERE id = '$avo6'"); }

$avo7 = $avo2[0]['pai'];
if($avo2[0]['terceiro_pai']){ $avo7 = DBRead('terceiros', "WHERE id = '$avo7'"); }else{ $avo7 = DBRead('animais', "WHERE id = '$avo7'"); }
$avo8 = $avo2[0]['mae'];
if($avo2[0]['terceiro_mae']){ $avo8 = DBRead('terceiros', "WHERE id = '$avo8'"); }else{ $avo8 = DBRead('animais', "WHERE id = '$avo8'"); }

$avo9 = $avo3[0]['pai'];
if($avo3[0]['terceiro_pai']){ $avo9 = DBRead('terceiros', "WHERE id = '$avo9'"); }else{ $avo9 = DBRead('animais', "WHERE id = '$avo9'"); }
$avo10 = $avo3[0]['mae'];
if($avo3[0]['terceiro_mae']){ $avo10 = DBRead('terceiros', "WHERE id = '$avo10'"); }else{ $avo10 = DBRead('animais', "WHERE id = '$avo10'"); }

$avo11 = $avo4[0]['pai'];
if($avo4[0]['terceiro_pai']){ $avo11 = DBRead('terceiros', "WHERE id = '$avo11'"); }else{ $avo11 = DBRead('animais', "WHERE id = '$avo11'"); }
$avo12 = $avo4[0]['mae'];
if($avo4[0]['terceiro_mae']){ $avo12 = DBRead('terceiros', "WHERE id = '$avo12'"); }else{ $avo12 = DBRead('animais', "WHERE id = '$avo12'"); }
?>


<div class="row">
<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1"></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style=" width:100%; border:transparent; resize: none"></textarea>
    </div>
    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1"></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style=" width:100%; border:transparent; resize: none"></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1"></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none"></textarea>
    </div>
    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Pai</label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$pai[0]['nome']?>&#10;FBB:<?=$pai[0]['FBB']?>
        &#10;Tipo:<?=$pai[0]['tipo']?></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Mãe</label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style=" width:100%; border:transparent; resize: none" readonly="readonly"><?=$mae[0]['nome']?>&#10;FBB:<?=$mae[0]['FBB']?>
        &#10;Tipo:<?=$mae[0]['tipo']?></textarea>
    </div>
  </div>
</div>

<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1"></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style=" width:100%; border:transparent; resize: none"></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1"></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none"></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Avô paterno<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style=" width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo1[0]['nome']?>&#10;FBB:<?=$avo1[0]['FBB']?>
        &#10;Tipo:<?=$avo1[0]['tipo']?></textarea>
    </div>
    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Avó paterna<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo2[0]['nome']?>&#10;FBB:<?=$avo2[0]['FBB']?>
        &#10;Tipo:<?=$avo2[0]['tipo']?></textarea>
    </div>


    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Avô materno<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo3[0]['nome']?>&#10;FBB:<?=$avo3[0]['FBB']?>
        &#10;Tipo:<?=$avo3[0]['tipo']?></textarea>
    </div>


    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Avó materna<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo4[0]['nome']?>&#10;FBB:<?=$avo4[0]['FBB']?>
        &#10;Tipo:<?=$avo4[0]['tipo']?></textarea>
    </div>
  </div>
</div>

<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Bisavô paterno<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style=" width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo5[0]['nome']?>&#10;FBB:<?=$avo5[0]['FBB']?>
        &#10;Tipo:<?=$avo5[0]['tipo']?></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Bisavó paterna<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style=" width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo6[0]['nome']?>&#10;FBB:<?=$avo6[0]['FBB']?>
        &#10;Tipo:<?=$avo6[0]['tipo']?></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Bisavô paterno<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo7[0]['nome']?>&#10;FBB:<?=$avo7[0]['FBB']?>
        &#10;Tipo:<?=$avo7[0]['tipo']?></textarea>
    </div>


    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Bisavó paterna<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo8[0]['nome']?>&#10;FBB:<?=$avo8[0]['FBB']?>
        &#10;Tipo:<?=$avo8[0]['tipo']?></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Bisavô materno<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo9[0]['nome']?>&#10;FBB:<?=$avo9[0]['FBB']?>
        &#10;Tipo:<?=$avo9[0]['tipo']?></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Bisavó materna<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo10[0]['nome']?>&#10;FBB:<?=$avo10[0]['FBB']?>
        &#10;Tipo:<?=$avo10[0]['tipo']?></textarea>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Bisavô materno<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo11[0]['nome']?>&#10;FBB:<?=$avo11[0]['FBB']?>
        &#10;Tipo:<?=$avo11[0]['tipo']?></textarea>
    </div>
    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Bisavó materna<span style="color:#F00;">*</span></label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="3" style="width:100%; border:transparent; resize: none" readonly="readonly"><?=$avo12[0]['nome']?>&#10;FBB:<?=$avo12[0]['FBB']?>
        &#10;Tipo:<?=$avo12[0]['tipo']?></textarea>
    </div>
  </div>
</div>
</div>
