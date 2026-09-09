<?
if($_POST['comprador']){
  $comprador = $_POST['comprador'];
  $comprador = DBRead('mercado', "WHERE nome = '$comprador'");
  $id_comprador = $comprador[0]['id'];
}else{
  $id_comprador = $_GET['id_comprador'];
  $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
}

$vendas = DBRead('vendas', "WHERE comprador = '$id_comprador' ORDER BY data asc");
foreach ($vendas as $venda){
  $total = $total+$vendas['preco_de_venda'];
  $x++;
}

if ($vendas){
  $data = $venda['data'];
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
}

?>

<script type="text/javascript">
function abrir_venda(id_animal){
  window.open("geral.php?pg=animal&id_animal="+id_animal+"&aba=vender", '_blank');
}

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
    window.location.href = "vendas/_excluir_venda.php?id_animal="+id_animal+"&id_comprador=<?=$id_comprador?>";
}

function atualizar(x){
  window.location.href = "geral.php?pg=comprador&id_comprador=<?=$id_comprador?>&filtro="+x;
}
</script>



<section class="content-header">
  <h1>
    Dados comprador
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-shopping-cart"></i> Vendas</a></li>
    <li><a href="#">Comprador</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-4">
				<div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
          <form method="post" action="vendas/_alterar_comprador.php?id_comprador=<?=$id_comprador?>">
            <div class="form-group">
                <label for="exampleInputPassword1">Nome completo</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?=$comprador[0]['nome']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">E-mail</label>
                <input type="text" class="form-control" id="email" name="email" value="<?=$comprador[0]['email']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Celular (WhatsApp)</label>
                <input type="text" class="form-control" id="celular" name="celular" value="<?=$comprador[0]['celular1']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" value="<?=$comprador[0]['cpf']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Cod. criador</label>
                <input type="text" class="form-control" id="cod" name="cod" value="<?=$comprador[0]['cod_criador']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Cidade</label>
                <input type="text" class="form-control" id="cidade" name="cidade" value="<?=$comprador[0]['cidade']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Estado</label>
                <select class="form-control select" name="estado" id="estado">
                  <option value="<?=$comprador[0]['estado']?>"><?=$comprador[0]['estado']?></option>
                  <option value=""></option>
                  <?
                  $estado = DBRead('estado', "ORDER BY estado asc");
                  foreach ($estado as $estado_) {
                  ?>
                    <option value="<?=$estado_['sigla']?>"><?=$estado_['estado']?></option>
                  <? } ?>
                </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-warning" style="width:100%; margin-top:4%;">Alterar comprador</button>
            </div>
          </form>
          </div>
        </div>
          <!-- /.box-body -->
          <div class="box box-success">
            <!-- /.box-header -->
            <div class="box-body" style="font:15px;">
            <strong>Valor total:</strong> R$ <?=number_format($total,2,",",".");?> </br>
            <strong>Vendas:</strong> <?=$x?></br>
            <strong>Última venda:</strong> <?=$data?></br>
            </div>
          </div>
			</div>
      <!-- /.col -->


      <div class="col-md-8">
          <div class="box box-success">
            <!-- /.box-header -->
        <form method="post" action="">
          <div class="box-body">
            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Nº</th>
                <th>Animal</th>
                <th>FBB</th>
                <th onclick="atualizar(1)" style="cursor:pointer;">Data <i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
                <th>Tipo de venda</th>
                <th onclick="atualizar(2)" style="cursor:pointer;">Valor <i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
                <th>Parcelas</th>
                <th>Excluir</th>
              </tr>

              <?
              $x=1;
              $filtro = $_GET['filtro'];
              if($filtro == 0){ $venda = DBRead('vendas', "WHERE comprador = '$id_comprador'"); }
              if($filtro == 1){ $venda = DBRead('vendas', "WHERE comprador = '$id_comprador' ORDER BY data desc"); }
              if($filtro == 2){ $venda = DBRead('vendas', "WHERE comprador = '$id_comprador' ORDER BY preco_de_venda desc"); }
              foreach ($venda as $vendas){
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

              ?>
                <tr>
                    <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$x?></td>
                    <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
                    <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$animal[0]['fbb']?></td>
                    <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$data?></td>
                    <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><?=$vendas['tipo_venda']." - ".$vendas['forma_de_pagamento']?></td>
                    <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" >R$ <?=number_format($vendas['preco_de_venda'],2,",",".");?></td>
                    <td onclick="abrir_venda(<?=$id_animal?>)" style="cursor:pointer;" ><? if($vendas['parcelas'] == 0){ echo "1x"; }else{ echo $vendas['parcelas']."x"; } ?></td>
                    <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_venda(<?=$id_animal?>)">X</button></td>
                  </tr>
                <? $x++; } ?>
                </table>

          </div>
        </form>

        </div>
        <!-- /.col -->
      </div>
    <!-- /.row -->
  </div>
</section>
  <!-- /.content -->
