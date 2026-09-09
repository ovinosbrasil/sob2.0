<table class="table table-bordered" id="tabela_padrao">
  <tr>
    <th onclick="atualizar(1)" style="cursor:pointer;">Crias</th>
    <th onclick="atualizar(2)" style="cursor:pointer;">Crias avaliadas</th>
    <th onclick="atualizar(3)" style="cursor:pointer;">Tipo 2</th>
    <th onclick="atualizar(4)" style="cursor:pointer;">Tipo 3</th>
    <th onclick="atualizar(5)" style="cursor:pointer;">Tipo 4</th>
    <th onclick="atualizar(6)" style="cursor:pointer;">Tipo 5</th>
    <th onclick="atualizar(6)" style="cursor:pointer;">Nota / Rebanho</th>
  </tr>
  <?
  $matriz = DBRead('matriz', "WHERE id_femea = '$id_animal'");
  $tipo2_ = $matriz[0]['tipo2']*100/$matriz[0]['qtd_avaliadas'];
  $tipo3_ = $matriz[0]['tipo3']*100/$matriz[0]['qtd_avaliadas'];
  $tipo4_ = $matriz[0]['tipo4']*100/$matriz[0]['qtd_avaliadas'];
  $tipo5_ = $matriz[0]['tipo5']*100/$matriz[0]['qtd_avaliadas'];

  $media = DBRead('matriz');
  foreach ($media as $media_){
    if($media_['nota'] > 0){
      $total = $total + $media_['nota'];
      $qtd++;
    }
  }
  $media_total = $total/$qtd;
  ?>
  <tr>
    <td><?=$matriz[0]['qtd_crias']?></td>
    <td><?=$matriz[0]['qtd_avaliadas']?></td>
    <td>( <?=$matriz[0]['tipo2']?> ) <span class="badge bg-red"><?=number_format($tipo2_, 1, ',', '.')?>%</span></td>
    <td>( <?=$matriz[0]['tipo3']?> ) <span class="badge bg-orange"><?=number_format($tipo3_, 1, ',', '.')?>%</span></td>
    <td>( <?=$matriz[0]['tipo4']?> ) <span class="badge bg-blue"><?=number_format($tipo4_, 1, ',', '.')?>%</span></td>
    <td>( <?=$matriz[0]['tipo5']?> ) <span class="badge bg-green"><?=number_format($tipo5_, 1, ',', '.')?>%</span></td>
    <td>
      <? if($matriz[0]['nota'] > $media_total){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz[0]['nota'],2,",",".");?> </span> / <?=number_format($media_total,2,",",".");?></td>
    </tr>
  </table>
