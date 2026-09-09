<?
$id_animal = $_GET['id_animal'];
?>
  <!-- left column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Cadastrar Chip</h3>
      </div>
      <!-- /.box-header -->
      <form method="post" action="chip/animal/_cadastrar.php?id_animal=<?=$id_animal?>">
      <!-- form start -->
        <div class="box-body">
            <div class="timeline-body" style="margin-bottom:10px;">
              Por favor, aproxime o leitor no chip do animal.
            </div>
            <input type="text" name="chip" class="form-control" id="chip">
          </div>
        </form>
        <div class="timeline-footer">
            <button type="submit" class="btn btn-danger" onclick="fechar_palco()">Fechar</button>
        </div>
    </div>
    <!-- /.col -->
</div>
  <!-- /.row -->
