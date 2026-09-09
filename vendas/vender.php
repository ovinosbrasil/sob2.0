<script type="text/javascript">
function ativar_venda(){
  saida = 0;
	if(!document.getElementById("comprador").value){
    document.getElementById("comprador").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("comprador").style.border = "1px solid green";}

  if(!document.getElementById("data_venda").value){
    document.getElementById("data_venda").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_venda").style.border = "1px solid green";}

  if(document.getElementById("valor").value == '0.00'){
    document.getElementById("valor").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("valor").style.border = "1px solid green";}

  if(!document.getElementById("tipo_venda").value){
    document.getElementById("tipo_venda").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("tipo_venda").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function calcular_valor(){
  x=1;
  total=0;
  valor = document.getElementById("preco_conjunto_"+x).value;
  while(x < 7){
    valor = document.getElementById("preco_conjunto_"+x).value;
    valor = valor.replace(/,/i, '');
    if(valor < 1){ valor = 0;}
    valor = parseInt(valor);
    total =  valor + total;
    x++;
  }
  document.getElementById("valor_total").value = total;
}
</script>


<section class="content-header">
  <h1>
    Vender animais
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatórios</a></li>
    <li><a href="#">Mortes</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <form role="form" action="vendas/_venda_conjunta.php" method="post">
    <div class="row">
      <div class="col-md-4">
				<div class="box box-success">
          <!-- /.box-header -->
        <div class="box-body">
            <? $x=1; while($x < 7){ ?>
            <div class="col-md-6" style="margin-left:-13px; margin-top:2%">
            <div class="form-group">
              <label for="exampleInputPassword1">Animal <?=$x?><span style="color:#F00;">*</span></label>
              <input type="text" name="animal_<?=$x?>" id="animal_<?=$x?>" class="form-control" onKeyPress="pesquisar_animal_conjunta(this.value,<?=$x?>)">
            </div>
            <div id="lista_animal_<?=$x?>" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
            </div>
          </div>

          <div class="col-md-6" style="margin-left:-13px; margin-top:2%">
            <div class="form-group">
              <label for="exampleInputPassword1">Valor <?=$x?><span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="preco_conjunto_<?=$x?>" name="preco_conjunto_<?=$x?>" onKeyDown="preco_conjunto(<?=$x?>)" onblur="calcular_valor()"/>
            </div>
          </div>
        <? $x++; } ?>
        </div>
			</div>
      <!-- /.col -->
  </div>

    <div class="col-md-8">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">

          <div class="row">
            <div class="col-md-12">
              <div class="box-body">
                <span style="color:red;"> <? if($animal[0]['status'] == 1){ echo "Cuidado! Esse animal está morto";} ?> </span>
                <span style="color:red;"> <? if($animal[0]['status'] == 3){ echo "Cuidado! Esse animal foi emprestado";} ?> </span>
                <span style="color:red;"> <? if($animal[0]['status'] == 4){ echo "Cuidado! Esse animal foi doado";} ?> </span>
                <span style="color:red;"> <? if($animal[0]['status'] == 5){ echo "Cuidado! Esse animal foi abatido";} ?> </span>
              </div>
            </div>

          <div class="col-md-4">
            <!-- general form elements -->
            <div class="box-body">
              <div class="form-group">
                <label for="exampleInputPassword1">Comprador<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="comprador" name="comprador" value="<?=$comprador[0]['nome']?>" onKeyUp="pesquisar_comprador(this.value)">
                <div id="lista_comprador" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:90%; display:none; margin-top:1%;">
                </div>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Parcelas</label>
                <select class="form-control select" id="parcelas" name="parcelas">
                  <? if($venda[0]['parcelas']){ ?> <option value="<?=$venda[0]['parcelas']?>"><?=$venda[0]['parcelas']?>x</option><? } ?>
                  <option value="1">1x</option>
                  <option value="2">2x</option>
                  <option value="3">3x</option>
                  <option value="4">4x</option>
                  <option value="5">5x</option>
                  <option value="6">6x</option>
                  <option value="7">7x</option>
                  <option value="8">8x</option>
                  <option value="9">9x</option>
                  <option value="10">10x</option>
                  <option value="11">11x</option>
                  <option value="12">12x</option>
                  <option value="13">13x</option>
                  <option value="14">14x</option>
                  <option value="15">15x</option>
                  <option value="16">16x</option>
                  <option value="17">17x</option>
                  <option value="18">18x</option>
                  <option value="19">19x</option>
                  <option value="20">20x</option>
                  <option value="21">21x</option>
                  <option value="22">22x</option>
                  <option value="23">23x</option>
                  <option value="24">24x</option>
                </select>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Observações</label>
                <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="6" style="height:135px; width:100%;"><?=$venda[0]['observacoes']?></textarea>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <!-- general form elements -->
            <div class="box-body">
              <div class="form-group">
                <label for="exampleInputPassword1">Data da venda<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data" name="data" value="<?=$data_venda?>">
                </div>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Tipo de venda<span style="color:#F00;">*</span></label>
                <select class="form-control select" id="tipo_venda" name="tipo_venda">
                  <? if($venda[0]['tipo_venda'] != ""){ ?><option value="<?=$venda[0]['tipo_venda']?>"><?=$venda[0]['tipo_venda']?></option> <? }else{?> <option value="">Selecionar</option><? } ?>
                  <option></option>
                  <option value="Fazenda">Fazenda</option>
                  <option value="Leilão">Leilão</option>
                  <option value="Exposição">Exposição</option>
                  <option value="Virtual">Virtual</option>
                </select>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Valor total<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="valor_total" name="valor_total" value="<?=$venda[0]['preco_de_venda']*100?>" readonly="true">
              </div>

            </div>
          </div>


          <div class="col-md-4">
            <!-- general form elements -->
            <div class="box-body">
              <div class="form-group">
                <label for="exampleInputPassword1">Forma de pagamento</label>
                <select class="form-control select" id="forma" name="forma">
                  <? if($venda[0]['forma_de_pagamento']){ ?><option value="<?=$venda[0]['forma_de_pagamento']?>"><?=$venda[0]['forma_de_pagamento']?></option> <? }else{?> <option value="">Selecionar</option><? } ?>
                  <option></option>
                  <option value="Boleto">Boleto</option>
                  <option value="Cheque">Cheque</option>
                  <option value="Dinheiro">Dinheiro</option>
                  <option value="Depósito">Depósito</option>
                  <option value="Transferência">Transferência</option>
                  <option value="Troca">Troca</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-success" style="margin-top:6%; width:100%;">Finalizar venda</button>
            </div>
          </div>

          </div>
          </form>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
