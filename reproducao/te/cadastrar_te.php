<script type="text/javascript">
function validar_ter() {
  var campos = {lote:'Lote', data_inicial:'Data', pai:'Macho', mae:'Fêmea', embrioes:'Embriões coletados'};
  var faltantes = [], primeiro;
  Object.keys(campos).forEach(function (id) {
    var campo = document.getElementById(id);
    var vazio = !campo.value.trim();
    campo.style.borderColor = vazio ? '#dd4b39' : '';
    campo.setAttribute('aria-invalid', String(vazio));
    if (vazio) { faltantes.push(campos[id]); primeiro = primeiro || campo; }
  });
  if (faltantes.length) { SobAlertas.camposObrigatorios(faltantes); primeiro.focus(); return false; }
  return true;
}


</script>

<section class="content-header">
  <h1>
    Cadastrar transplante de embriões
  </h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-venus-mars"></i> Reprodução</li>
    <li><a href="geral.php?pg=lista_te">Transplante de embriões</a></li>
    <li class="active">Cadastrar lote</li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
				<div class="box" style="border-top:0;">
          <form method="post" action="reproducao/te/_cadastrar.php" onsubmit="return validar_ter()" novalidate>
          <!-- /.box-header -->
          <div class="box-body">
            <h2 class="box-title" style="font-size:16px; margin:0 0 20px;">Dados do lote</h2>
            <div class="row">
            <div class="form-group col-sm-6 col-md-4">
                <label for="lote">Lote<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lote" name="lote" required>
            </div>


            <div class="form-group col-sm-6 col-md-4">
                <label for="data_inicial">Data<span class="text-danger">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" placeholder="dd/mm/aaaa" required>
                </div>
            </div>


            <div class="form-group col-sm-6 col-md-4">
                <label for="embrioes">Embriões coletados<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="embrioes" name="embrioes" min="0" step="1" required>
            </div>

            <div class="form-group col-sm-6 col-md-4" style="position:relative;">
              <label for="pai">Macho<span class="text-danger">*</span></label>
              <div class="input-group">
              <input type="text" class="form-control" id="pai" name="macho" onKeyUp="pesquisar_pai(this.value)" value="<?=$user[0]['prefixo']?>" required>
                <span class="input-group-btn"><a class="btn btn-success" href="geral.php?pg=cadastrar_animal&amp;tipo=2" target="_blank" rel="noopener" title="Cadastrar animal em nova aba">Novo</a></span>
              </div>
              <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;">
              </div>
            </div>

            <div class="form-group col-sm-6 col-md-4" style="position:relative;">
              <label for="macho_complementar">Macho complementar</label>
              <input type="text" class="form-control" id="macho_complementar" name="macho_complementar" autocomplete="off" oninput="pesquisar_pai_2(this.value)">
              <input type="hidden" id="id_pai_2" name="id_pai_2" value="0">
              <input type="hidden" id="terceiro_pai_2" name="terceiro_pai_2" value="0">
              <div id="lista_pai_2" style="border:1px solid #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;"></div>
            </div>

            <div class="form-group col-sm-6 col-md-4" style="position:relative;">
              <label for="mae">Fêmea<span class="text-danger">*</span></label>
              <div class="input-group">
              <input type="text" class="form-control" id="mae" name="femea" onKeyUp="pesquisar_mae(this.value)" value="<?=$user[0]['prefixo']?>" required>
                <span class="input-group-btn"><a class="btn btn-success" href="geral.php?pg=cadastrar_animal&amp;tipo=2" target="_blank" rel="noopener" title="Cadastrar animal em nova aba">Novo</a></span>
              </div>
              <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:100%; display:none; margin-top:1%;">
              </div>
            </div>


            </div>
        </div>
        <div class="box-footer text-right">
          <a class="btn btn-default" href="geral.php?pg=lista_te">Cancelar</a>
          <button type="submit" class="btn btn-success">Cadastrar lote</button>
        </div>
      </form>
			</div>
      <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->

<script src="reproducao/te/macho_complementar.js"></script>
