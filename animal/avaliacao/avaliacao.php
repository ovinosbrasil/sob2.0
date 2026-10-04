<script type="text/javascript">

function ativar_apartacao(){
  saida = 0;
	if(!document.getElementById("data_nascimento").value){
    document.getElementById("data_nascimento").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_nascimento").style.border = "1px solid green";}

  if(!(document.getElementById("data_apartacao") || document.getElementById("data_adulto")).value){
    (document.getElementById("data_apartacao") || document.getElementById("data_adulto")).style.border = "1px solid red";
    saida = 1;
  }else{(document.getElementById("data_apartacao") || document.getElementById("data_adulto")).style.border = "1px solid green";}

  if(!(Number(document.getElementById("peso_inicial").value) > 0)){
    document.getElementById("peso_inicial").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("peso_inicial").style.border = "1px solid green";}

  if(!(Number((document.getElementById("peso_apartacao") || document.getElementById("peso_adulto")).value) > 0)){
    (document.getElementById("peso_apartacao") || document.getElementById("peso_adulto")).style.border = "1px solid red";
    saida = 1;
  }else{(document.getElementById("peso_apartacao") || document.getElementById("peso_adulto")).style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}


function atualizar_avaliacao(x){
    window.location.href = "geral.php?pg=animal&id_animal="+<?=$id_animal?>+"&aba=avaliacao&avaliacao="+x;
}

function preencher_automatico(valor) {
  if (!['5', '4', '3', '2'].includes(String(valor))) return;
  document.querySelectorAll('#avaliacoes-animal .avaliacao-nota').forEach(function (campo) {
    campo.value = valor;
    campo.setAttribute('aria-invalid', 'false');
    document.getElementById(campo.dataset.titulo).style.color = '';
  });
}
</script>

<? $avaliacao = $_GET['avaliacao'] ?? ''; ?>

<style>
  #avaliacoes-animal > .col-md-12 > .box-body { padding:0; }
  #avaliacoes-animal .avaliacao-controles { display:flex; flex-wrap:wrap; gap:16px; }
  #avaliacoes-animal .avaliacao-controles .form-group { width:240px; max-width:100%; }
  #avaliacoes-animal .table th, #avaliacoes-animal .table td { vertical-align:middle; }
  #avaliacoes-animal .table tfoot { background:#f7f7f7; }

  #avaliacoes-animal .avaliacao-gmd { padding:0 15px; }
  #avaliacoes-animal .gmd-layout { display:flex; align-items:flex-start; gap:24px; }
  #avaliacoes-animal .gmd-campos { width:240px; flex-shrink:0; }
  #avaliacoes-animal .gmd-informacoes { flex:1; min-width:0; }
  #avaliacoes-animal .gmd-campos small { display:block; margin-top:4px; color:#748292; }
  #avaliacoes-animal .gmd-campos .form-group:last-child { margin-bottom:0 !important; }
  #avaliacoes-animal .gmd-resumo { display:flex; flex:1; flex-wrap:wrap; gap:48px; padding:16px; margin-top:12px; background:white; border:1px solid #ddd; border-radius:8px; }
  #avaliacoes-animal .gmd-resumo span { display:block; color:#748292; font-size:12px; margin-bottom:4px; }
  #avaliacoes-animal .gmd-resumo strong { display:block; }
  #avaliacoes-animal #gmd_resultado { color:#487a3d; }
  #avaliacoes-animal .gmd-dicas { margin-top:16px; padding:12px; background:#fff8e1; border-left:4px solid #ffca45; }
  #avaliacoes-animal .gmd-dicas p { margin:0 0 8px; }
  #avaliacoes-animal .gmd-dicas .table { margin-bottom:0; background:transparent; }
  #avaliacoes-animal .avaliacao-grade { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:16px; }
  #avaliacoes-animal .avaliacao-grade .box { margin-bottom:0; height:100%; }
  #avaliacoes-animal .avaliacao-grade .avaliacao-nota { width:100%; }
  #avaliacoes-animal .avaliacao-grade { margin-bottom:16px; }
  @media (max-width:1199px) {
    #avaliacoes-animal .avaliacao-grade { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  #avaliacoes-animal .avaliacao-item .box, #avaliacoes-animal .avaliacao-gmd .box {
    background:#f6f8fb; border:1px solid #e3eaf5; border-radius:8px; box-shadow:none;
    padding:16px; margin-bottom:16px;
  }
  #avaliacoes-animal .avaliacao-item .box-header, #avaliacoes-animal .avaliacao-gmd .box-header {
    padding:0 0 12px; border-bottom:1px solid #ddd;
  }
  #avaliacoes-animal .avaliacao-item .box-title, #avaliacoes-animal .avaliacao-gmd .box-title {
    font-size:14px; font-weight:600; color:#263746;
  }
  #avaliacoes-animal .avaliacao-item .box-body, #avaliacoes-animal .avaliacao-gmd .box-body { padding:12px 0 0; }
  #avaliacoes-animal .form-control { border:1px solid #e3eaf5; border-radius:4px; height:40px; box-shadow:none; }
  #avaliacoes-animal .form-control:focus { border-color:#78aac1; }
  #avaliacoes-animal .form-control[aria-invalid="true"] { border-color:#dd4b39; }
  #avaliacoes-animal .avaliacao-criterio { display:flex; align-items:flex-start; gap:24px; }
  #avaliacoes-animal .avaliacao-nota { width:210px; max-width:100%; flex-shrink:0; }
  #avaliacoes-animal .avaliacao-gmd form > .col-md-4 { width:240px; padding:0; }
  #avaliacoes-animal .avaliacao-gmd form > .col-md-4 > .col-md-6,
  #avaliacoes-animal .avaliacao-gmd form > .col-md-4 > .col-md-12 { width:100%; padding:0; margin-top:0 !important; }
  #avaliacoes-animal .avaliacao-gmd .form-group { margin:0 0 16px !important; }
  #avaliacoes-animal .avaliacao-gmd label { font-weight:400; color:#748292; }
  #avaliacoes-animal .avaliacao-gmd .box-body > .col-md-6 { width:100%; padding:0; clear:both; }
  #avaliacoes-animal .avaliacao-gmd .box-body > .col-md-6 > .col-md-12 { padding:0; }
  #avaliacoes-animal .avaliacao-gmd .box-body > .col-md-6 > .col-md-12:not(:last-child) {
    margin:16px 0; padding:12px; background:#fff8e1; border-left:4px solid #ffca45;
  }
  @media (max-width:600px) {
    #avaliacoes-animal .gmd-layout { flex-direction:column; }
    #avaliacoes-animal .gmd-campos, #avaliacoes-animal .gmd-informacoes, #avaliacoes-animal .gmd-resumo { width:100%; }
    #avaliacoes-animal .gmd-resumo { gap:24px; margin-top:0; }
    #avaliacoes-animal .avaliacao-grade { grid-template-columns:1fr; }
    #avaliacoes-animal .avaliacao-nota, #avaliacoes-animal .avaliacao-gmd form > .col-md-4 { width:100%; }
  }
</style>
<div class="row" id="avaliacoes-animal">
<div class="col-md-12 avaliacao-controles">
<div class="form-group">
      <label for="avalicao">Selecionar avaliação</label>
      <select class="form-control select" id="avalicao" name="avalicao" onchange="atualizar_avaliacao(this.value)">
        <option value="" <?=!$avaliacao ? 'selected' : ''?>>Geral</option>
        <option value="1" <?=$avaliacao == 1 ? 'selected' : ''?>>1ª Avaliação</option>
        <option value="2" <?=$avaliacao == 2 ? 'selected' : ''?>>2ª Avaliação</option>
      </select>
</div>
<? if ($avaliacao == 1 || $avaliacao == 2) { ?>
<div class="form-group">
  <label for="automatico">Preenchimento automático</label>
  <select class="form-control select" id="automatico" name="automatico" onchange="preencher_automatico(this.value)">
    <option value="">Selecionar tipo</option>
    <option value="5">Tipo 5</option>
    <option value="4">Tipo 4</option>
    <option value="3">Tipo 3</option>
    <option value="2">Tipo 2</option>
  </select>
</div>
<? } ?>
</div>

<?
if(!$avaliacao){

$ava1 = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = 1");
$ava2 = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = 2");

if (!empty($ava1) || !empty($ava2)) {

$avaliacaoVazia = array_fill_keys(['barril', 'cabeca', 'cobertura', 'comprimento', 'conformacao', 'cor', 'distribuicao', 'id', 'orgao', 'pescoco', 'quarto_anterior', 'quarto_posterior', 'tamanho', 'tipo'], null);
$ava1 = [array_replace($avaliacaoVazia, $ava1[0] ?? [])];
$ava2 = [array_replace($avaliacaoVazia, $ava2[0] ?? [])];
$ava1_ = $ava1[0]['tipo'] ?? 0;
$ava2_ = $ava2[0]['tipo'] ?? 0;

$total=$qtd=0;
$media1 = DBRead('avaliacao', "WHERE avaliacao = '1'");
foreach (($media1 ?: []) as $media1_) {
  $total = $total+$media1_['tipo'];
  $qtd++;
}
$media1_ = $qtd > 0 ? number_format($total/$qtd,2,".","") : 0;


$total=$qtd=0;
$media2 = DBRead('avaliacao', "WHERE avaliacao = '2'");
foreach (($media2 ?: []) as $media2_) {
  $total = $total+$media2_['tipo'];
  $qtd++;
}
$media2_ = $qtd > 0 ? number_format($total/$qtd,2,".","") : 0;

?>

<div class="col-md-12">
  <!-- general form elements -->
  <div class="box-body">
    <div class="col-md-6">
      <h3 style="font-size:16px; margin-top:0;">Evolução das avaliações</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr>
            <th>Critério</th>
            <th>1ª Avaliação</th>
            <th>2ª Avaliação</th>
            <th>Evolução</th>
          </tr></thead>
          <tbody>
          <tr>
            <td>Pesagem</td>
            <td><?=$ava1[0]['tamanho']?></td>
            <td><?=$ava2[0]['tamanho']?></td>
            <td>
              <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] > $ava2[0]['tamanho'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] < $ava2[0]['tamanho'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] == $ava2[0]['tamanho'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Cabeça</td>
            <td><?=$ava1[0]['cabeca']?></td>
            <td><?=$ava2[0]['cabeca']?></td>
            <td>
              <? if(($ava1[0]['cabeca']) && ($ava2[0]['cabeca']) && ($ava1[0]['cabeca'] > $ava2[0]['cabeca'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['cabeca']) && ($ava2[0]['cabeca']) && ($ava1[0]['cabeca'] < $ava2[0]['cabeca'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['cabeca']) && ($ava2[0]['cabeca']) && ($ava1[0]['cabeca'] == $ava2[0]['cabeca'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Pescoço</td>
            <td><?=$ava1[0]['pescoco']?></td>
            <td><?=$ava2[0]['pescoco']?></td>
            <td>
              <? if(($ava1[0]['pescoco']) && ($ava2[0]['pescoco']) && ($ava1[0]['pescoco'] > $ava2[0]['pescoco'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['pescoco']) && ($ava2[0]['pescoco']) && ($ava1[0]['pescoco'] < $ava2[0]['pescoco'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['pescoco']) && ($ava2[0]['pescoco']) && ($ava1[0]['pescoco'] == $ava2[0]['pescoco'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Quarto Anterior</td>
            <td><?=$ava1[0]['quarto_anterior']?></td>
            <td><?=$ava2[0]['quarto_anterior']?></td>
            <td>
              <? if(($ava1[0]['quarto_anterior']) && ($ava2[0]['quarto_anterior']) && ($ava1[0]['quarto_anterior'] > $ava2[0]['quarto_anterior'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['quarto_anterior']) && ($ava2[0]['quarto_anterior']) && ($ava1[0]['quarto_anterior'] < $ava2[0]['quarto_anterior'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['quarto_anterior']) && ($ava2[0]['quarto_anterior']) && ($ava1[0]['quarto_anterior'] == $ava2[0]['quarto_anterior'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Barril</td>
            <td><?=$ava1[0]['barril']?></td>
            <td><?=$ava2[0]['barril']?></td>
            <td>
              <? if(($ava1[0]['barril']) && ($ava2[0]['barril']) && ($ava1[0]['barril'] > $ava2[0]['barril'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['barril']) && ($ava2[0]['barril']) && ($ava1[0]['barril'] < $ava2[0]['barril'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['barril']) && ($ava2[0]['barril']) && ($ava1[0]['barril'] == $ava2[0]['barril'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Quarto Posterior</td>
            <td><?=$ava1[0]['quarto_posterior']?></td>
            <td><?=$ava2[0]['quarto_posterior']?></td>
            <td>
              <? if(($ava1[0]['quarto_posterior']) && ($ava2[0]['quarto_posterior']) && ($ava1[0]['quarto_posterior'] > $ava2[0]['quarto_posterior'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['quarto_posterior']) && ($ava2[0]['quarto_posterior']) && ($ava1[0]['quarto_posterior'] < $ava2[0]['quarto_posterior'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['quarto_posterior']) && ($ava2[0]['quarto_posterior']) && ($ava1[0]['quarto_posterior'] == $ava2[0]['quarto_posterior'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Comprimento</td>
            <td><?=$ava1[0]['comprimento']?></td>
            <td><?=$ava2[0]['comprimento']?></td>
            <td>
              <? if(($ava1[0]['comprimento']) && ($ava2[0]['comprimento']) && ($ava1[0]['comprimento'] > $ava2[0]['comprimento'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['comprimento']) && ($ava2[0]['comprimento']) && ($ava1[0]['comprimento'] < $ava2[0]['comprimento'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['comprimento']) && ($ava2[0]['comprimento']) && ($ava1[0]['comprimento'] == $ava2[0]['comprimento'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Orgão Sexual</td>
            <td><?=$ava1[0]['orgao']?></td>
            <td><?=$ava2[0]['orgao']?></td>
            <td>
              <? if(($ava1[0]['orgao']) && ($ava2[0]['orgao']) && ($ava1[0]['orgao'] > $ava2[0]['orgao'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['orgao']) && ($ava2[0]['orgao']) && ($ava1[0]['orgao'] < $ava2[0]['orgao'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['orgao']) && ($ava2[0]['orgao']) && ($ava1[0]['orgao'] == $ava2[0]['orgao'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Conformação</td>
            <td><?=$ava1[0]['conformacao']?></td>
            <td><?=$ava2[0]['conformacao']?></td>
            <td>
              <? if(($ava1[0]['conformacao']) && ($ava2[0]['conformacao']) && ($ava1[0]['conformacao'] > $ava2[0]['conformacao'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['conformacao']) && ($ava2[0]['conformacao']) && ($ava1[0]['conformacao'] < $ava2[0]['conformacao'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['conformacao']) && ($ava2[0]['conformacao']) && ($ava1[0]['conformacao'] == $ava2[0]['conformacao'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Tamanho</td>
            <td><?=$ava1[0]['tamanho']?></td>
            <td><?=$ava2[0]['tamanho']?></td>
            <td>
              <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] > $ava2[0]['tamanho'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] < $ava2[0]['tamanho'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['tamanho']) && ($ava2[0]['tamanho']) && ($ava1[0]['tamanho'] == $ava2[0]['tamanho'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Distribuição</td>
            <td><?=$ava1[0]['distribuicao']?></td>
            <td><?=$ava2[0]['distribuicao']?></td>
            <td>
              <? if(($ava1[0]['distribuicao']) && ($ava2[0]['distribuicao']) && ($ava1[0]['distribuicao'] > $ava2[0]['distribuicao'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['distribuicao']) && ($ava2[0]['distribuicao']) && ($ava1[0]['distribuicao'] < $ava2[0]['distribuicao'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['distribuicao']) && ($ava2[0]['distribuicao']) && ($ava1[0]['distribuicao'] == $ava2[0]['distribuicao'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Cobertura</td>
            <td><?=$ava1[0]['cobertura']?></td>
            <td><?=$ava2[0]['cobertura']?></td>
            <td>
              <? if(($ava1[0]['cobertura']) && ($ava2[0]['cobertura']) && ($ava1[0]['cobertura'] > $ava2[0]['cobertura'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['cobertura']) && ($ava2[0]['cobertura']) && ($ava1[0]['cobertura'] < $ava2[0]['cobertura'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['cobertura']) && ($ava2[0]['cobertura']) && ($ava1[0]['cobertura'] == $ava2[0]['cobertura'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          <tr>
            <td>Cor</td>
            <td><?=$ava1[0]['cor']?></td>
            <td><?=$ava2[0]['cor']?></td>
            <td>
              <? if(($ava1[0]['cor']) && ($ava2[0]['cor']) && ($ava1[0]['cor'] > $ava2[0]['cor'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
              <? if(($ava1[0]['cor']) && ($ava2[0]['cor']) && ($ava1[0]['cor'] < $ava2[0]['cor'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
              <? if(($ava1[0]['cor']) && ($ava2[0]['cor']) && ($ava1[0]['cor'] == $ava2[0]['cor'])){ ?> <span>Manteve</span> <? } ?>
            </td>
          </tr>
          </tbody>
          <tfoot><tr>
            <th>Resultado</th>
            <th><? if($ava1[0]['id']>0){ ?> Tipo <?=$ava1[0]['tipo']; } ?></th>
            <th><? if($ava2[0]['id']>0){ ?> Tipo <?=$ava2[0]['tipo']; } ?></th>
            <th><? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] > $ava2[0]['tipo'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] < $ava2[0]['tipo'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] == $ava2[0]['tipo'])){ ?> <span>Manteve</span> <? } ?></th>
          </tr>
          </tfoot></table>
      </div>
    </div>

    <div class="col-md-6">
          <div class="box" style="border-top:0; box-shadow:none;">
          <div class="box-header with-border">
            <h3 class="box-title">Animal × média do rebanho</h3>

          </div>
          <div class="box-body chart-responsive">
            <div class="chart" id="bar-chart" style="height:420px;"></div>
          </div>
          <!-- /.box-body -->
          </div>
    </div>

  </div>
</div>

<? }
} ?>

<? if($avaliacao == 1){ include "animal/avaliacao/avaliacao1.php"; } ?>
<? if($avaliacao == 2){ include "animal/avaliacao/avaliacao2.php"; } ?>

</div>
