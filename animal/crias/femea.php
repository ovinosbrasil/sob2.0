<script type="text/javascript">
function mudar(tipo){
  if(tipo){
    window.location.href = "geral.php?pg=animal&id_animal="+<?=$id_animal?>+"&aba=crias&t=g";
  }else{
    window.location.href = "geral.php?pg=animal&id_animal="+<?=$id_animal?>+"&aba=crias";
  }
}

function atualizar_tabela(x){
  window.location.href = "geral.php?pg=animal&id_animal=<?=$id_animal?>&aba=crias&filtro="+x;
}
</script>

<?
$cria = DBRead('animais', "WHERE mae = '$id_animal' AND  terceiro_mae = '0' ORDER BY data_de_nascimento asc");
if(!empty($cria[0]['id'])){


$t = $_GET['t'] ?? '';
if(!$t){
?>
<div id="lista_crias">
<div class="cabecalho-crias"><h3>Lista de crias (Monta natural e Inseminação artificial)</h3><button type="button" class="btn btn-primary" onclick="mudar(1)">Gráficos de vendas</button></div>
<div class="table-responsive"><table class="table table-bordered table-striped">
  <thead><tr>
    <th style="width:1%;">Nº</th>
    <th>Animal</th>
    <th>Nascimento</th>
    <th>Intervalo</th>
    <th>Sexo</th>
    <th>Peso 90 dias</th>
    <th>GMD Apartação</th>
    <th>Morte Apartação</th>
    <th onclick="atualizar_tabela(1)" style="cursor:pointer;">Tipo</th>
    <th onclick="atualizar_tabela(2)" style="cursor:pointer;">Status</th>
  </tr></thead><tbody>

    <?
    $x=0;
    $filtro = (int)($_GET['filtro'] ?? 0);
    if (!in_array($filtro, array(0, 1, 2), true)) $filtro = 0;
    if($filtro == 0){ $cria = DBRead('animais', "WHERE mae = '$id_animal' AND tipo_reproducao != 'Embrionagem' AND terceiro_mae = '0' ORDER BY data_de_nascimento asc"); }
    if($filtro == 1){ $cria = DBRead('animais', "WHERE mae = '$id_animal' AND tipo_reproducao != 'Embrionagem' AND terceiro_mae = '0' ORDER BY tipo desc"); }
    if($filtro == 2){ $cria = DBRead('animais', "WHERE mae = '$id_animal' AND tipo_reproducao != 'Embrionagem' AND terceiro_mae = '0' ORDER BY status asc"); }
    $paginacaoCrias = paginacaoCrias(count($cria ?: array()), array('pg' => 'animal', 'id_animal' => $id_animal, 'aba' => 'crias', 'filtro' => $filtro));
    $x = $paginacaoCrias['inicio'];
    $data_antiga = $x > 0 ? date('d/m/Y', strtotime($cria[$x - 1]['data_de_nascimento'])) : '';
    $cria = array_slice($cria ?: array(), $x, $paginacaoCrias['por_pagina']);
    foreach ($cria as $crias) {
      $x++;
      $id_cria = $crias['id'];
      $cria_ = DBRead('animais', "WHERE id = '$id_cria'");
      $data = $cria_[0]['data_de_nascimento'];
      $data_atual = $data;
      $data = '0';
      $data['0'] = $data_atual['8'];
      $data['1'] = $data_atual['9'];
      $data['2'] = "/";
      $data['3'] = $data_atual['5'];
      $data['4'] = $data_atual['6'];
      $data['5'] = "/";
      $data['6'] = $data_atual['0'];
      $data['7'] = $data_atual['1'];
      $data['8'] = $data_atual['2'];
      $data['9'] = $data_atual['3'];
      $data_nascimento = $data;

      //Intervalo de parto
      if($data_antiga == ''){ $intervalo = '';}
      if($data_antiga == $data_nascimento){ $intervalo = '';}
      if(($data_antiga != $data_nascimento) && ($data_antiga != '')){
        $time_inicial = geraTimestamp($data_antiga);
        $time_final = geraTimestamp($data_nascimento);
        $diferenca = $time_final - $time_inicial;
        $intervalo = (int)floor( $diferenca / (60 * 60 * 24))/30;
      }
      $data_antiga = $data_nascimento;
      //FIM INTERVALO

      //GMD
      $data = $cria_[0]['data2'];
      $data_atual = $data;
      $data = '0';
      $data['0'] = $data_atual['8'];
      $data['1'] = $data_atual['9'];
      $data['2'] = "/";
      $data['3'] = $data_atual['5'];
      $data['4'] = $data_atual['6'];
      $data['5'] = "/";
      $data['6'] = $data_atual['0'];
      $data['7'] = $data_atual['1'];
      $data['8'] = $data_atual['2'];
      $data['9'] = $data_atual['3'];
      $data_apartacao = $data;
      $time_inicial = geraTimestamp($data_nascimento);
      $time_final = geraTimestamp($data_apartacao);
      $diferenca = $time_final - $time_inicial;
      $dias_apartacao = (int)floor( $diferenca / (60 * 60 * 24));
      //FIM GMD

      //MORTE APARTACAO
      $dias_saida = 0;
      if($cria_[0]['data_de_saida'] > $cria_[0]['data_de_nascimento']){
        $data = $cria_[0]['data_de_saida'];
        $data_atual = $data;
        $data = '0';
        $data['0'] = $data_atual['8'];
        $data['1'] = $data_atual['9'];
        $data['2'] = "/";
        $data['3'] = $data_atual['5'];
        $data['4'] = $data_atual['6'];
        $data['5'] = "/";
        $data['6'] = $data_atual['0'];
        $data['7'] = $data_atual['1'];
        $data['8'] = $data_atual['2'];
        $data['9'] = $data_atual['3'];
        $data_saida = $data;
        $time_inicial = geraTimestamp($data_nascimento);
        $time_final = geraTimestamp($data_saida);
        $diferenca = $time_final - $time_inicial;
        $dias_saida = (int)floor( $diferenca / (60 * 60 * 24));
      }
      //

    ?>
    <?
    if($cria_[0]['status'] == '0'){ ?> <tr><? }
    if($cria_[0]['status'] == '1'){ ?> <tr style="color:red;"><? }
    if($cria_[0]['status'] == '2'){ ?> <tr style="color:green;"><? } ?>
    <td><?=$x?></td>
    <td style="cursor:pointer;" onclick="abrir_animal(<?=$cria_[0]['id']?>)"><?=$cria_[0]['nome']?></td>
    <td><?=$data_nascimento?></td>
    <td><? if($intervalo > 1){ echo number_format($intervalo, 1, ',', '.'), " Meses"; }?></td>
    <td><?=$cria_[0]['sexo']?></td>
    <td><?=number_format(($cria_[0]['peso2']/$dias_apartacao)*90, 2, ',', '.'), " kg";?></td>
    <td><?=number_format(($cria_[0]['peso2']/$dias_apartacao)*1000, 2, ',', '.'), " g";?></td>
    <td><? if($dias_saida <= 90 && $dias_saida > 0){ echo "Sim"; }else{ echo "Não"; }?></td>
    <td><?=$cria_[0]['tipo']?></td>
    <td>
      <?
      if($cria_[0]['status'] == '0'){ ?> <span style="color:#37abc0;">Rebanho<? }
      if($cria_[0]['status'] == '1'){ ?> <span style="color:red;">Morto<? }
      if($cria_[0]['status'] == '2'){ ?> <span style="color:green;">Vendido<? }
      if($cria_[0]['status'] == '3'){ ?> <span style="color:red;">Empréstimo<? }
      if($cria_[0]['status'] == '4'){ ?> <span style="color:red;">Doação<? }
      if($cria_[0]['status'] == '5'){ ?> <span style="color:red;">Abate<? } ?>
    </td>
  </tr>
  <? } ?>
  </tbody></table></div>
  <?php renderPaginacaoCrias($paginacaoCrias); ?>


  <h4 class="box-title">Lista de crias (Transplante de embriões)</h4>
  <div class="table-responsive"><table class="table table-bordered table-striped">
  <thead><tr>
      <th style="width:1%;">Nº</th>
      <th>Animal</th>
      <th>Nascimento</th>
      <th>Sexo</th>
      <th>Peso 90 dias</th>
      <th>GMD Apartação</th>
      <th>Morte Apartação</th>
      <th onclick="atualizar_tabela(1)" style="cursor:pointer;">Tipo</th>
      <th onclick="atualizar_tabela(2)" style="cursor:pointer;">Status</th>
    </tr></thead><tbody>

      <?
      $x=0;
      if($filtro == 0){ $cria = DBRead('animais', "WHERE mae = '$id_animal' AND (tipo_reproducao = 'Embrionagem') AND terceiro_mae = '0' ORDER BY data_de_nascimento asc"); }
      if($filtro == 1){ $cria = DBRead('animais', "WHERE mae = '$id_animal' AND (tipo_reproducao = 'Embrionagem') AND terceiro_mae = '0' ORDER BY tipo desc"); }
      if($filtro == 2){ $cria = DBRead('animais', "WHERE mae = '$id_animal' AND (tipo_reproducao = 'Embrionagem') AND terceiro_mae = '0' ORDER BY status asc"); }

      $paginacaoEmbrioes = paginacaoCrias(count($cria ?: array()), array('pg' => 'animal', 'id_animal' => $id_animal, 'aba' => 'crias', 'filtro' => $filtro), 'pagina_embrioes');
      $x = $paginacaoEmbrioes['inicio'];
      $cria = array_slice($cria ?: array(), $x, $paginacaoEmbrioes['por_pagina']);
      $data_antiga = '';
      foreach ($cria as $crias) {
        $x++;
        $id_cria = $crias['id'];
        $cria_ = DBRead('animais', "WHERE id = '$id_cria'");
        $data = $cria_[0]['data_de_nascimento'];
        $data_atual = $data;
        $data = '0';
        $data['0'] = $data_atual['8'];
        $data['1'] = $data_atual['9'];
        $data['2'] = "/";
        $data['3'] = $data_atual['5'];
        $data['4'] = $data_atual['6'];
        $data['5'] = "/";
        $data['6'] = $data_atual['0'];
        $data['7'] = $data_atual['1'];
        $data['8'] = $data_atual['2'];
        $data['9'] = $data_atual['3'];
        $data_nascimento = $data;


        //GMD
        $data = $cria_[0]['data2'];
        $data_atual = $data;
        $data = '0';
        $data['0'] = $data_atual['8'];
        $data['1'] = $data_atual['9'];
        $data['2'] = "/";
        $data['3'] = $data_atual['5'];
        $data['4'] = $data_atual['6'];
        $data['5'] = "/";
        $data['6'] = $data_atual['0'];
        $data['7'] = $data_atual['1'];
        $data['8'] = $data_atual['2'];
        $data['9'] = $data_atual['3'];
        $data_apartacao = $data;
        $time_inicial = geraTimestamp($data_nascimento);
        $time_final = geraTimestamp($data_apartacao);
        $diferenca = $time_final - $time_inicial;
        $dias_apartacao = (int)floor( $diferenca / (60 * 60 * 24));
        //FIM GMD

        //MORTE APARTACAO
        $dias_saida = 0;
        if($cria_[0]['data_de_saida'] > $cria_[0]['data_de_nascimento']){
          $data = $cria_[0]['data_de_saida'];
          $data_atual = $data;
          $data = '0';
          $data['0'] = $data_atual['8'];
          $data['1'] = $data_atual['9'];
          $data['2'] = "/";
          $data['3'] = $data_atual['5'];
          $data['4'] = $data_atual['6'];
          $data['5'] = "/";
          $data['6'] = $data_atual['0'];
          $data['7'] = $data_atual['1'];
          $data['8'] = $data_atual['2'];
          $data['9'] = $data_atual['3'];
          $data_saida = $data;
          $time_inicial = geraTimestamp($data_nascimento);
          $time_final = geraTimestamp($data_saida);
          $diferenca = $time_final - $time_inicial;
          $dias_saida = (int)floor( $diferenca / (60 * 60 * 24));
        }
        //

      ?>
      <?
      if($cria_[0]['status'] == '0'){ ?> <tr><? }
      if($cria_[0]['status'] == '1'){ ?> <tr style="color:red;"><? }
      if($cria_[0]['status'] == '2'){ ?> <tr style="color:green;"><? }
      if($cria_[0]['status'] == '3'){ ?> <tr style="color:red;"><? }
      if($cria_[0]['status'] == '4'){ ?> <tr style="color:red;"><? }
      if($cria_[0]['status'] == '5'){ ?> <tr style="color:red;"><? } ?>
      <td><?=$x?></td>
      <td style="cursor:pointer;" onclick="abrir_animal(<?=$cria_[0]['id']?>)"><?=$cria_[0]['nome']?></td>
      <td><?=$data_nascimento?></td>
      <td><?=$cria_[0]['sexo']?></td>
      <td><?=number_format(($cria_[0]['peso2']/$dias_apartacao)*90, 2, ',', '.'), " kg";?></td>
      <td><?=number_format(($cria_[0]['peso2']/$dias_apartacao)*1000, 2, ',', '.'), " g";?></td>
      <td><? if($dias_saida <= 90 && $dias_saida > 0){ echo "Sim"; }else{ echo "Não"; }?></td>
      <td><?=$cria_[0]['tipo']?></td>
      <td>
        <?if($cria_[0]['status'] == '0'){ ?> <span style="color:#37abc0;">Rebanho<? }
        if($cria_[0]['status'] == '1'){ ?> <span style="color:red;">Morto<? }
        if($cria_[0]['status'] == '2'){ ?> <span style="color:green;">Vendido<? }
        if($cria_[0]['status'] == '3'){ ?> <span style="color:red;">Empréstimo<? }
        if($cria_[0]['status'] == '4'){ ?> <span style="color:red;">Doação<? }
        if($cria_[0]['status'] == '5'){ ?> <span style="color:red;">Abate<? } ?>
      </td>
    </tr>
    <? } ?>
  </tbody></table></div>
  <?php renderPaginacaoCrias($paginacaoEmbrioes); ?>
</div>
<? }

if($t == 'g'){

//CALCULO DE VENDAS DO REBANHO
$venda = DBRead('vendas', "WHERE data > '2011-01-01'");
foreach ($venda as $vendas) {
  $id_animal_venda = $vendas['id_animal'];
  $animal_venda = DBRead('animais', "WHERE id = '$id_animal_venda'");
  if($animal_venda[0]['sexo'] == 'Macho'){
  	$media_macho = $media_macho+$vendas['preco_de_venda'];
  	$qtd_macho++;
  }else{
  	$media_femea = $media_femea+$vendas['preco_de_venda'];
  	$qtd_femea++;
  }
  $soma = $vendas['preco_de_venda'] + $soma;
  $qtd++;
}
$media_macho = $media_macho/$qtd_macho;
$media_femea = $media_femea/$qtd_femea;
$valor = $soma/$qtd;
//FIM CALCULO REBANHO

//CALCULO VENDAS DO ANIMAL
$animal_venda = DBRead('animais', "WHERE mae = '$id_animal' AND terceiro_mae = '0' AND status = '2'");
foreach ($animal_venda as $animal_venda_){
	$id_animal_venda = $animal_venda_['id'];
  $vendas = DBRead('vendas', "WHERE id_animal = '$id_animal_venda' AND data > '2011-01-01'");
	if($vendas[0]['id'] > 0){
	   if($animal_venda_['sexo'] == 'Fêmea'){
       $femea_media = $femea_media + $vendas[0]['preco_de_venda']; $qtd_femea2++;
     }else{
       $macho_media = $macho_media + $vendas[0]['preco_de_venda']; $qtd_macho2++;
    }
	 $total = $total + $vendas[0]['preco_de_venda'];
   $qtd_total++;
  }
}


$femea_media = $femea_media/$qtd_femea2;
$macho_media = $macho_media/$qtd_macho2;
$total = $total/$qtd_total;

if($media_macho == ''){ $media_macho = 0; }
if($media_femea == ''){ $media_femea = 0; }
if($valor == ''){ $valor = 0; }
if($macho_media == ''){ $macho_media = 0; }
if($femea_media == ''){ $femea_media = 0; }
if($total == ''){ $total = 0; }
$somatotal = number_format($total*$qtd_total,2,",",",");
$media_macho = number_format($media_macho,2,",",".");
$macho_media = number_format($macho_media,2,",",".");
$media_femea = number_format($media_femea,2,",",".");
$femea_media = number_format($femea_media,2,",",".");
$valor = number_format($valor,2,",",".");
$total = number_format($total,2,",",".");
?>
  <div class="col-md-6">
        <div class="box-header with-border">
          <h3 class="box-title">Avalição x Avaliação do rebanho</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body chart-responsive">
          <div class="chart" id="bar-chart2" style="height: 550px;"></div>
        </div>
        <!-- /.box-body -->
  </div>

  <div class="col-md-4">
    <div class="form-group" style="margin-top:3%;">
      <span style="color:#00a65a;">Média de machos do animal: R$ <?=$macho_media?><br/></span>
      <span style="color:#4c8eac;">Média de machos do rebanho: R$ <?=$media_macho?></span>

    </div>

    <div class="form-group" style="margin-top:3%;">
      <span style="color:#00a65a;">Média de fêmeas do animal: R$ <?=$femea_media?><br/></span>
      <span style="color:#4c8eac;">Média de fêmeas do rebanho: R$ <?=$media_femea?></span>

    </div>

    <div class="form-group" style="margin-top:3%;">
      <span style="color:#00a65a;">Média geral do animal: R$ <?=$total?><br/></span>
      <span style="color:#4c8eac;">Média geral do rebanho: R$ <?=$valor?></span>

    </div>

    <div class="form-group" style="margin-top:3%;">
      <span style="color:#00a65a;">Valor Total: R$ <?=$somatotal?><br/></span>
      <span style="color:#00a65a;">Total de vendas: <?=$qtd_total?></span>

    </div>

    <div class="form-group" style="margin-top:3%; color:red;">
      Atenção: Médias calculadas com as vendas a partir de 01/01/2011
    </div>
  </div>

  <div class="col-md-2">
  <button type="button" class="btn btn-primary" style="margin-top:0%; width:100%;" onclick="mudar(0)">Lista das crias</button>
  </div>
<? }
}else{
  Echo "Animal não tem crias cadastradas no sistema";
} ?>
