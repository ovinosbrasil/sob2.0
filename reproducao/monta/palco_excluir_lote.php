<?
$id_lote = $_GET['id_lote'];
?>
  <!-- left column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-danger">
      <div class="box-header with-border">
        <h3 class="box-title">Excluir lote</h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
        <div class="box-body">
            <div class="timeline-body" style="margin-bottom:10px;">
              Você deseja realmente excluir esse lote e todas as informações ligadas a ele?
            </div>
            <div class="timeline-footer">
              <button type="submit" class="btn btn-success" onclick="ativar_excluir_lote(<?=$id_lote?>)">Sim</button>
                <button type="submit" class="btn btn-danger" onclick="fechar_excluir_lote()">Não</button>
            </div>
          </div>
    </div>
    <!-- /.col -->
</div>
  <!-- /.row -->
