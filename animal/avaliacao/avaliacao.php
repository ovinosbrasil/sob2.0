<script type="text/javascript">

function ativar_apartacao(){
  saida = 0;
	if(!document.getElementById("data_nascimento").value){
    document.getElementById("data_nascimento").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_nascimento").style.border = "1px solid green";}

  if(!document.getElementById("data_apartacao").value){
    document.getElementById("data_apartacao").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_apartacao").style.border = "1px solid green";}

  if(document.getElementById("peso_inicial").value == '0.00'){
    document.getElementById("peso_inicial").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("peso_inicial").style.border = "1px solid green";}

  if(document.getElementById("peso_apartacao").value == '0.00'){
    document.getElementById("peso_apartacao").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("peso_apartacao").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}


function atualizar_avaliacao(x){
    window.location.href = "geral.php?pg=animal&id_animal="+<?=$id_animal?>+"&aba=avaliacao&avaliacao="+x;
}

function preencher_automatico(valor){
if(valor == 5){
	tamanho = document.forms["form1"].elements["tamanho"];
	tamanho[0].checked = true;

	cabeca = document.forms["form1"].elements["cabeca"];
	cabeca[0].checked = true;

	pescoco = document.forms["form1"].elements["pescoco"];
	pescoco[0].checked = true;

	quarto_anterior = document.forms["form1"].elements["quarto_anterior"];
	quarto_anterior[0].checked = true;

	barril = document.forms["form1"].elements["barril"];
	barril[0].checked = true;

	quarto_posterior = document.forms["form1"].elements["quarto_posterior"];
	quarto_posterior[0].checked = true;

	comprimento = document.forms["form1"].elements["comprimento"];
	comprimento[0].checked = true;

	orgao = document.forms["form1"].elements["orgao"];
	orgao[0].checked = true;

	distribuicao = document.forms["form1"].elements["distribuicao"];
	distribuicao[0].checked = true;

	cobertura = document.forms["form1"].elements["cobertura"];
	cobertura[0].checked = true;

	cor = document.forms["form1"].elements["cor"];
	cor[0].checked = true;
}

if(valor == 4){
	tamanho = document.forms["form1"].elements["tamanho"];
	tamanho[1].checked = true;

	cabeca = document.forms["form1"].elements["cabeca"];
	cabeca[1].checked = true;

	pescoco = document.forms["form1"].elements["pescoco"];
	pescoco[1].checked = true;

	quarto_anterior = document.forms["form1"].elements["quarto_anterior"];
	quarto_anterior[1].checked = true;

	barril = document.forms["form1"].elements["barril"];
	barril[1].checked = true;

	quarto_posterior = document.forms["form1"].elements["quarto_posterior"];
	quarto_posterior[1].checked = true;

	comprimento = document.forms["form1"].elements["comprimento"];
	comprimento[1].checked = true;

	orgao = document.forms["form1"].elements["orgao"];
	orgao[1].checked = true;

	distribuicao = document.forms["form1"].elements["distribuicao"];
	distribuicao[1].checked = true;

	cobertura = document.forms["form1"].elements["cobertura"];
	cobertura[1].checked = true;

	cor = document.forms["form1"].elements["cor"];
	cor[1].checked = true;
}

if(valor == 3){
	tamanho = document.forms["form1"].elements["tamanho"];
	tamanho[2].checked = true;

	cabeca = document.forms["form1"].elements["cabeca"];
	cabeca[2].checked = true;

	pescoco = document.forms["form1"].elements["pescoco"];
	pescoco[2].checked = true;

	quarto_anterior = document.forms["form1"].elements["quarto_anterior"];
	quarto_anterior[2].checked = true;

	barril = document.forms["form1"].elements["barril"];
	barril[2].checked = true;

	quarto_posterior = document.forms["form1"].elements["quarto_posterior"];
	quarto_posterior[2].checked = true;

	comprimento = document.forms["form1"].elements["comprimento"];
	comprimento[2].checked = true;

	orgao = document.forms["form1"].elements["orgao"];
	orgao[2].checked = true;

	distribuicao = document.forms["form1"].elements["distribuicao"];
	distribuicao[2].checked = true;

	cobertura = document.forms["form1"].elements["cobertura"];
	cobertura[2].checked = true;

	cor = document.forms["form1"].elements["cor"];
	cor[2].checked = true;
}

if(valor == 2){
	tamanho = document.forms["form1"].elements["tamanho"];
	tamanho[3].checked = true;

	cabeca = document.forms["form1"].elements["cabeca"];
	cabeca[3].checked = true;

	pescoco = document.forms["form1"].elements["pescoco"];
	pescoco[3].checked = true;

	quarto_anterior = document.forms["form1"].elements["quarto_anterior"];
	quarto_anterior[3].checked = true;

	barril = document.forms["form1"].elements["barril"];
	barril[3].checked = true;

	quarto_posterior = document.forms["form1"].elements["quarto_posterior"];
	quarto_posterior[3].checked = true;

	comprimento = document.forms["form1"].elements["comprimento"];
	comprimento[3].checked = true;

	orgao = document.forms["form1"].elements["orgao"];
	orgao[3].checked = true;

	distribuicao = document.forms["form1"].elements["distribuicao"];
	distribuicao[3].checked = true;

	cobertura = document.forms["form1"].elements["cobertura"];
	cobertura[3].checked = true;

	cor = document.forms["form1"].elements["cor"];
	cor[3].checked = true;
}
}
</script>

<? $avaliacao = $_GET['avaliacao']; ?>

<div class="row">
<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Selecionar Avaliação</label>
      <select class="form-control select" id="avalicao" name="avalicao" onchange="atualizar_avaliacao(this.value)">
        <? if(!$avaliacao){ ?> <option value="">Geral</option> <? } ?>
        <? if($avaliacao == 1){ ?> <option value="1">1ª Avaliação</option> <? } ?>
        <? if($avaliacao == 2){ ?> <option value="2">2ª Avaliação</option> <? } ?>
        <option></option>
        <option value="">Geral</option>
        <option value="1">1ª Avaliação</option>
        <option value="2">2ª Avaliação</option>
      </select>
    </div>
  </div>
</div>

<?
if(!$avaliacao){

$ava1 = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = 1");
$ava2 = DBRead('avaliacao', "WHERE id_animal = '$id_animal' AND avaliacao = 2");

if($ava1[0]['id'] < 0){ $ava1_ = 0; }else{ $ava1_ = $ava1[0]['tipo'];}
if(!$ava2[0]['id']< 0 ){ $ava2_ = 0; }else{ $ava2_ = $ava2[0]['tipo'];}

$total=$qtd=0;
$media1 = DBRead('avaliacao', "WHERE avaliacao = '1'");
foreach ($media1 as $media1_) {
  $total = $total+$media1_['tipo'];
  $qtd++;
}
$media1_ = number_format($total/$qtd,2,".","");


$total=$qtd=0;
$media2 = DBRead('avaliacao', "WHERE avaliacao = '2'");
foreach ($media2 as $media2_) {
  $total = $total+$media2_['tipo'];
  $qtd++;
}
$media2_ = $total ? number_format($total/$qtd,2,".","") : 0;

?>

<div class="col-md-12" style="margin-left:-12px; margin-top:-2%;">
  <!-- general form elements -->
  <div class="box-body">
    <div class="col-md-6">
      <div class="form-group" style="margin-top:3%;">
        <table class="table table-bordered" id="tabela_padrao">
          <tr>
            <th></th>
            <th>1ª Avaliação</th>
            <th>2ª Avaliação</th>
            <th>Evolução</th>
          </tr>
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
          <tr>
            <th>Resultado</th>
            <th><? if($ava1[0]['id']>0){ ?> Tipo <?=$ava1[0]['tipo']; } ?></th>
            <th><? if($ava2[0]['id']>0){ ?> Tipo <?=$ava2[0]['tipo']; } ?></th>
            <th><? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] > $ava2[0]['tipo'])){ ?> <span style="color:red;">Regrediu</span> <? } ?>
            <? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] < $ava2[0]['tipo'])){ ?> <span style="color:green;">Evoluiu</span> <? } ?>
            <? if(($ava1[0]['tipo']) && ($ava2[0]['tipo']) && ($ava1[0]['tipo'] == $ava2[0]['tipo'])){ ?> <span>Manteve</span> <? } ?></th>
          </tr>
          </table>
      </div>
    </div>

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
            <div class="chart" id="bar-chart" style="height: 550px;"></div>
          </div>
          <!-- /.box-body -->
    </div>

  </div>
</div>

<? } ?>

<? if($avaliacao == 1){ include "animal/avaliacao/avaliacao1.php"; } ?>
<? if($avaliacao == 2){ include "animal/avaliacao/avaliacao2.php"; } ?>

</div>
