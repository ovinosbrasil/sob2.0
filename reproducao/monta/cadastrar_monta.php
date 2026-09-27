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

  if(!document.getElementById("data_final").value){
    document.getElementById("data_final").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_final").style.border = "1px solid green";}

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
    Cadastrar Monta natural
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Cadastrar Monta natural</a></li>
  </ol>
</section>

  <section class="content">
    <div class="box" style="border-top:0;">
      <form method="post" action="reproducao/monta/_cadastrar.php" onsubmit="return validar_montar()">
        <div class="box-body">
          <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
            <div class="col-sm-6 col-md-4">
              <div class="form-group">
                <label for="lote">Lote<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lote" name="lote">
              </div>
            </div>

            <div class="col-sm-6 col-md-4">
              <div class="form-group">
                <label for="data_inicial">Data inicial<span class="text-danger">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial">
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-md-4">
              <div class="form-group">
                <label for="data_final">Data final<span class="text-danger">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                  <input type="text" class="form-control pull-right" id="data_final" name="data_final">
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-md-4">
              <div class="form-group" style="position:relative;">
                <label for="pai">Macho<span class="text-danger">*</span>
                  <a href="geral.php?pg=cadastrar_animal&amp;tipo=2" target="_blank" rel="noopener"><span style="font-size:11px; color:green;">Novo</span></a>
                </label>
                <input type="text" class="form-control" id="pai" name="macho" onkeyup="pesquisar_pai(this.value)" value="<?=htmlspecialchars($user[0]['prefixo'], ENT_QUOTES, 'UTF-8')?>">
                <div id="lista_pai" style="border:1px solid #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;"></div>
              </div>
            </div>

            <div class="col-sm-6 col-md-4">
              <div class="form-group">
                <label for="raca">Raça<span class="text-danger">*</span></label>
                <select class="form-control select" id="raca" name="raca">
                  <option value="<?=htmlspecialchars($user[0]['raca'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($user[0]['raca'], ENT_QUOTES, 'UTF-8')?></option>
                  <?php
                  $raca = DBRead('raca', 'ORDER BY nome ASC') ?: array();
                  foreach ($raca as $raca_) { ?>
                    <option value="<?=htmlspecialchars($raca_['nome'], ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars($raca_['nome'], ENT_QUOTES, 'UTF-8')?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-6 col-md-4">
              <div class="form-group">
                <label for="notificacao">Notificação<span class="text-danger">*</span></label>
                <select class="form-control select" id="notificacao" name="notificacao">
                  <option value="">Selecionar</option>
                  <option value="PO">PO</option>
                  <option value="PC">PC</option>
                </select>
              </div>
            </div>

            <div class="col-sm-12 text-right">
              <button type="submit" class="btn btn-success">Cadastrar lote</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
