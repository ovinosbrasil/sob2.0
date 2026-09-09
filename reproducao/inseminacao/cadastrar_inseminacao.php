<script type="text/javascript">
function validar_montar(){
  saida = 0;
  if(!document.getElementById("lote").value){
    document.getElementById("lote").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("lote").style.border = "1px solid green";}

  if(!document.getElementById("data_inicial").value){
    document.getElementById("data_inicial").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_inicial").style.border = "1px solid green";}

  if(!document.getElementById("semen").value){
    document.getElementById("semen").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("semen").style.border = "1px solid green";}

  if(!document.getElementById("pai").value){
    document.getElementById("pai").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("pai").style.border = "1px solid green";}

  if(!document.getElementById("raca").value){
    document.getElementById("raca").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("raca").style.border = "1px solid green";}

  if(!document.getElementById("notificacao").value){
    document.getElementById("notificacao").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("notificacao").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

</script>

<section class="content-header">
  <h1>
    Cadastrar Inseminação artificial
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Cadastra Inseminação artificial</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="reproducao/inseminacao/_cadastrar.php" onsubmit="return validar_montar()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Lote<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="lote" name="lote">
            </div>


            <div class="form-group">
                <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial">
                </div>
            </div>


            <div class="form-group">
              <label for="exampleInputPassword1">Macho<span style="color:#F00;">*</span>
                <a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"><span style="font-size:11px; color:green;">Novo</span></a></label>
              <input type="text" class="form-control" id="pai" name="macho" onKeyUp="pesquisar_pai(this.value)" value="<?=$user[0]['prefixo']?>">
              <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
              </div>
            </div>


            <div class="form-group">
              <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="raca" name="raca">
                <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option>
                <option></option>
                <?
                $raca = DBRead('raca', "ORDER BY nome asc");
                foreach ($raca as $raca_) { ?>
                  <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                <? } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Sêmen<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="semen" name="semen">
                <option value="">Selecionar</option>
                <option value=""></option>
                <option value="A fresco">A fresco</option>
                <option value="Congelado">Congelado</option>
                <option value="Refrigerado">Refrigerado</option>
              </select>
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Notificação<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="notificacao" name="notificacao">
                <option value="">Selecionar</option>
                <option></option>
                <option value="PO">PO</option>
                <option value="PC">PC</option>
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-success" style="width:100%; margin-top:4%;">Cadastrar lote</button>
            </div>
        </div>
      </form>
			</div>
      <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
