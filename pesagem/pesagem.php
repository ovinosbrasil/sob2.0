<?

function geraTimestamp($data) {
  $partes = explode('/', $data);
  return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

$hoje = date('d/m/Y');
$id_animal = $_GET['id_animal'];
$animal = DBRead('animais', "WHERE id = '$id_animal'");
if($id_animal>0){
  $peso = DBRead('pesagem', "WHERE id_animal = '$id_animal' ORDER BY data asc");
  if($peso[0]['peso'] > 0){ $pesagem1 = $peso[0]['peso']; $data1 = $peso[0]['data'];}
  if($peso[1]['peso'] > 0){ $pesagem2 = $peso[1]['peso']; $data2 = $peso[1]['data']; }else{ $pesagem2 = $pesagem1; $data2 = $data1; }
  if($peso[2]['peso'] > 0){ $pesagem3 = $peso[2]['peso']; $data3 = $peso[2]['data']; }else{ $pesagem3 = $pesagem2; $data3 = $data2; }
  if($peso[3]['peso'] > 0){ $pesagem4 = $peso[3]['peso']; $data4 = $peso[3]['data']; }else{ $pesagem4 = $pesagem3; $data4 = $data3; }
  if($peso[4]['peso'] > 0){ $pesagem5 = $peso[4]['peso']; $data5 = $peso[4]['data']; }else{ $pesagem5 = $pesagem4; $data5 = $data4; }
  if($peso[5]['peso'] > 0){ $pesagem6 = $peso[5]['peso']; $data6 = $peso[5]['data']; }else{ $pesagem6 = $pesagem5; $data6 = $data5; }
  if($peso[6]['peso'] > 0){ $pesagem7 = $peso[6]['peso']; $data7 = $peso[6]['data']; }else{ $pesagem7 = $pesagem6; $data7 = $data6; }
  if($peso[7]['peso'] > 0){ $pesagem8 = $peso[7]['peso']; $data8 = $peso[7]['data']; }else{ $pesagem8 = $pesagem7; $data8 = $data7; }
  if($peso[8]['peso'] > 0){ $pesagem9 = $peso[8]['peso']; $data9 = $peso[8]['data']; }else{ $pesagem9 = $pesagem8; $data9 = $data8; }
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
                  <input type="text" name="animal" id="animal" class="form-control" onKeyPress="pesquisar_animal_pesagem(this.value)" value="<?=$animal[0]['nome']?>">
                <div id="lista_animal_peso" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;"></div>
            </div>
            <? if($id_animal){ ?>
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
          $peso = DBRead('pesagem', "WHERE id_animal = '$id_animal' ORDER BY data asc");
          foreach ($peso as $peso_){
            $data = $peso_['data'];;
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
            $data_pesagem = $data;

            if($data_antiga > "0000-00-00"){
              $time_inicial = geraTimestamp($data_antiga);
              $time_final = geraTimestamp($data_pesagem);
              $diferenca = $time_final - $time_inicial;
              $dias = (int)floor( $diferenca / (60 * 60 * 24));
              $gmd = ($peso_['peso']-$peso_antigo)/$dias;
            }else{
              $data_antiga = $data_pesagem;
              $peso_antigo = $peso_['peso'];
            }
          ?>

        <? if($gmd < 0){?>  <tr style="color:red;"> <? } else{ ?> <tr style="color:green;"> <? } ?>
            <td><?=$data?></td>
            <td><?=$peso_['peso']?> kg</td>
            <td><?=number_format($gmd,2,",",".");?>g - <?=$dias?> dias</td>
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
