<?
$filtro = $_GET['filtro'];
$filtro2 = $_GET['filtro2'];
?>
<script type="text/javascript">
function atualizar(x){
      window.location.href = "geral.php?pg=relatorio_reprodutores&filtro2=<?=$filtro2?>&filtro="+x;
}

function abrir_reprodutor(id){
  window.open('geral.php?pg=relatorio_reprodutor&id_animal='+id+'&filtro=1', '_blank');
}

function atualizar2(x){
      window.location.href = "geral.php?pg=relatorio_reprodutores&filtro=<?=$filtro?>&filtro2="+x;
}
</script>

<section class="content-header">
  <h1>
    Relatório de reprodutores
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatórios</a></li>
    <li><a href="#">Reprodutores</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">

    <div class="col-md-12">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">

          <div class="col-md-4" style="margin-left:-12px;">
            <label for="exampleInputPassword1">Filtro</label>
              <div class="form-group">
                <select  class="form-control select" onchange="atualizar2(this.value)">
                  <?
                  if($filtro2){ ?> <option value="<?=$filtro2?>"><?=$filtro2?></option> <? }else{ ?> <option value="">Selecionar</option><? } ?>
                  <option value=""></option>
                  <option value="Todos">Todos</option>
                  <option value="Rebanho">Rebanho</option>
                  <option value="Mortos/Vendidos">Mortos/Vendidos</option>
                </select>
              </div>
          </div>

          <div class="col-md-12" style="margin-left:-12px;">
          <div class="form-group" style="color:red; ">
            * Crias Avaliadas = Crias que passaram pelo processo de avaliação completo.<br/>
            * Qualidade das crias = Nota baseada na tipificação média das crias.<br/>
            * Média de vendas apenas para crias vendidas a partir de 2011.<br/>
            * Mortes registradas no nascimento.<br/>
            * GMD = Ganho médio diário de peso das crias.<br/>
          </div>
          </div>
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th onclick="atualizar(1)" style="cursor:pointer;">Crias<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(2)" style="cursor:pointer;">Crias Avaliadas<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(4)" style="cursor:pointer;">Qualidade das crias<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(3)" style="cursor:pointer;">Média de venda<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(5)" style="cursor:pointer;">Morte nasc.<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(6)" style="cursor:pointer;">Ganho de peso<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th>Classificação</th>
            </tr>
            <?
            $medias = DBRead('reprodutor');
            foreach ($medias as $medias_) {
              if($medias_['venda_geral'] > 0){ $media_venda = $media_venda+$medias_['venda_geral']; $qtd_venda++; }
              if($medias_['nota'] > 0){ $media_nota = $media_nota+$medias_['nota']; $qtd_nota++; }
              if($medias_['qtd_mortes'] > 0){ $media_mortes = $media_mortes+$medias_['qtd_mortes']; $qtd_morte++; }
              if($medias_['gmd'] > 0){ $media_gmd = $media_gmd+$medias_['gmd']; $qtd_gmd++; }
            }
            $media_venda = $qtd_venda ? $media_venda/$qtd_venda : 0;
            $media_nota = $qtd_nota ? $media_nota/$qtd_nota : 0;
            $media_mortes = $qtd_morte ? $media_mortes/$qtd_morte : 0;
            $media_gmd = $qtd_gmd ? $media_gmd/$qtd_gmd : 0;


            if(!$filtro){ $reprodutor = DBRead('reprodutor'); }
            if($filtro == 1){ $reprodutor = DBRead('reprodutor', "ORDER BY qtd_crias desc"); }
            if($filtro == 2){ $reprodutor = DBRead('reprodutor', "ORDER BY qtd_avaliadas desc"); }
            if($filtro == 3){ $reprodutor = DBRead('reprodutor', "ORDER BY venda_geral desc"); }
            if($filtro == 4){ $reprodutor = DBRead('reprodutor', "ORDER BY nota desc"); }
            if($filtro == 5){ $reprodutor = DBRead('reprodutor', "ORDER BY qtd_mortes asc"); }
            if($filtro == 6){ $reprodutor = DBRead('reprodutor', "ORDER BY gmd desc"); }
            $x=1;
            foreach ($reprodutor as $reprodutor_){

              $id_reprodutor = $reprodutor_['id_macho'];
              $animal = DBRead('animais', "WHERE id = '$id_reprodutor'");
              $tipo2_ = $reprodutor_['tipo2']*100/$reprodutor_['qtd_avaliadas'];
              $tipo3_ = $reprodutor_['tipo3']*100/$reprodutor_['qtd_avaliadas'];
              $tipo4_ = $reprodutor_['tipo4']*100/$reprodutor_['qtd_avaliadas'];
              $tipo5_ = $reprodutor_['tipo5']*100/$reprodutor_['qtd_avaliadas'];
              $estrela = 0;

              $filtro2 = $_GET['filtro2'];
              if(($filtro2 == 'Todos') || ($filtro2 == '') || (($filtro2 == 'Rebanho') && ($animal[0]['status'] == 0)) || (($filtro2 == 'Mortos/Vendidos') && ($animal[0]['status'] >= 1)) ){
            ?>

            <tr onclick="abrir_reprodutor(<?=$animal[0]['id']?>)" style="cursor:pointer;" >
              <td><?=$x?></td>
              <? if($animal[0]['status'] == 0){ ?> <td> <?}else{?> <td style="color:red;"> <? } ?><?=$animal[0]['nome']?></td>
              <td><?=$reprodutor_['qtd_crias']?></td>
              <td><?=$reprodutor_['qtd_avaliadas']?></td>
              <? if($reprodutor_['nota'] > $media_nota){ $estrela++; ?> <td style="color:green;"> <? }else{?> <td style="color:red;"> <? } ?>
                Nota: <?=number_format($reprodutor_['nota'],2,",",".");?></td>
              <? if($reprodutor_['venda_geral'] > $media_venda){ $estrela++; ?> <td style="color:green;"> <? }else{?> <td style="color:red;"> <? } ?>
                R$ <?=number_format($reprodutor_['venda_geral'],2,",",".");?></td>
              <? if($reprodutor_['qtd_mortes'] < $media_mortes){ $estrela++; ?> <td style="color:green;"> <? }else{?> <td style="color:red;"> <? } ?>
                <?=number_format($reprodutor_['qtd_mortes'], 1, ',', '.')?>%</td>
              <? if($reprodutor_['gmd'] > $media_gmd){ $estrela++; ?> <td style="color:green;"> <? }else{?> <td style="color:red;"> <? } ?><?=number_format($reprodutor_['gmd']*1000,2,",",".");?> gmd</td>
              <td>
                <? if($estrela == 4){ ?><span class="badge bg-green">1</span><? } ?>
                <? if($estrela == 3){ ?><span class="badge bg-blue">2</span><? } ?>
                <? if($estrela == 2){ ?><span class="badge bg-orange">3</span><? } ?>
                <? if($estrela == 1){ ?><span class="badge bg-red">4</span><? } ?>
                <? if($estrela == 0){ ?><span class="badge bg-red">4</span><? } ?>
              </td>
              </tr>
            <? $x++; } }?>
            </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
