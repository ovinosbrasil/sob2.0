<?
function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

function addDayIntoDate($date,$days) {
     $thisyear = substr ( $date, 0, 4 );
     $thismonth = substr ( $date, 4, 2 );
     $thisday =  substr ( $date, 6, 2 );
     $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday + $days, $thisyear );
     return strftime("%Y%m%d", $nextdate);
}

$id_animal = (int)($_GET['id_animal'] ?? 0);
$aba = $_GET['aba'] ?? '';
if ($aba === 'pedigree') $aba = 'geral';
$animal = DBRead('animais',"WHERE id = '$id_animal'");
$animalCabecalho = $animal[0] ?? array();
?>
<div id="transparencia">asd</div>

<section class="content-header">
  <h1 style="display:flex; flex-wrap:wrap; align-items:center; gap:10px;">
    <span><?=htmlspecialchars($animalCabecalho['nome'] ?? '', ENT_QUOTES, 'UTF-8')?></span>
    <?php $situacoesAnimal = array(0 => 'Rebanho', 1 => 'Morto', 2 => 'Vendido', 3 => 'Empréstimo', 4 => 'Doação', 5 => 'Abate');
    $statusAnimal = (int)($animalCabecalho['status'] ?? 0); ?>
    <span class="<?=$statusAnimal === 0 ? 'text-info' : ($statusAnimal === 2 ? 'text-success' : 'text-danger')?>">(<?=$situacoesAnimal[$statusAnimal] ?? '—'?>)</span>
    <a href="animal/_imprimir.php?id_animal=<?=$id_animal?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm" title="Imprimir animal" aria-label="Imprimir animal"><i class="fa fa-print" aria-hidden="true"></i></a>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-github-alt"></i> Animais</a></li>
    <li><a href="#">Pesquisar</a></li>
  </ol>
</section>

<style>
  #pagina-animal .nav-tabs-custom { box-shadow:0 1px 1px rgba(0,0,0,.1); }
  #pagina-animal .nav-tabs { display:flex; flex-wrap:wrap; }
  #pagina-animal .nav-tabs > li { float:none; border-top:0; }
  #pagina-animal .nav-tabs > li.active > a { border-bottom:2px solid #00a65a; }
  #pagina-animal .tab-content { padding:20px; }
  #form-dados-animal .box-body { padding:0; }
  #form-dados-animal .acoes-animal { border-top:1px solid #f4f4f4; padding-top:15px; margin-top:5px; text-align:right; }
  #pagina-animal .registros-animal .box-body { padding:0; }
  #pagina-animal .titulo-registros { font-size:16px; margin:0 0 15px; }
  #pagina-animal .acoes-registros { border-top:1px solid #f4f4f4; padding-top:15px; margin-bottom:20px; }
  #pagina-animal .registros-animal .table td,
  #pagina-animal .registros-animal .table th { vertical-align:middle; }
  #pagina-animal .celula-animal-link { cursor:pointer; }
  @media (max-width:767px) {
    #pagina-animal .tab-content { padding:15px; }
    #form-dados-animal .acoes-animal .btn { width:100%; }
  }
</style>
<section class="content" id="pagina-animal">
  <div class="row">
    <div class="col-md-12">
      <!-- Custom Tabs -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <? if(($aba == 'geral') || ($aba == '')){?><li class="active"><a href="#tab_1" data-toggle="tab">Geral</a></li><? }else{?> <li><a href="#tab_1" data-toggle="tab">Geral</a></li><? } ?>
          <? if($aba == 'avaliacao'){?> <li class="active"><a href="geral.php?pg=animal&aba=avaliacao&id_animal=<?=$id_animal?>">Avaliações</a></li><? }else{?> <li><a href="geral.php?pg=animal&aba=avaliacao&id_animal=<?=$id_animal?>">Avaliações</a></li><? } ?>
          <? if($aba == 'crias'){ ?><li class="active"><a href="#tab_4" data-toggle="tab">Crias</a></li><? }else{?> <li><a href="#tab_4" data-toggle="tab">Crias</a></li><? } ?>
          <? if($aba == 'vender'){?> <li class="active"><a href="#tab_5" data-toggle="tab">Vender</a></li><? }else{?> <li><a href="#tab_5" data-toggle="tab">Vender</a></li><? } ?>
          <? if($aba == 'saida'){?> <li class="active"><a href="#tab_6" data-toggle="tab">Saída/morte</a></li><? }else{?> <li><a href="#tab_6" data-toggle="tab">Saída/morte</a></li><? } ?>
          <? if($aba == 'doenca'){?> <li class="active"><a href="#tab_7" data-toggle="tab">Doenças</a></li><? }else{?> <li><a href="#tab_7" data-toggle="tab">Doenças</a></li><? } ?>
          <? if($aba == 'vacina'){?> <li class="active"><a href="#tab_8" data-toggle="tab">Vacinas</a></li><? }else{?> <li><a href="#tab_8" data-toggle="tab">Vacinas</a></li><? } ?>
          <? if($aba == 'premios'){?> <li class="active"><a href="#tab_9" data-toggle="tab">Prêmios</a></li><? }else{?> <li><a href="#tab_9" data-toggle="tab">Prêmios</a></li><? } ?>
          <? if($aba == 'reproducao'){?> <li class="active"><a href="#tab_10" data-toggle="tab">Lotes de Reprodução</a></li><? }else{?> <li><a href="#tab_10" data-toggle="tab">Lotes de Reprodução</a></li><? } ?>
          <li <?=$aba == 'excluir' ? 'class="active"' : ''?>><a href="#tab_excluir" data-toggle="tab" class="text-danger">Exclusão</a></li>
        </ul>
        <div class="tab-content">


        <? if(($aba == '') || ($aba == 'geral')) {?><div class="tab-pane active" id="tab_1"><? }else{?><div class="tab-pane" id="tab_1"><? } ?>
          <? include "animal/geral.php"; ?>
        </div>

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
        <? if($aba == 'vender'){?><div class="tab-pane active" id="tab_5"><? }else{ ?> <div class="tab-pane" id="tab_5"><? } ?>
          <? include "animal/vender/vender.php"; ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'saida'){?><div class="tab-pane active" id="tab_6"><? }else{ ?> <div class="tab-pane" id="tab_6"><? } ?>
          <? include "animal/saida/saida.php"; ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'doenca'){?><div class="tab-pane active" id="tab_7"><? }else{ ?> <div class="tab-pane" id="tab_7"><? } ?>
          <? include "animal/doenca/doenca.php"; ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'vacina'){?><div class="tab-pane active" id="tab_8"><? }else{ ?> <div class="tab-pane" id="tab_8"><? } ?>
          <? include "animal/vacina/vacina.php"; ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'premios'){?><div class="tab-pane active" id="tab_9"><? }else{ ?> <div class="tab-pane" id="tab_9"><? } ?>
          <? include "animal/premios/premios.php"; ?>
        </div>
        <!-- nav-tabs-custom -->

        <!-- /.tab-pane -->
        <? if($aba == 'reproducao'){?><div class="tab-pane active" id="tab_10"><? }else{ ?> <div class="tab-pane" id="tab_10"><? } ?>
          <? include "animal/reproducao/reproducao.php"; ?>
        </div>
        <div class="tab-pane <?=$aba == 'excluir' ? 'active' : ''?>" id="tab_excluir">
          <h3 style="font-size:18px; margin-top:0;">Excluir animal</h3>
          <p class="help-block">A exclusão remove o cadastro deste animal e todas as suas referências. Esta ação não pode ser desfeita.</p>
          <p><strong><?=htmlspecialchars($animalCabecalho['nome'] ?? '', ENT_QUOTES, 'UTF-8')?></strong></p>
          <button type="button" class="btn btn-danger" data-id="<?=$id_animal?>" data-origem="Rebanho" data-nome="<?=htmlspecialchars($animalCabecalho['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>" onclick="confirmarExclusaoRebanho(this)"><i class="fa fa-trash-o" aria-hidden="true"></i> Excluir animal</button>
        </div>
        <!-- nav-tabs-custom -->
    </div>
    </div>
  </div>
  </div>
</section>
