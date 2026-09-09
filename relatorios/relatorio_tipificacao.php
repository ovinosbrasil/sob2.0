<?  $filtro = $_GET['filtro']; ?>
<script type="text/javascript">
function atualizar(filtro){
    window.location.href = "geral.php?pg=relatorio_tipificacao&filtro="+filtro;
}
function atualizar2(tipo){
    window.location.href = "geral.php?pg=relatorio_tipificacao&filtro=<?=$filtro?>&tipo="+tipo;
}
</script>

<section class="content-header">
  <h1>
    Relatório de tipificação das crias
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatórios</a></li>
    <li><a href="#">Tipificação</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
				<div class="box box-success">
          <form method="post" action="geral.php?pg=relatorio_mortes" onsubmit="return validar_montar()">
          <!-- /.box-header -->
          <div class="box-body">

        <div class="col-md-4" style="margin-left:-12px;">
          <label for="exampleInputPassword1">Filtro</label>
            <div class="form-group">
              <select  class="form-control select" onchange="atualizar(this.value)">
                <?
                if($filtro){ ?> <option value="<?=$filtro?>"><?=$filtro?></option> <? }else{ ?> <option value="">Selecionar</option><? } ?>
                <option value=""></option>
                <option value="Reprodutores">Reprodutores</option>
                <option value="Matrizes">Matrizes</option>
              </select>
            </div>
        </div>


<? if($filtro == 'Matrizes'){ ?>
        <div class="form-group" style="margin-top:2%; color:red;">
          Obs.: Lista de matrizes com 3 ou mais crias avaliadas.
        </div>

<table class="table table-bordered" id="tabela_padrao">
  <tr>
    <th>Matriz</th>
    <th onclick="atualizar2(1)" style="cursor:pointer;">Crias Avaliadas</th>
    <th onclick="atualizar2(2)" style="cursor:pointer;">Pesagem</th>
    <th onclick="atualizar2(3)" style="cursor:pointer;">Cabeça</th>
    <th onclick="atualizar2(4)" style="cursor:pointer;">Pescoço</th>
    <th onclick="atualizar2(5)" style="cursor:pointer;">Quarto anterior</th>
    <th onclick="atualizar2(6)" style="cursor:pointer;">Barril</th>
    <th onclick="atualizar2(7)" style="cursor:pointer;">Quarto posterior</th>
    <th onclick="atualizar2(8)" style="cursor:pointer;">Comprimento</th>
    <th onclick="atualizar2(9)" style="cursor:pointer;">Orgão sexual</th>
    <th onclick="atualizar2(10)" style="cursor:pointer;">Gordura</th>
    <th onclick="atualizar2(11)" style="cursor:pointer;">Cobertura</th>
    <th onclick="atualizar2(12)" style="cursor:pointer;">Cor</th>
    <th onclick="atualizar2(13)" style="cursor:pointer;">Conformação</th>
  </tr>

<?
$media = DBRead('matriz');
foreach ($media as $media_){
  if($media_['cabeca'] > 0){
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


$tipo = $_GET['tipo'];
if(!$tipo){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= 3"); }
if($tipo == 1){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY qtd_avaliadas desc"); }
if($tipo == 2){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY pesagem desc"); }
if($tipo == 3){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY cabeca desc"); }
if($tipo == 4){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY pescoco desc"); }
if($tipo == 5){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY quarto_anterior desc"); }
if($tipo == 6){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY barril desc"); }
if($tipo == 7){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY quarto_posterior desc"); }
if($tipo == 8){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY comprimento desc"); }
if($tipo == 9){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY orgao desc"); }
if($tipo == 10){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY gordura desc"); }
if($tipo == 11){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY cobertura desc"); }
if($tipo == 12){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY cor desc"); }
if($tipo == 13){ $matriz_ = DBRead('matriz', "WHERE qtd_avaliadas >= '3' ORDER BY conformacao desc"); }

foreach ($matriz_ as $matriz) {
$id_matriz = $matriz['id_femea'];
$femea = DBRead('animais', "WHERE id = '$id_matriz'");

?>

  <tr>
    <? if($femea[0]['status'] == 0){ ?> <td> <?}else{?> <td style="color:red;"> <? } ?><?=$femea[0]['nome']?></td>
    <td ><?=$matriz['qtd_avaliadas']?></td>
    <td>
      <? if($matriz['pesagem'] > $pesagem){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['pesagem'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['cabeca'] > $cabeca){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['cabeca'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['pescoco'] > $pescoco){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['pescoco'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['quarto_anterior'] > $quarto_anterior){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['quarto_anterior'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['barril'] > $barril){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['barril'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['quarto_posterior'] > $quarto_posterior){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['quarto_posterior'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['comprimento'] > $comprimento){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['comprimento'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['orgao'] > $orgao){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['orgao'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['gordura'] > $gordura){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['gordura'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['cobertura'] > $cobertura){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['cobertura'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['cor'] > $cor){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['cor'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($matriz['conformacao'] > $conformacao){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($matriz['conformacao'], 2, ',', '.')?></span>
    </td>
    </tr>
  <? } ?>
  </table>
<? } ?>


<? if($filtro == 'Reprodutores'){ ?>
        <div class="form-group" style="margin-top:2%; color:red;">
          Obs.: Lista de reprodutores com 3 ou mais crias avaliadas.
        </div>

<table class="table table-bordered" id="tabela_padrao">
  <tr>
    <th>Reprodutor</th>
    <th onclick="atualizar2(1)" style="cursor:pointer;">Crias Avaliadas</th>
    <th onclick="atualizar2(2)" style="cursor:pointer;">Pesagem</th>
    <th onclick="atualizar2(3)" style="cursor:pointer;">Cabeça</th>
    <th onclick="atualizar2(4)" style="cursor:pointer;">Pescoço</th>
    <th onclick="atualizar2(5)" style="cursor:pointer;">Quarto anterior</th>
    <th onclick="atualizar2(6)" style="cursor:pointer;">Barril</th>
    <th onclick="atualizar2(7)" style="cursor:pointer;">Quarto posterior</th>
    <th onclick="atualizar2(8)" style="cursor:pointer;">Comprimento</th>
    <th onclick="atualizar2(9)" style="cursor:pointer;">Orgão sexual</th>
    <th onclick="atualizar2(10)" style="cursor:pointer;">Gordura</th>
    <th onclick="atualizar2(11)" style="cursor:pointer;">Cobertura</th>
    <th onclick="atualizar2(12)" style="cursor:pointer;">Cor</th>
    <th onclick="atualizar2(13)" style="cursor:pointer;">Conformação</th>
  </tr>

<?
$media = DBRead('reprodutor');
foreach ($media as $media_){
  if($media_['cabeca'] > 0){
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


$tipo = $_GET['tipo'];
if(!$tipo){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= 3"); }
if($tipo == 1){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY qtd_avaliadas desc"); }
if($tipo == 2){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY pesagem desc"); }
if($tipo == 3){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY cabeca desc"); }
if($tipo == 4){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY pescoco desc"); }
if($tipo == 5){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY quarto_anterior desc"); }
if($tipo == 6){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY barril desc"); }
if($tipo == 7){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY quarto_posterior desc"); }
if($tipo == 8){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY comprimento desc"); }
if($tipo == 9){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY orgao desc"); }
if($tipo == 10){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY gordura desc"); }
if($tipo == 11){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY cobertura desc"); }
if($tipo == 12){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY cor desc"); }
if($tipo == 13){ $reprodutor_ = DBRead('reprodutor', "WHERE qtd_avaliadas >= '3' ORDER BY conformacao desc"); }

foreach ($reprodutor_ as $reprodutor) {
$id_reprodutor = $reprodutor['id_macho'];
$macho = DBRead('animais', "WHERE id = '$id_reprodutor'");

?>

  <tr>
    <? if($macho[0]['status'] == 0){ ?> <td> <?}else{?> <td style="color:red;"> <? } ?><?=$macho[0]['nome']?></td>
    <td ><?=$reprodutor['qtd_avaliadas']?></td>
    <td>
      <? if($reprodutor['pesagem'] > $pesagem){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['pesagem'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['cabeca'] > $cabeca){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['cabeca'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['pescoco'] > $pescoco){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['pescoco'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['quarto_anterior'] > $quarto_anterior){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['quarto_anterior'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['barril'] > $barril){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['barril'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['quarto_posterior'] > $quarto_posterior){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['quarto_posterior'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['comprimento'] > $comprimento){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['comprimento'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['orgao'] > $orgao){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['orgao'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['gordura'] > $gordura){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['gordura'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['cobertura'] > $cobertura){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['cobertura'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['cor'] > $cor){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['cor'], 2, ',', '.')?></span>
    </td>
    <td>
      <? if($reprodutor['conformacao'] > $conformacao){?> <span style="color:green;"> <? }else{ ?> <span style="color:red;"> <? } ?>
      <?=number_format($reprodutor['conformacao'], 2, ',', '.')?></span>
    </td>
    </tr>
  <? } ?>
  </table>
<? } ?>

</div>
<!-- /.col -->
</div>

</div>
</div>
</section>
<!-- /.content -->
