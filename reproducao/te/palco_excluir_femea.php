<?
$id_controle = $_GET['id_controle'];
$id_lote = $_GET['id_lote'];
?>
  <!-- left column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-danger">
      <div class="box-header with-border">
        <h3 class="box-title">Excluir fêmea do lote</h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
        <div class="box-body">
            <div class="timeline-body" style="margin-bottom:10px;">
              Você deseja realmente excluir essa fêmea do lote e todas as informações ligadas a ele?
            </div>
            <div class="timeline-footer">
              <button type="submit" class="btn btn-success" onclick="ativar_excluir_femea(<?=$id_controle?>,<?=$id_lote?>)">Sim</button>
                <button type="submit" class="btn btn-danger" onclick="fechar_excluir_femea()">Não</button>
            </div>
          </div>
    </div>
    <!-- /.col -->
</div>
  <!-- /.row -->
