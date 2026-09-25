<?php
$hoje = date('d/m/Y');
$id_animal = max(0, (int)filter_var($_GET['id_animal'] ?? 0, FILTER_VALIDATE_INT));
$animal = $id_animal ? (DBRead('animais', "WHERE id = '$id_animal'") ?: array()) : array();
$peso = $animal ? (DBRead('pesagem', "WHERE id_animal = '$id_animal' ORDER BY data ASC, id ASC") ?: array()) : array();
$lerDataPeso = function ($valor) {
  if (!is_string($valor) || !preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $valor, $partes)
      || !checkdate((int)$partes[2], (int)$partes[3], (int)$partes[1])) {
    return null;
  }
  return DateTime::createFromFormat('!Y-m-d', $valor);
};
$dadosGraficoPesagem = array();
foreach ($peso as $registroPeso) {
  if ($lerDataPeso($registroPeso['data'] ?? null) && is_numeric($registroPeso['peso'] ?? null)) {
    $dadosGraficoPesagem[] = array('y' => $registroPeso['data'], 'item1' => (float)$registroPeso['peso']);
  }
}
?>

<script type="text/javascript">
function validar(){
  saida = 0;
	if(!document.getElementById("animal").value){
    document.getElementById("animal").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("animal").style.border = "1px solid green";}

  if(!document.getElementById("data").value){
    document.getElementById("data").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data").style.border = "1px solid green";}

  if(document.getElementById("valor").value == ''){
    document.getElementById("valor").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("valor").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function excluir_peso(id_peso){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "pesagem/palco_excluir_peso.php?id_peso="+id_peso;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_excluir").innerHTML = resposta;
}
}
PP.send(null);
document.getElementById("transparencia").style.display = 'block';
document.getElementById("palco_excluir").style.display = 'block';
}

function fechar_excluir_peso(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_peso(id_peso){
    window.location.href = "pesagem/_excluir_peso.php?id_peso="+id_peso+"&id_animal=<?=$id_animal?>";
}
</script>

<section class="content-header">
  <h1>
    Pesagem individual
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-eyedropper"></i> Pesagem</a></li>
    <li><a href="#">Pesquisa</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="pesagem/_pesagem.php" onsubmit="return validar()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                  <label for="exampleInputPassword1">Animal<span style="color:#F00;">*</span></label>
                  <input type="text" name="animal" id="animal" class="form-control" oninput="pesquisar_animal_pesagem(this.value)" value="<?=htmlspecialchars($animal[0]['nome'] ?? '', ENT_QUOTES, 'UTF-8')?>">
                <div id="lista_animal_peso" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;"></div>
            </div>
            <? if($animal){ ?>
            <div class="form-group">
                <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data" name="data" value="<?=$hoje?>">
                </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Peso (kg)<span style="color:#F00;">*</span></label>
                  <input type="text" class="form-control pull-right" id="valor" name="valor">
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-success" style="margin-top:6%; width:100%;">Cadastrar peso</button>
            </div>
          <? } ?>
			   </div>
       </form>
    </div>


    <div class="box box-success">
      <form method="post" action="pesagem/_pesagem.php" onsubmit="return validar()">
      <!-- /.box-header -->
      <div class="box-body">
        <table class="table table-bordered" id="tabela_padrao" >
          <tr>
            <th>Data</th>
            <th>Peso</th>
            <th>Gmd - Dias</th>
            <th>&nbsp;</th>
          </tr>
          <?
          $dataBase = null;
          $pesoBase = null;
          if (!$peso) { ?>
            <tr><td colspan="4" class="text-center"><?=$animal ? 'Nenhuma pesagem cadastrada.' : 'Selecione um animal para consultar as pesagens.'?></td></tr>
          <?php }
          foreach ($peso as $peso_) {
            $dataPeso = $lerDataPeso($peso_['data'] ?? null);
            $data = $dataPeso ? $dataPeso->format('d/m/Y') : 'Não informada';
            $gmd = null;
            $dias = null;
            if ($dataPeso && is_numeric($peso_['peso'] ?? null)) {
              if ($dataBase && $dataPeso > $dataBase) {
                $dias = (int)$dataBase->diff($dataPeso)->days;
                $gmd = ($peso_['peso'] - $pesoBase) / $dias * 1000;
              } elseif (!$dataBase) {
                $dataBase = $dataPeso;
                $pesoBase = $peso_['peso'];
              }
            }
          ?>

        <? if($gmd < 0){?>  <tr style="color:red;"> <? } else{ ?> <tr style="color:green;"> <? } ?>
            <td><?=$data?></td>
            <td><?=$peso_['peso']?> kg</td>
            <td><?=$gmd === null ? '—' : number_format($gmd, 2, ',', '.') . ' g - ' . $dias . ' dias'?></td>
            <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_peso(<?=$peso_['id']?>)">X</button></td>
            </tr>
          <? } ?>
          </table>
     </div>
   </form>
  </div>
</div>


      <div class="col-md-9">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
            </div>
            Gráfico de evolução
              <div class="box-header with-border">
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body chart-responsive">
                <?php if (!$dadosGraficoPesagem) { ?><p class="text-muted">Nenhuma pesagem disponível para o gráfico.</p><?php } ?>
                <div class="chart" id="line-chart" style="height: 300px;"></div>
              </div>
              <!-- /.box-body -->
          </div>
          <!-- /.box-body -->
        </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
</section>
  <!-- /.content -->
