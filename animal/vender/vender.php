<script type="text/javascript">
function desfazer_venda2(){
    window.location.href = "animal/vender/_desfazer_venda.php?id_animal=<?=$id_animal?>";
}


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
</script>

<?
$venda = DBRead('vendas', "WHERE id_animal = '$id_animal'");
if($venda[0]['id'] > 0){
$data = $venda[0]['data'];
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
$data_venda = $data;


$comprador = $venda[0]['comprador'];
$comprador = DBRead('mercado', "WHERE id = '$comprador'");
}
?>
<form role="form" action="animal/vender/_vender.php?id_animal=<?=$id_animal?>" method="post" onsubmit="return ativar_venda()">
<div class="row">
  <div class="col-md-12">
    <div class="box-body">
      <span style="color:red;"> <? if($animal[0]['status'] == 1){ echo "Cuidado! Esse animal está morto";} ?> </span>
      <span style="color:red;"> <? if($animal[0]['status'] == 3){ echo "Cuidado! Esse animal foi emprestado";} ?> </span>
      <span style="color:red;"> <? if($animal[0]['status'] == 4){ echo "Cuidado! Esse animal foi doado";} ?> </span>
      <span style="color:red;"> <? if($animal[0]['status'] == 5){ echo "Cuidado! Esse animal foi abatido";} ?> </span>
    </div>
  </div>

<div class="col-md-3">
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

<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group">
      <label for="exampleInputPassword1">Data da venda<span style="color:#F00;">*</span></label>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control pull-right" id="data_venda" name="data_venda" value="<?=$data_venda?>">
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
      <button type="submit" class="btn btn-success" style="margin-top:6%; width:100%;">Vender animal</button>
      <? if($animal[0]['status'] == 2){ ?><button type="button" class="btn btn-primary" style="margin-top:6%; width:100%;" onclick="desfazer_venda2()">Desfazer venda</button> <? } ?>
      <a href="http://187.9.216.98/website4/criador/comunicar.aspx" target="_blank"><button type="button" class="btn btn-warning" style="margin-top:6%; width:100%;">Transferir animal</button></a>
    </div>
  </div>
</div>


<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group">
      <label for="exampleInputPassword1">Valor<span style="color:#F00;">*</span></label>
      <input type="text" class="form-control" id="valor" name="valor" value="<?=$venda[0]['preco_de_venda']*100?>">
    </div>

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
</div>

</div>
</form>
