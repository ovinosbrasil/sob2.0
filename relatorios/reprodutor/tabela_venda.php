<table class="table table-bordered" id="tabela_padrao" width="auto">
  <tr>
    <th>Vendas</th>
    <th>Média machos / Rebanho</th>
    <th>Média fêmeas / Rebanho</th>
    <th>Média geral / Rebanho</th>
    <th>Valor total</th>
  </tr>
  <?
  $reprodutor = DBRead('reprodutor', "WHERE id_macho = '$id_animal'");
  $media = DBRead('reprodutor');
  foreach ($media as $media_){
    if($media_['venda_macho'] > 0){ $total_macho = $total_macho+$media_['venda_macho']; $qtd_macho++;}
    if($media_['venda_femea'] > 0){ $total_femea = $total_femea+$media_['venda_femea']; $qtd_femea++;}
  }
  $total = $total_macho+$total_femea;
  $media_macho = $total_macho/$qtd_macho;
  $media_femea = $total_femea/$qtd_femea;
  $media_total = $total/($qtd_macho+$qtd_femea);
  ?>

  <tr>
    <td><?=$reprodutor[0]['qtd_vendas']?></td>
    <td>
      <? if($reprodutor[0]['venda_macho'] > $media_macho){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
        R$ <?=number_format($reprodutor[0]['venda_macho'],2,",",".");?> </span>/ R$ <?=number_format($media_macho,2,",",".");?></td>
    <td>
      <? if($reprodutor[0]['venda_femea'] > $media_femea){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
        R$ <?=number_format($reprodutor[0]['venda_femea'],2,",",".");?> </span>/ R$ <?=number_format($media_femea,2,",",".");?></td>
    <td>
      <? if($reprodutor[0]['venda_geral'] > $media_total){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      R$ <?=number_format($reprodutor[0]['venda_geral'],2,",",".");?> </span>/ R$ <?=number_format($media_total,2,",",".");?></td>
    <td>R$ <?=number_format($reprodutor[0]['venda_geral']*$reprodutor[0]['qtd_vendas'],2,",",".");?></td>
    </tr>
  </table>

  <div class="form-group" style="margin-top:1%; color:red;">
    Atenção: Médias calculadas com as vendas a partir de 01/01/2011
  </div>
