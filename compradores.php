
<section class="content-header">
  <h1>
    Cadastrar comprador
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Vendas</a></li>
    <li><a href="#">Comprador</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-4">
				<div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
          <form method="post" action="vendas/_comprador.php">
            <div class="form-group">
                <label for="exampleInputPassword1">Nome completo</label>
                <input type="text" class="form-control" id="nome" name="nome">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">E-mail</label>
                <input type="text" class="form-control" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Celular (WhatsApp)</label>
                <input type="text" class="form-control" id="celular" name="celular">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Cod. criador</label>
                <input type="text" class="form-control" id="cod" name="cod">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Cidade</label>
                <input type="text" class="form-control" id="cidade" name="cidade">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Estado</label>
                <select class="form-control select" name="estado" id="estado">
                  <option value="">Selecionar</option>
                  <option value=""></option>
                  <?
                  $estado = DBRead('estado', "ORDER BY estado asc");
                  foreach ($estado as $estado_) {
                  ?>
                    <option value="<?=$estado_['estado']?>"><?=$estado_['estado']?></option>
                  <? } ?>
                </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-success" style="width:100%; margin-top:4%;">Cadastrar comprador</button>
            </div>
          </form>
          </div>
        </div>
          <!-- /.box-body -->
			</div>
      <!-- /.col -->


      <div class="col-md-8">
          <div class="box box-success">
            <!-- /.box-header -->
        <form method="post" action="">
          <div class="box-body">
            <div class="col-md-4">
              <div class="form-group">
                  <label for="exampleInputPassword1">Pesquisar comprador</label>
                  <input type="text" class="form-control" id="comprador" name="comprador" value="<?=$mae?>" onKeyUp="pesquisar_comprador(this.value)">
                  <div id="lista_comprador" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:90%; display:none; margin-top:1%;">
                </div>
              </div>
            </div>

              <div class="col-md-4">
                <button type="submit" class="btn btn-primary" style="margin-top:4%;">Pesquisar</button>
              </div>
          </div>
        </form>

        </div>
        <!-- /.col -->
      </div>
    <!-- /.row -->
  </div>
</section>
  <!-- /.content -->
