<script type="text/javascript">
function excluir_venda(id_venda){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/embrioes/palco_excluir_venda.php?id_venda="+id_venda;
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

function fechar_excluir(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_venda(id_venda){
    window.location.href = "reproducao/embrioes/_excluir_venda.php?id_venda="+id_venda;
}
</script>
<?

$data_inicial = $_POST['data_inicial']; if(!$data_inicial){ $data_inicial = $_GET['data_inicial']; } if(!$data_inicial){ $data_inicial = date('01/m/Y'); }
$data_final = $_POST['data_final']; if(!$data_final){ $data_final = $_GET['data_final']; } if(!$data_final){ $data_final = date('31/m/Y'); }
$id_cliente = $_POST['cliente_filtro'];
$status = $_POST['status_filtro'];
$tipo = $_POST['tipo_filtro'];

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
?>


<section class="content-header">
  <h1>
    Relatório de vendas dos embriões
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Banco de embriões</a></li>
    <li><a href="#">Relatório</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="geral.php?pg=vendas_embriao" onsubmit="return validar_montar()">
          <!-- /.box-header -->
          <div class="box-body">

            <div class="form-group">
                <label for="exampleInputPassword1">Data inicial<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$data_inicial?>">
                </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data final<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_final" name="data_final" value="<?=$data_final?>">
                </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary" style="width:100%; margin-top:4%;">Pesquisar</button>
            </div>

        </div>
      </form>
			</div>
      <!-- /.col -->

  </div>

    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Data</th>
              <th>Embrião</th>
              <th>Cliente</th>
              <th>Valor</th>
              <th>Parcelas</th>
              <th>Tipo</th>
              <th style="width:15%;">Funções</th>
            </tr>
            <?
            $venda = DBRead('venda_embriao', "WHERE data >= '$data_inicial_' and data <= '$data_final_'  ORDER BY data asc ");
            foreach($venda as $venda_){
              $id_embriao = $venda_['id_embriao'];
              $embriao = DBRead('embriao', "WHERE id = $id_embriao");

              $id_macho = $embriao[0]['pai'];
              if($embriao[0]['terceiro']){
                $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
              }else{
                $macho = DBRead('animais', "WHERE id = '$id_macho'");
              }

              $id_femea = $embriao[0]['mae'];
              if($embriao[0]['terceiro']){
                $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
              }else{
                $femea = DBRead('animais', "WHERE id = '$id_femea'");
              }

              $data = $venda_['data'];
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

              $id_comprador = $venda_['comprador'];
              $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
              ?>

              <tr>
                <td><?=$data?></td>
                <td><?=$macho[0]['nome']?> - <?=$femea[0]['nome']?></td>
                <td><?=$comprador[0]['nome']; ?></td>
                <td>R$ <?=number_format($venda_['valor'],2,",",".");?></td>
                <td><?=$venda_['parcelas']?>x</td>
                <td><?=$venda_['tipo_venda']?> - <?=$venda_['forma_de_pagamento']?></td>
                <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_venda(<?=$venda_['id']?>)">X</button></td>
              </tr>
            <? }  ?>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
