<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

$id_animal = $_GET['id_animal'];
$aba = $_GET['aba'];
$animal = DBRead('animais',"WHERE id = '$id_animal'");
?>
<div id="transparencia">asd</div>

<section class="content-header">
  <h1>
    <?
    echo $animal[0]['nome'];
    if($animal[0]['status'] == '0'){ ?> <span style="color:#37abc0;">( Rebanho )<? }
    if($animal[0]['status'] == '1'){ ?> <span style="color:red;">( Morto )<? }
    if($animal[0]['status'] == '2'){ ?> <span style="color:green;">( Vendido )<? } ?>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Animais</a></li>
    <li><a href="#">Pesquisar</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <!-- Custom Tabs -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <? if(($aba == 'geral') || ($aba == '')){?><li class="active"><a href="#tab_1" data-toggle="tab">Geral</a></li><? }else{?> <li><a href="#tab_1" data-toggle="tab">Geral</a></li><? } ?>
          <? if($aba == 'pedigree'){ ?><li class="active"><a href="#tab_2" data-toggle="tab">Pedigree</a></li><? }else{?> <li><a href="#tab_2" data-toggle="tab">Pedigree</a></li><? } ?>
          <? if($aba == 'avaliacao'){?> <li class="active"><a href="geral.php?pg=animal&aba=avaliacao&id_animal=<?=$id_animal?>">Avaliações</a></li><? }else{?> <li><a href="geral.php?pg=animal&aba=avaliacao&id_animal=<?=$id_animal?>">Avaliações</a></li><? } ?>
          <? if($aba == 'crias'){ ?><li class="active"><a href="#tab_4" data-toggle="tab">Crias</a></li><? }else{?> <li><a href="#tab_4" data-toggle="tab">Crias</a></li><? } ?>
          <? if($aba == 'vender'){?> <li class="active"><a href="#tab_5" data-toggle="tab">Vender</a></li><? }else{?> <li><a href="#tab_5" data-toggle="tab">Vender</a></li><? } ?>
          <? if($aba == 'saida'){?> <li class="active"><a href="#tab_6" data-toggle="tab">Saida/morte</a></li><? }else{?> <li><a href="#tab_6" data-toggle="tab">Saida/morte</a></li><? } ?>
          <? if($aba == 'doenca'){?> <li class="active"><a href="#tab_7" data-toggle="tab">Doenças</a></li><? }else{?> <li><a href="#tab_7" data-toggle="tab">Doenças</a></li><? } ?>
          <? if($aba == 'vacinas'){?> <li class="active"><a href="#tab_8" data-toggle="tab">Vacinas</a></li><? }else{?> <li><a href="#tab_8" data-toggle="tab">Vacinas</a></li><? } ?>
          <? if($aba == 'premios'){?> <li class="active"><a href="#tab_9" data-toggle="tab">Prêmios</a></li><? }else{?> <li><a href="#tab_9" data-toggle="tab">Prêmios</a></li><? } ?>
          <? if($aba == 'reproducao'){?> <li class="active"><a href="#tab_10" data-toggle="tab">Lotes de Reprodução</a></li><? }else{?> <li><a href="#tab_10" data-toggle="tab">Lotes de Reprodução</a></li><? } ?>
        </ul>
        <div class="tab-content">


        <? if($aba == '') {?><div class="tab-pane active" id="tab_1"><? }else{?><div class="tab-pane" id="tab_1"><? } ?>
          <? include "animal/geral.php"; ?>
        </div>

        <!-- /.tab-pane -->
        <? if($aba == 'pedigree'){?><div class="tab-pane active" id="tab_2"><? }else{ ?> <div class="tab-pane" id="tab_2"><? } ?>
          <? include "animal/pedigree.php"; ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'avaliacao'){?><div class="tab-pane active" id="tab_3"><? }else{ ?> <div class="tab-pane" id="tab_3"><? } ?>
          <? include "animal/avaliacao/avaliacao.php"; ?>
        </div>
        <!-- /.tab-content -->


        <!-- /.tab-pane -->
        <? if($aba == 'crias'){?><div class="tab-pane active" id="tab_4"><? }else{ ?> <div class="tab-pane" id="tab_4"><? } ?>
          <? include "animal/crias/crias.php"; ?>
        </div>
        <!-- /.tab-content -->

        <!-- /.tab-pane -->
        <? if($aba == 'excluir'){?><div class="tab-pane active" id="tab_5"><? }else{ ?> <div class="tab-pane" id="tab_5"><? } ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'excluir'){?><div class="tab-pane active" id="tab_6"><? }else{ ?> <div class="tab-pane" id="tab_6"><? } ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'excluir'){?><div class="tab-pane active" id="tab_7"><? }else{ ?> <div class="tab-pane" id="tab_7"><? } ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'excluir'){?><div class="tab-pane active" id="tab_8"><? }else{ ?> <div class="tab-pane" id="tab_8"><? } ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'excluir'){?><div class="tab-pane active" id="tab_9"><? }else{ ?> <div class="tab-pane" id="tab_9"><? } ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'excluir'){?><div class="tab-pane active" id="tab_10"><? }else{ ?> <div class="tab-pane" id="tab_10"><? } ?>
        </div>
        <!-- nav-tabs-custom -->
    </div>
    </div>
  </div>
  </div>
</section>
