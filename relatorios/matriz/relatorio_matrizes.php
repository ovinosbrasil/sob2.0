<? $filtro2 = $_GET['filtro2']; ?>
<script type="text/javascript">
function atualizar(x){
      window.location.href = "geral.php?pg=relatorio_matrizes&filtro="+x+"&filtro2=<?=$filtro2?>";
}

function abrir_matriz(id){
  window.open('geral.php?pg=relatorio_matriz&id_animal='+id+'&filtro=1', '_blank');
}

function atualizar2(x){
      window.location.href = "geral.php?pg=relatorio_matrizes&filtro2="+x;
}
</script>

<section class="content-header">
  <h1>
    Relatório de matrizes
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatórios</a></li>
    <li><a href="#">matrizes</a></li>
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
            * Prolificidade apenas para crias nascidas a partir de 2011 com matriz de até 7 anos no momento do parto.<br/>
          </div>
        </div>

          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th onclick="atualizar(1)" style="cursor:pointer;">Crias<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(2)" style="cursor:pointer;">Crias Avaliadas<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(4)" style="cursor:pointer;">Qualidade das crias (0 a 10)<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(3)" style="cursor:pointer;">Média de venda<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(5)" style="cursor:pointer;">Intervalor de partos<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(6)" style="cursor:pointer;">Kg apartado<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar(7)" style="cursor:pointer;">Prolificidade<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th>Classificação</th>
            </tr>
            <?
            $medias = DBRead('matriz');
            foreach ($medias as $medias_) {
              if($medias_['venda_geral'] > 0){ $media_venda = $media_venda+$medias_['venda_geral']; $qtd_venda++; }
              if($medias_['nota'] > 0){ $media_nota = $media_nota+$medias_['nota']; $qtd_nota++; }
              if($medias_['intervalo'] > 0){ $media_intervalo = $media_intervalo+$medias_['intervalo']; $qtd_intervalo++; }
              if($medias_['peso_apartacao'] > 0){ $media_peso = $media_peso+$medias_['peso_apartacao']; $qtd_peso++; }
              if($medias_['prolificidade'] > 0){ $media_prolificidade = $media_prolificidade+$medias_['prolificidade']; $qtd_prolificidade++; }
            }
            $media_venda = $qtd_venda ? $media_venda/$qtd_venda : 0;
            $media_nota = $qtd_nota ? $media_nota/$qtd_nota : 0;
            $media_intervalo = $qtd_intervalo ? $media_intervalo/$qtd_intervalo : 0;
            $media_peso = $qtd_peso ? $media_peso/$qtd_peso : 0;
            $media_prolificidade = $qtd_prolificidade ? $media_prolificidade/$qtd_prolificidade : 0;
            $filtro = $_GET['filtro'];
            if(!$filtro){ $matriz = DBRead('matriz'); }
            if($filtro == 1){ $matriz = DBRead('matriz', "ORDER BY qtd_crias desc");}
            if($filtro == 2){ $matriz = DBRead('matriz', "ORDER BY qtd_avaliadas desc"); }
            if($filtro == 3){ $matriz = DBRead('matriz', "ORDER BY venda_geral desc"); }
            if($filtro == 4){ $matriz = DBRead('matriz', "ORDER BY nota desc"); }
            if($filtro == 5){ $matriz = DBRead('matriz', "ORDER BY intervalo asc"); }
            if($filtro == 6){ $matriz = DBRead('matriz', "ORDER BY peso_apartacao desc"); }
            if($filtro == 7){ $matriz = DBRead('matriz', "ORDER BY prolificidade desc"); }
            $x=1;
            foreach ($matriz as $matriz_){

              $id_matriz = $matriz_['id_femea'];
              $animal = DBRead('animais', "WHERE id = '$id_matriz'");
              $tipo2_ = $matriz_['qtd_avaliadas'] ? $matriz_['tipo2']*100/$matriz_['qtd_avaliadas'] : 0;
              $tipo3_ = $matriz_['qtd_avaliadas'] ? $matriz_['tipo3']*100/$matriz_['qtd_avaliadas'] : 0;
              $tipo4_ = $matriz_['qtd_avaliadas'] ? $matriz_['tipo4']*100/$matriz_['qtd_avaliadas'] : 0;
              $tipo5_ = $matriz_['qtd_avaliadas'] ? $matriz_['tipo5']*100/$matriz_['qtd_avaliadas'] : 0;
              $estrela = 0;

              $intervalo = $matriz_['intervalo'];
              $anos_intervalo = (int)floor($intervalo/365);
              $resto_ano_intervalo = $intervalo%365;
              $meses_intervalo = (int)floor($resto_ano_intervalo/30);
              $dias_fim_intervalo = $resto_ano_intervalo%30;
              if(($filtro2 == 'Todos') || ($filtro2 == '') || (($filtro2 == 'Rebanho') && ($animal[0]['status'] == 0)) || (($filtro2 == 'Mortos/Vendidos') && ($animal[0]['status'] >= 1)) ){
            ?>
            <tr onclick="abrir_matriz(<?=$animal[0]['id']?>)" style="cursor:pointer;" >
              <td><?=$x?></td>
                <? if($animal[0]['status'] == 0){ ?> <td> <?}else{?> <td style="color:red;"> <? } ?><?=$animal[0]['nome']?></td>
              <td><?=$matriz_['qtd_crias']?></td>
              <td><?=$matriz_['qtd_avaliadas']?></td>
              <? if($matriz_['nota'] > $media_nota){ $estrela++; ?> <td style="color:green;"> <? }else{?> <td style="color:red;"> <? } ?>
                Nota: <?=number_format($matriz_['nota'],2,",",".");?></td>
                <? if($matriz_['venda_geral'] > $media_venda){ $estrela++; ?> <td style="color:green;"> <? }else{?> <td style="color:red;"> <? } ?>
                R$ <?=number_format($matriz_['venda_geral'],2,",",".");?></td>
              <? if(($matriz_['intervalo'] < $media_intervalo) && ($matriz_['intervalo'] > 0)){ $estrela++; ?> <td style="color:green;"> <? } ?>
                <?  if($matriz_['intervalo'] > $media_intervalo){ ?> <td  style="color:red;">  <? } ?>
              <?  if($matriz_['intervalo'] == 0){ ?> <td> N/A <? } ?>
                  <?  if($matriz_['intervalo'] > 0){ ?> <?=$anos_intervalo,"A ", $meses_intervalo,"M ", $dias_fim_intervalo,"D"; }?></td>
              <? if($matriz_['peso_apartacao'] > $media_peso){ $estrela++; ?> <td style="color:green;"> <? }else{?> <td style="color:red;"> <? } ?><?=number_format($matriz_['peso_apartacao'],2,",",".");?> kg</td>
              <? if($matriz_['prolificidade'] > $media_prolificidade){ $estrela++; ?> <td style="color:green;"> <? }else{?> <td style="color:red;"> <? } ?>
                <?=number_format($matriz_['prolificidade'],2,",",".");?></td>
              <td>
                <? if($estrela == 5){ ?><span class="badge bg-green">1</span><? } ?>
                <? if($estrela == 4){ ?><span class="badge bg-blue">2</span><? } ?>
                <? if($estrela == 3){ ?><span class="badge bg-orange">3</span><? } ?>
                <? if($estrela == 2){ ?><span class="badge bg-red">4</span><? } ?>
                <? if($estrela == 1){ ?><span class="badge bg-red">4</span><? } ?>
                <? if($estrela == 0){ ?><span class="badge bg-red">4</span><? } ?>
              </td>
              </tr>
            <? $x++; }} ?>
            </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
