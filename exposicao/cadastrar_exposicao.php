<script type="text/javascript">
function validar(){
  saida = 0;
  if(!document.getElementById("evento").value){
    document.getElementById("evento").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("evento").style.border = "1px solid green";}

  if(!document.getElementById("data").value){
    document.getElementById("data").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

</script>

<section class="content-header">
  <h1>
    Cadastrar Exposição
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-trophy"></i> Exposição</a></li>
    <li><a href="#">Cadastrar</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="exposicao/_cadastrar.php" onsubmit="return validar()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Evento<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="evento" name="evento">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data" name="data">
                </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Cidade</label>
                  <input type="text" class="form-control" id="cidade" name="cidade">
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Local</label>
                <input type="text" class="form-control" id="local" name="local">
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-success" style="width:100%; margin-top:4%;">Cadastrar Exposição</button>
            </div>
        </div>
      </form>
			</div>
      <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
