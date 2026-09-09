<table class="table table-bordered" id="tabela_padrao" style="width:50%;">
  <tr>
    <th>Crias</th>
    <th>Peso de apartação</th>
    <th>Média do rebanho</th>
  </tr>

<?
$matriz = DBRead('matriz', "WHERE id_femea = '$id_animal'");
$media = DBRead('matriz');
foreach ($media as $media_){
  if($media_['peso_apartacao'] > 0){
    $total = $total + $media_['peso_apartacao'];
    $qtd++;
  }
}
$media_total = $total/$qtd;
?>

  <tr>
    <td><?=$matriz[0]['qtd_crias']?></td>
    <td>
      <? if($matriz[0]['peso_apartacao'] > $media_total){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
        <?=number_format($matriz[0]['peso_apartacao'], 1, ',', '.')?> kg</span></td>
    <td><?=number_format($media_total, 1, ',', '.')?> kg</td>
    </tr>
  </table>

  <div class="form-group" style="margin-top:1%; color:red;">
    Atenção: Calculo aproximado para 90 dias dos animais que tiveram 1ª avaliação realizada até os 5 meses.
  </div>
