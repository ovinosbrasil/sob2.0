<?
include "../_config.php";

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



$sql = DBRead("controle_financeiro", "WHERE data >= '$data_inicial_' and data <= '$data_final_'  ORDER BY data asc ");

foreach($sql as $linha){
  if((!$linha['tipo']) && ($linha['status']) ){
    $receber = $receber+$linha['valor'];
  }

  if((!$linha['tipo']) && (!$linha['status']) ){
    $receber_ = $receber_+$linha['valor'];
  }

  if(($linha['tipo']) && ($linha['status']) ){ $pagar = $pagar+$linha['valor']; }
  if(($linha['tipo']) && (!$linha['status']) ){ $pagar_ = $pagar_+$linha['valor']; }
}


?>

<script type="text/javascript">
function atualizar_financeiro(tipo,id_financeiro){
  if(tipo == 0){ confirmar(id_financeiro); }
  if(tipo == 1){ cancelar(id_financeiro); }
  if(tipo == 2){ alterar(id_financeiro); }
  if(tipo == 3){ abrir_excluir(id_financeiro); }
}


function confirmar(id_financeiro){
		data_inicial = document.getElementById("data_inicial").value;
		data_final = document.getElementById("data_final").value;
    window.location.href = "financeiro/_confirmar.php?id_financeiro="+id_financeiro+"&data_inicial="+data_inicial+"&data_final="+data_final;
}

function cancelar(id_financeiro){
		data_inicial = document.getElementById("data_inicial").value;
		data_final = document.getElementById("data_final").value;
    window.location.href = "financeiro/_cancelar.php?id_financeiro="+id_financeiro+"&data_inicial="+data_inicial+"&data_final="+data_final;
}

function abrir_excluir(id_financeiro){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
 var url = "financeiro/palco_excluir.php?id_financeiro="+id_financeiro;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_excluir").innerHTML = resposta; document.getElementById("palco_excluir").style.display = 'block';
}
}
PP.send(null);
document.getElementById("transparencia").style.display = 'block';
}

function excluir(id_financeiro){
		data_inicial = document.getElementById("data_inicial").value;
		data_final = document.getElementById("data_final").value;
    window.location.href = "financeiro/_excluir.php?id_financeiro="+id_financeiro+"&data_inicial="+data_inicial+"&data_final="+data_final;
}


function fechar_excluir(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function faturamento(){
	document.getElementById("transparencia").style.display = 'block';
	document.getElementById("palco_faturamento").style.display = 'block';
}

function fechar_faturamento(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_faturamento").style.display = 'none';
}

function debito(){
	document.getElementById("transparencia").style.display = 'block';
	document.getElementById("palco_debito").style.display = 'block';
}

function fechar_debito(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_debito").style.display = 'none';
}

function alterar(id_financeiro){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "financeiro/palco_alterar.php?id_financeiro="+id_financeiro;

// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_alterar").innerHTML = resposta; document.getElementById("palco_alterar").style.display = 'block';
}
}
PP.send(null);
document.getElementById("transparencia").style.display = 'block';
}

function fechar_alterar(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_alterar").style.display = 'none';
}
</script>


<div class="row" id="palco_excluir" style=" z-index:9999999999;  left:40%; top:5%;  position:absolute; position:fixed;"></div>
<div class="row" id="palco_alterar" style=" z-index:999999999;  left:20%; top:5%;  position:absolute; position:fixed; display:none;"></div>
<div class="row" id="palco_faturamento" style=" z-index:999999999999;  left:20%; top:5%; position:absolute; position:fixed; display:none;">
    <!-- left column -->
    <div class="col-md-9">
      <!-- general form elements -->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Cadastrar faturamento</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form method="post" action="financeiro/_faturamento.php">
        <div class="box-body">
          <div class="col-md-4 ">
            <div class="form-group">
              <label for="exampleInputEmail1">Descrição<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="descricao_faturamento" name="descricao_faturamento" placeholder="Digite aqui...">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Forma de pagamento<span style="color:#F00;">*</span></label>
                <select class="form-control select" name="forma_faturamento" id="forma_faturamento">
                  <option value="">Selecionar forma</option>
                  <option value=""></option>
                  <option value="Boleto">Boleto</option>
                  <option value="Dinheiro">Dinheiro</option>
                  <option value="Depósito">Depósito</option>
                  <option value="Cartão">Cartão</option>
                  <option value="Transação bancária">Transação bancária</option>
                </select>
            </div>
          </div>

          <div class="col-md-4 " style="margin-left:-12px;">
            <div class="form-group">
                <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_faturamento" name="data_faturamento" value="<?=date('d/m/Y');?>">
                </div>
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Repetir parcelas</label>
              <select class="form-control select" name="parcelas_faturamento" id="parcelas_faturamento">
                <option value="1">1x</option>
                <option value=""></option>
                <option value="2">2x</option>
                <option value="3">3x</option>
                <option value="4">4x</option>
                <option value="5">5x</option>
                <option value="6">6x</option>
                <option value="7">7x</option>
                <option value="8">8x</option>
                <option value="9">9x</option>
                <option value="10">10x</option>
              </select>
            </div>

        </div>

      <div class="col-md-4">
        <div class="form-group">
          <label for="exampleInputPassword1">Valor<span style="color:#F00;">*</span></label>
          <input type="text" id="valor_faturamento" name="valor_faturamento" class="form-control" placeholder="R$ 00,00">
        </div>


      </div>
      <button type="submit" class="btn btn-success" style="margin-left:1.5%;">Cadastrar Faturamento</button>
    </form>
    <button type="button" class="btn btn-danger" style="float:right; margin-right:2.5%;" onclick="fechar_faturamento()">Fechar</button>
  </div></div></div></div>

<div class="row" id="palco_debito" style=" z-index:99999999;  left:20%; top:5%;  position:absolute; position:fixed; display:none;">
      <!-- left column -->
      <div class="col-md-9">
        <!-- general form elements -->
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Cadastrar Débito</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <form method="post" action="financeiro/_debito.php">
          <div class="box-body">
            <div class="col-md-4 ">
              <div class="form-group">
                <label for="exampleInputEmail1">Descrição<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="descricao_debito" name="descricao_debito" placeholder="Digite aqui...">
              </div>

              <div class="form-group">
                  <label for="exampleInputPassword1">Forma de pagamento<span style="color:#F00;">*</span></label>
                  <select class="form-control select" name="forma_debito" id="forma_debito">
                    <option value="">Selecionar forma</option>
                    <option value=""></option>
                    <option value="Boleto">Boleto</option>
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão">Cartão</option>
                    <option value="Transação bancária">Transação bancária</option>
                  </select>
              </div>
            </div>

            <div class="col-md-4 " style="margin-left:-12px;">
              <div class="form-group">
                  <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="data_debito" name="data_debito" value="<?=date('d/m/Y');?>">
                  </div>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Categoria<span style="color:#F00;">*</span></label>
                <select class="form-control select" name="categoria_debito" id="categoria_debito">
                  <option value="">Selecionar</option>
                  <option value=""></option>
                  <option value="Custos fixos (Água, luz, telefone)">Custos fixos (Água, luz, telefone)</option>
                  <option value="Infraestrutura">Infraestrutura</option>
                  <option value="Pagamento Profissional">Pagamento Profissional</option>
                  <option value="Encargos de funcionários">Encargos de funcionários</option>
                  <option value="Contabilidade">Contabilidade</option>
                  <option value="Marketing">Marketing</option>
                  <option value="Outros">Outros</option>
                </select>
              </div>

          </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="exampleInputPassword1">Valor<span style="color:#F00;">*</span></label>
            <input type="text" id="valor_debito" name="valor_debito" class="form-control" placeholder="R$ 00,00">
          </div>

          <div class="form-group">
            <label for="exampleInputPassword1">Parcelas</label>
            <select class="form-control select" name="parcelas_debito" id="parcelas_debito">
              <option value="1">1x</option>
              <option value=""></option>
              <option value="2">2x</option>
              <option value="3">3x</option>
              <option value="4">4x</option>
              <option value="5">5x</option>
              <option value="6">6x</option>
              <option value="7">7x</option>
              <option value="8">8x</option>
              <option value="9">9x</option>
              <option value="10">10x</option>
            </select>
        </div>

      </div>
    <button type="submit" class="btn btn-success" style="margin-left:1.5%;">Cadastrar Débito</button>
  </form>
<button type="button" class="btn btn-danger" style="float:right; margin-right:2.5%;" onclick="fechar_debito()">Fechar</button>
</div></div></div></div>



<section class="content-header">
  <h1>
    Extrato financeiro
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-money"></i> Extrato financeiro</a></li>
    <li><a href="#">Lista</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-primary">
          <!-- /.box-header -->
          <div class="box-body">
						<form method="post" action="geral.php?pg=financeiro">
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
                <label for="exampleInputPassword1">Faturamento/Débito</label>
                <select class="form-control select" onchange="atualizar_exames(this.value)" name="tipo_filtro" id="tipo_filtro">
                  <option value="Todas">Todos</option>
                  <option value=""></option>
									<option value="0">Faturamento</option>
									<option value="1">Débito</option>
                </select>
            </div>


						<div class="form-group">
								<label for="exampleInputPassword1">Status</label>
								<select class="form-control select" onchange="atualizar_exames(this.value)" name="status_filtro" id="status_filtro">
									<? if($status){ ?> <option value="<?=$status?>"><?=$status?></option> <? }else{ ?> <option value="Todas">Todas</option> <? } ?>
									<option value="Todos"></option>
									<option value="Confirmado">Confirmado</option>
									<option value="Aguardando">Aguardando</option>
									<option value="Todos">Todos</option>
								</select>
						</div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width:100%; margin-bottom:3%;">Pesquisar Extrato</button>
            </div>
					</form>

            <div class="form-group">
              <label for="exampleInputPassword1" style="font-size:18px; color:#00a65a;">Faturamento:
            R$<?=number_format($receber,2,",",".");?></label><br/>

              <label for="exampleInputPassword1" style="font-size:18px; color:#f53030;">Débito:
                 R$<?=number_format($pagar,2,",",".");?></label>
                 <br/>
              <label for="exampleInputPassword1" style="font-size:18px;">Receber:
                  R$<?=number_format($receber_,2,",",".");?></label><br/>

              <label for="exampleInputPassword1" style="font-size:18px;">Pagar:
                  R$<?=number_format($pagar_,2,",",".");?></label>

            </div>
          </div>
          <!-- /.box-body -->
			</div>
      <!-- /.col -->
    </div>



      <div class="col-md-9">
        <div class="box  box-primary">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
							<button type="submit" class="btn btn-danger" onclick="debito()" style="float:right; margin-bottom:1%; margin-left:3%;">Adicionar Débito</button>
						  <button type="submit" class="btn btn-success" style="float:right; margin-bottom:1%; margin-left:3%;" onclick="faturamento()">Adicionar Faturamento</button>
            </div>

            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Cliente/categoria</th>
                <th>Valor</th>
                <th>Tipo</th>
                <th style="width:15%;">Funções</th>
              </tr>
							<?
							if(($tipo == 'Todas') || ($tipo == '')){ $tipo = 'x';}
							if(($status == 'Todas') || ($status == '')){ $status = 'x';}
							if($status == 'Confirmado'){ $status = 1; }
							if($status == 'Aguardando'){ $status = '0'; }

              foreach($sql as $linha){
                $id_animal = $linha['id_animal'];
                if($id_animal){
                $venda = DBRead('vendas', "WHERE id_animal = '$id_animal'");
                $id_comprador = $venda[0]['comprador'];
                if($id_comprador){
                    $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
                }
                }

                if(($linha['tipo'] == 0) && ($linha['id_animal'] == 0)){
                  $id_comprador = $linha['id_comprador'];
                  $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
                }


                $data = $linha['data'];
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

   							if(($linha['tipo'] == 0) && ($linha['status'] == 0) ){?>  <tr> <? }
  							if(($linha['tipo'] == 0) && ($linha['status'] == 1)) { ?>  <tr style="color:#093; font-size:15px;"> <? }
  							if(($linha['tipo'] == 1) && ($linha['status'] == 0)) {?>  <tr> <? }
   						  if(($linha['tipo'] == 1) && ($linha['status'] == 1)) {?>  <tr style="color:#F00; font-size:15px;"> <? }

                if( (($tipo == $linha['tipo']) || ($tipo == 'x'))  && (($status == $linha['status']) || ($status == 'x'))){ ?>
                  <td><?=$data?></td>
                  <td><?=$linha['titulo']?></td>
                  <td>
                  <?
                  if(($linha['tipo'] == 0) || ($linha['tipo'] == 2) ){
                    echo $comprador[0]['nome'];
                  }
                  if($linha['tipo'] == 1){
                    echo $linha['categoria'];
                  }
                  ?>
                </td>
                  <td>R$ <? if($linha['tipo'] == 1){ echo "- ";} ?> <?=number_format($linha['valor'],2,",",".");?></td>
                  <td><?
                  if(($linha['tipo'] == 0) && ($linha['id_animal'] > 1)){
                    echo $venda[0]['tipo_venda']," - ", $linha['forma_de_pagamento'];
                  }

                  if($linha['tipo'] == 1){
                    echo $linha['forma_de_pagamento'];
                  }

                  if(($linha['tipo'] == 0) && ($linha['id_animal'] == 0)){
                    echo $linha['forma_de_pagamento'];
                  }
                  ?></td>
                  <td>
                    <select name="" id="" onchange="atualizar_financeiro(this.value,<?=$linha['id']?>)" style="margin:0%; padding:0%; height:30px;">
                      <option value="">Selecionar</option>
                      <option value=""></option>
                      <option value="0">Confirmar</option>
                      <option value="1">Cancelar</option>
                      <option value="2">Alterar</option>
                      <option value="3">Excluir</option>
                    </select>
                  </td>
                </tr>
              <? } } ?>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
