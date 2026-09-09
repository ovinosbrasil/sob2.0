<table class="table table-bordered" id="tabela_padrao" style="width:50%;">
  <tr>
    <th>Crias</th>
    <th>Mortalidade no nascimento</th>
    <th>Média do rebanho</th>
  </tr>

<?
$reprodutor = DBRead('reprodutor', "WHERE id_macho = '$id_animal'");
$media = DBRead('reprodutor');
foreach ($media as $media_){
  if($media_['qtd_mortes'] > 0){
    $total = $total + $media_['qtd_mortes'];
    $qtd++;
  }
}
$media_total = $total/$qtd;
?>

  <tr>
    <td><?=$reprodutor[0]['qtd_crias']?></td>
    <td>
      <? if($reprodutor[0]['qtd_mortes'] < $media_total){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
        <?=number_format($reprodutor[0]['qtd_mortes'], 1, ',', '.')?>%</span></td>
    <td><?=number_format($media_total, 1, ',', '.')?>%</td>
    </tr>
  </table>
