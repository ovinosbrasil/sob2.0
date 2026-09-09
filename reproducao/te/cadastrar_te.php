<script type="text/javascript">
function validar_ter(){
  saida = 0;
  if(!document.getElementById("lote").value){
    document.getElementById("lote").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("lote").style.border = "1px solid green";}

  if(!document.getElementById("data_inicial").value){
    document.getElementById("data_inicial").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_inicial").style.border = "1px solid green";}

  if(!document.getElementById("pai").value){
    document.getElementById("pai").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("pai").style.border = "1px solid green";}

  if(!document.getElementById("mae").value){
    document.getElementById("mae").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("mae").style.border = "1px solid green";}


  if(!document.getElementById("embrioes").value){
    document.getElementById("embrioes").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("embrioes").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

</script>

<section class="content-header">
  <h1>
    Cadastrar Transplante de embriões
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Cadastra Transplante de embriões</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="reproducao/te/_cadastrar.php" onsubmit="return validar_ter()">
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
              <label for="exampleInputPassword1">Fêmea<span style="color:#F00;">*</span>
                <a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"><span style="font-size:11px; color:green;">Novo</span></a></label>
              <input type="text" class="form-control" id="mae" name="femea" onKeyUp="pesquisar_mae(this.value)" value="<?=$user[0]['prefixo']?>">
              <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
              </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Embriões coletados<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="embrioes" name="embrioes">
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
