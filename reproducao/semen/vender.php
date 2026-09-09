<?
$id_embriao = $_GET['id_embriao'];
$embriao = DBRead('semen', "WHERE id = '$id_embriao'");

$id_macho = $embriao[0]['id_animal'];
if($embriao[0]['terceiro']){
  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
}else{
  $macho = DBRead('animais', "WHERE id = '$id_macho'");
}

?>

<script type="text/javascript">
function ativar_venda(){
  saida = 0;
	if(!document.getElementById("comprador").value){
    document.getElementById("comprador").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("comprador").style.border = "1px solid green";}

  if(!document.getElementById("data").value){
    document.getElementById("data").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data").style.border = "1px solid green";}

  if(document.getElementById("valor").value == ''){
    document.getElementById("valor").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("valor").style.border = "1px solid green";}

  if(!document.getElementById("tipo_venda").value){
    document.getElementById("tipo_venda").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("tipo_venda").style.border = "1px solid green";}

  if(!document.getElementById("embriao").value){
    document.getElementById("embriao").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("embriao").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

</script>


<section class="content-header">
  <h1>
    Vender Sêmen
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Reprodução</a></li>
    <li><a href="#">Vendas sêmen</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <form role="form" action="reproducao/semen/_vender.php?id_embriao=<?=$id_embriao?>" method="post" onsubmit="return ativar_venda()">
    <div class="row">


    <div class="col-md-12">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">

          <div class="row">

          <div class="col-md-4">
            <!-- general form elements -->
            <div class="box-body">

              <div class="form-group">
                <label for="exampleInputPassword1">Sêmen<span style="color:#F00;">*</span></label>
                <select class="form-control select" id="embriao" name="embriao">
                 <option value="<?=$embriao[0]['id']?>"><?=$macho[0]['nome']?></option>
                </select>

              </div>


              <div class="form-group">
                <label for="exampleInputPassword1">Comprador<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="comprador" name="comprador" value="<?=$comprador[0]['nome']?>" onKeyUp="pesquisar_comprador(this.value)">
                <div id="lista_comprador" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:90%; display:none; margin-top:1%;">
                </div>
              </div>

              <div class="col-md-6" style="margin-left:-13px;">
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
              </div>

              <div class="col-md-6" style="margin-left:-13px;">
              <div class="form-group">
                <label for="exampleInputPassword1">Quantidade de doses</label>
                <input type="text" class="form-control" id="qtd" name="qtd" onkeyup="somenteNumeros(this);">
              </div>
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
                  <input type="text" class="form-control pull-right" id="data" name="data">
                </div>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Tipo de venda<span style="color:#F00;">*</span></label>
                <select class="form-control select" id="tipo_venda" name="tipo_venda">
                  <option value="">Selecionar</option>
                  <option></option>
                  <option value="Fazenda">Fazenda</option>
                  <option value="Leilão">Leilão</option>
                  <option value="Exposição">Exposição</option>
                  <option value="Virtual">Virtual</option>
                </select>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Valor<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="valor" name="valor">
              </div>

            </div>
          </div>


          <div class="col-md-4">
            <!-- general form elements -->
            <div class="box-body">
              <div class="form-group">
                <label for="exampleInputPassword1">Forma de pagamento</label>
                <select class="form-control select" id="forma" name="forma">
                <option value="">Selecionar</option>
                  <option></option>
                  <option value="Boleto">Boleto</option>
                  <option value="Cheque">Cheque</option>
                  <option value="Dinheiro">Dinheiro</option>
                  <option value="Depósito">Depósito</option>
                  <option value="Transferência">Transferência</option>
                  <option value="Troca">Troca</option>
                </select>
              </div>

              <div class="form-group">
                <label for="exampleInputPassword1">Observações</label>
                <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="5" style="height:10-0px; width:100%;"></textarea>
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-success" style="margin-top:1%; width:100%;">Finalizar venda</button>
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
