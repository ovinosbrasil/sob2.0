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


  if(!document.getElementById("vacina").value){
    document.getElementById("vacina").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("vacina").style.border = "1px solid green";}


  if(saida){ return false; }else{ return true; }
}

function atualizar_vacina(x){
  if(x == 'x'){
    document.getElementById("nova_vacina_").style.display = 'block';
  }
}

</script>

<section class="content-header">
  <h1>
    Cadastrar Monta natural
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-eyedropper"></i> Vacinas</a></li>
    <li><a href="#">Cadastrar</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="vacinas/_cadastrar.php" onsubmit="return validar_montar()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Lote<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="lote" name="lote">
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Vacina<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="vacina" name="vacina" onchange="atualizar_vacina(this.value)">
                <option value="">Selecionar</option>
                <option></option>
                <?
                $vacina = DBRead('vacina', "ORDER BY nome asc");
                foreach ($vacina as $vacina_) { ?>
                  <option value="<?=$vacina_['id']?>"><?=$vacina_['nome']?></option>
                <? } ?>
                <option style="color:green;" value="x">Cadastrar nova vacina</option>
              </select>
            </div>

            <div class="form-group" id="nova_vacina_" style="display:none;">
              <label for="exampleInputPassword1">Nova vacina<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="nova_vacina" name="nova_vacina">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data inicial<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial">
                </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-success" style="width:100%; margin-top:4%;">Cadastrar lote de vacina</button>
            </div>
        </div>
      </form>
			</div>
      <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
