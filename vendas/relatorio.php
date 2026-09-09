<?
$filtro = $_GET['filtro'];
if (!$filtro) { 
  $filtro = $_POST['filtro'];
}

if (!$filtro) {
  $filtro = "Data";
}

if ($filtro == 'Data') {
  $data_inicial = $_POST['data_inicial'];
  if (!$data_inicial) {
    $data_inicial = $_GET['data_inicial'];
  }

  if (!$data_inicial) {
    $data_inicial = date('01/m/Y');
  }

  $data_final = $_POST['data_final'];
  if (!$data_final) {
    $data_final = $_GET['data_final'];
  }

  if (!$data_final) {
    $data_final = date('31/m/Y');
  }
  
  $comprador = $_POST['comprador'];
  if ($comprador) {
    $comprador = DBRead('mercado', "WHERE nome = '$comprador'");
    $id_comprador = $comprador[0]['id'];
  }

  $data = $data_inicial;
  $data_atual = $data;
  $data = '0';
  $data['0'] = $data_atual['6'];
  $data['1'] = $data_atual['7'];
  $data['2'] = $data_atual['8'];
  $data['3'] = $data_atual['9'];
  $data['4'] = "-";
  $data['5'] = $data_atual['3'];
  $data['6'] = $data_atual['4'];
  $data['7'] = "-";
  $data['8'] = $data_atual['0'];
  $data['9'] = $data_atual['1'];
  $data_inicial_ = $data;


  $data = $data_final;
  $data_atual = $data;
  $data = '0';
  $data['0'] = $data_atual['6'];
  $data['1'] = $data_atual['7'];
  $data['2'] = $data_atual['8'];
  $data['3'] = $data_atual['9'];
  $data['4'] = "-";
  $data['5'] = $data_atual['3'];
  $data['6'] = $data_atual['4'];
  $data['7'] = "-";
  $data['8'] = $data_atual['0'];
  $data['9'] = $data_atual['1'];
  $data_final_ = $data;


  if (($id_comprador) && ($id_comprador != 'Todos')) {
    $sql = DBRead("vendas", "WHERE data >= '$data_inicial_' and data <= '$data_final_' and comprador = '$id_comprador' ORDER BY data asc ");
  } else {
    $sql = DBRead("vendas", "WHERE data >= '$data_inicial_' and data <= '$data_final_' ORDER BY data asc ");
  }
}


if ($filtro == 'Anual') {
  $ano = $_POST['ano'];
  if (!$ano) { 
    $ano = date('Y');
  }
  $data_inicial_ = $ano."-01-01";
  $data_final_ = $ano."-12-31";

  $sql = DBRead("vendas", "WHERE data >= '$data_inicial_' and data <= '$data_final_' ORDER BY data asc ");
  $venda_janeiro = $venda_fevereiro = $venda_marco = $venda_abril = $venda_maio = $venda_junho = $venda_julho = $venda_agosto = $venda_setembro = $venda_outubro = $venda_novembro = $venda_dezembro = 0;

  foreach ($sql as $vendas){
    list($ano, $mes, $dia) = explode('-', $vendas['data']);
    if ($mes == 01) {
       $venda_janeiro = $venda_janeiro + $vendas['preco_de_venda']; 
    }
    if ($mes == 02) {
       $venda_fevereiro = $venda_fevereiro + $vendas['preco_de_venda']; 
    }
    if ($mes == 03) {
       $venda_marco = $venda_marco + $vendas['preco_de_venda']; 
    }
    if ($mes == 04) {
       $venda_abril = $venda_abril + $vendas['preco_de_venda']; 
    }
    if ($mes == 05) {
       $venda_maio = $venda_maio + $vendas['preco_de_venda']; 
    }
    if ($mes == 06) {
       $venda_junho = $venda_junho + $vendas['preco_de_venda']; 
    }
    if ($mes == 07) {
       $venda_julho = $venda_julho + $vendas['preco_de_venda']; 
    }
    if ($mes == 8) {
        $venda_agosto = $venda_agosto + $vendas['preco_de_venda']; 
    }
    if ($mes == 9) {
        $venda_setembro = $venda_setembro + $vendas['preco_de_venda'];  
    }
    if ($mes == 10) {
       $venda_outubro = $venda_outubro + $vendas['preco_de_venda']; 
    }
    if ($mes == 11) {
       $venda_novembro = $venda_novembro + $vendas['preco_de_venda']; 
    }
    if ($mes == 12) {
       $venda_dezembro = $venda_dezembro + $vendas['preco_de_venda']; 
    }
  }
}

foreach($sql as $linha){
  $total = $linha['preco_de_venda']+$total;
}


?>

<script type="text/javascript">

function excluir_venda(id_animal){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "vendas/palco_excluir.php?id_animal="+id_animal;
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

function fechar_excluir_venda(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_venda(id_animal){
    window.location.href = "vendas/_excluir_venda.php?tipo=1&id_animal="+id_animal+"&id_comprador=<?=$id_comprador?>";
}
function atualizar(filtro){
    window.location.href = "geral.php?pg=relatorio_venda&filtro="+filtro;
}

function abrir_venda(id_animal){
  window.open("geral.php?pg=animal&id_animal="+id_animal+"&aba=vender", '_blank');
}
</script>


<div class="row" id="palco_excluir" style=" z-index:9999999999;  left:40%; top:5%;  position:absolute; position:fixed;"></div>

<section class="content-header">
  <h1>
    Relatório de Vendas
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-shopping-cart"></i> Vendas</a></li>
    <li><a href="#">Relatório</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
						<form method="post" action="geral.php?pg=relatorio_venda&filtro=<?=$filtro?>">

              <div class="form-group">
                  <label for="exampleInputPassword1">Tipo</label>
                  <select class="form-control select" onchange="atualizar(this.value)" name="filtro" id="filtro">
                    <option value="<?=$filtro?>"><?=$filtro?></option>
                    <option value=""></option>
                    <option value="Data">Data</option>
                    <option value="Anual">Anual</option>
                  </select>
              </div>

            <? if($filtro == 'Data'){ ?>
            <div class="form-group">
                <label for="exampleInputPassword1">Data Inicial</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$data_inicial?>">
                </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data Final</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_final" name="data_final" value="<?=$data_final?>">
                </div>
            </div>


            <div class="form-group">
                <label for="exampleInputPassword1">Pesquisar comprador</label>
                <input type="text" class="form-control" id="comprador" name="comprador" value="<?=$mae?>" onKeyUp="pesquisar_comprador(this.value)">
                <div id="lista_comprador" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:90%; display:none; margin-top:1%;">
              </div>
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1" style="font-size:18px; color:#00a65a;">Faturamento:
            R$<?=number_format($total,2,",",".");?></label><br/>

            </div>
            <? } ?>

            <? if($filtro == 'Anual'){ ?>
              <div class="form-group">
                  <label for="exampleInputPassword1">Tipo</label>
                  <select class="form-control select" name="ano" id="ano">
                    <? $data = $_POST['ano']; if(!$data){ $data = date('Y'); }?>
                    <option><?=$data?></option>
                    <option></option>
                    <option><?=date('Y')?></option>
                    <option><?=date('Y')-1?></option>
                    <option><?=date('Y')-2?></option>
                    <option><?=date('Y')-3?></option>
                    <option><?=date('Y')-4?></option>
                    <option><?=date('Y')-5?></option>
                    <option><?=date('Y')-6?></option>
                  </select>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1" style="font-size:18px; color:#00a65a;">Faturamento:
              R$<?=number_format($total,2,",",".");?></label><br/>

              </div>
            <? } ?>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width:100%; margin-bottom:3%;">Pesquisar Extrato</button>
            </div>
					</form>


          </div>
          <!-- /.box-body -->
			</div>
      <!-- /.col -->
    </div>



      <div class="col-md-9">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
            <? if($filtro == 'Data'){ ?>
              <table class="table table-bordered" id="tabela_padrao">
                <tr>
                  <th>Nº</th>
                  <th>Animal</th>
                  <th>Comprador</th>
                  <th>Data</th>
                  <th>Tipo de venda</th>
                  <th>Valor</th>
                  <th>Parclas</th>
                  <th>Excluir</th>
                </tr>

                <?
                $x=1;
                foreach ($sql as $vendas){
                  $id_animal = $vendas['id_animal'];
                  $animal = DBRead('animais', "WHERE id = '$id_animal'");
                  $data = $vendas['data'];
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

                  $id_comprador = $vendas['comprador'];
                  $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");

                ?>
                  <tr>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$x?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$comprador[0]['nome']?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$data?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$vendas['tipo_venda']." - ".$vendas['forma_de_pagamento']?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" >R$ <?=number_format($vendas['preco_de_venda'],2,",",".");?></td>
                      <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$vendas['parcelas']."x"?></td>
                      <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_venda(<?=$id_animal?>)">X</button></td>
                    </tr>
                  <? $x++; } ?>
                  </table>

            <? } ?>

            <? if($filtro == 'Anual'){ ?>
              <div class="col-md-12">
                    <div class="box-header with-border">
                      <h3 class="box-title">Gráfico anual</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <div class="box-body chart-responsive">
                      <div class="chart" id="bar-chart3" style="height: 550px;"></div>
                    </div>
                    <!-- /.box-body -->


              <table class="table table-bordered" id="tabela_padrao">
                <tr>
                  <th>Jan</th>
                  <th>Fev</th>
                  <th>Mar</th>
                  <th>Abr</th>
                  <th>Mai</th>
                  <th>Jun</th>
                  <th>Jul</th>
                  <th>Ago</th>
                  <th>Set</th>
                  <th>Out</th>
                  <th>Nov</th>
                  <th>Dez</th>
                </tr>
                  <tr>
                      <td>R$ <?=number_format($venda_janeiro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_fevereiro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_marco,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_abril,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_maio,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_junho,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_julho,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_agosto,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_setembro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_outubro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_novembro,2,",",".");?></td>
                      <td>R$ <?=number_format($venda_dezembro,2,",",".");?></td>
                    </tr>
                </table>
          </div>
        <? } ?>

      </div>
    </div>
  </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
