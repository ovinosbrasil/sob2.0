<table class="table table-bordered" id="tabela_padrao">
  <tr>
    <th>Crias Avaliadas</th>
    <th>Pesagem</th>
    <th>Cabeça</th>
    <th>Pescoço</th>
    <th>Quarto anterior</th>
    <th>Barril</th>
    <th>Quarto posterior</th>
    <th>Comprimento</th>
    <th>Orgão sexual</th>
    <th>Gordura</th>
    <th>Cobertura</th>
    <th>Cor</th>
    <th>Conformação</th>

  </tr>

<?
$reprodutor = DBRead('reprodutor', "WHERE id_macho = '$id_animal'");
$media = DBRead('reprodutor');
foreach ($media as $media_){
  if($media_['pesagem'] > 0){
    $pesagem = $pesagem+$media_['pesagem'];
    $cabeca = $cabeca+$media_['cabeca'];
    $pescoco = $pescoco+$media_['pescoco'];
    $quarto_anterior = $quarto_anterior+$media_['quarto_anterior'];
    $barril = $barril+$media_['barril'];
    $quarto_posterior = $quarto_posterior+$media_['quarto_posterior'];
    $comprimento = $comprimento+$media_['comprimento'];
    $orgao = $orgao+$media_['orgao'];
    $gordura = $gordura+$media_['gordura'];
    $cobertura = $cobertura+$media_['cobertura'];
    $conformacao = $conformacao+$media_['conformacao'];
    $qtd++;
  }
}
$pesagem = $pesagem/$qtd;
$cabeca = $cabeca/$qtd;
$pescoco = $pescoco/$qtd;
$quarto_anterior = $quarto_anterior/$qtd;
$barril = $barril/$qtd;
$quarto_posterior = $quarto_posterior/$qtd;
$comprimento = $comprimento/$qtd;
$orgao = $orgao/$qtd;
$gordura = $gordura/$qtd;
$cobertura = $cobertura/$qtd;
$conformacao = $conformacao/$qtd;
?>

  <tr>
    <td><?=$reprodutor[0]['qtd_avaliadas']?></td>
    <td>
      <? if($reprodutor[0]['pesagem'] > $pesagem){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['pesagem'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['cabeca'] > $cabeca){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['cabeca'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['pescoco'] > $pescoco){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['pescoco'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['quarto_anterior'] > $quarto_anterior){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['quarto_anterior'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['barril'] > $barril){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['barril'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['quarto_posterior'] > $quarto_posterior){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['quarto_posterior'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['comprimento'] > $comprimento){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['comprimento'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['orgao'] > $orgao){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['orgao'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['gordura'] > $gordura){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['gordura'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['cobertura'] > $cobertura){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['cobertura'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['cor'] > $cor){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['cor'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor[0]['conformacao'] > $conformacao){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor[0]['conformacao'], 2, ',', '.')?></span>
    </td>
    </tr>
  </table>
