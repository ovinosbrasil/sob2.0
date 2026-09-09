<?
include "../../_config.php";
$id_embriao = $_GET['id_embriao'];
$embriao = DBRead('semen', "WHERE id = '$id_embriao'");
?>

<!-- left column -->
<div class="col-md-12">
  <!-- general form elements -->
  <div class="box box-danger">
    <div class="box-header with-border">
      <h3 class="box-title">Alterar quantidade de sêmen</h3>
    </div>
    <!-- /.box-header -->

    <!-- form start -->
      <div class="box-body">
        <form method="post" action="reproducao/semen/_alterar.php?id_embriao=<?=$id_embriao?>">

        <div class="form-group">
            <label for="exampleInputPassword1">Quantidade<span style="color:#F00;">*</span></label>
            <input type="text" class="form-control" id="qtd" name="qtd" value="<?=$embriao[0]['qtd']?>">
        </div>

        <div class="form-group">
          <button type="button" class="btn btn-danger" style="margin-right:30%;" onclick="fechar_alterar()">Fechar</button>
          <button type="submit" class="btn btn-warning" style="">Alterar</button>
        </div>
      </form>
        </div>
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
