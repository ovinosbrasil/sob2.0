<table class="table table-bordered" id="tabela_padrao" style="width:50%;">
  <tr>
    <th>Crias</th>
    <th>Ganho média diário (Crias)</th>
    <th>Média do rebanho</th>
  </tr>

<?
$reprodutor = DBRead('reprodutor', "WHERE id_macho = '$id_animal'");
$media = DBRead('reprodutor');
foreach ($media as $media_){
  if($media_['gmd'] > 0){
    $total = $total + $media_['gmd'];
    $qtd++;
  }
}
$media_total = $total/$qtd;
?>

  <tr>
    <td><?=$reprodutor[0]['qtd_crias']?></td>
    <td>
      <? if($reprodutor[0]['gmd'] > $media_total){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
        <?=number_format($reprodutor[0]['gmd']*1000, 1, ',', '.')?> g/dia</span></td>
    <td><?=number_format($media_total*1000, 1, ',', '.')?> g/dia</td>
    </tr>
  </table>

  <div class="form-group" style="margin-top:1%; color:red;">
    Atenção: Médias calculadas com animais que tiveram 2ª avaliação realizada até os 10 meses.
  </div>
