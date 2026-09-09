<?
$id_animal = $_GET['id_animal'];
$animal = DBRead('animais', "WHERE id = '$id_animal'");
$filtro = $_GET['filtro'];
?>

<script type="text/javascript">
function atualizar(x){
  window.location.href = "geral.php?pg=relatorio_reprodutor&id_animal=<?=$id_animal?>&filtro="+x;
}
</script>


<section class="content-header">
  <h1>
    Relatório completo (<?=$animal[0]['nome']?>)
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatórios</a></li>
    <li><a href="#">Reprodutores</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">

            <div class="col-md-3" style="margin-left:0.7%;">
              <div class="form-group">
                  <label for="exampleInputPassword1">Filtro:</label>
                  <select class="form-control select" id="filtro" name="filtro" onchange="atualizar(this.value)">
                  <? if($filtro == 1){?> <option value="1">Índice de progênie (Vendas)</option> <? } ?>
                  <? if($filtro == 2){?> <option value="2">Qualidade das crias</option> <? } ?>
                  <? if($filtro == 3){?> <option value="3">Mortalidade das crias</option> <? } ?>
                  <? if($filtro == 4){?> <option value="4">Ganho média diário (gmd)</option> <? } ?>
                  <? if($filtro == 5){?> <option value="5">Tipificação das crias</option> <? } ?>
                  <? if(!$filtro){ ?> <option value="">Selecionar</option> <? } ?>
                  <option value=""></option>
                  <option value="1">Índice de progênie (Vendas)</option>
                  <option value="2">Qualidade das crias</option>
                  <option value="3">Mortalidade das crias</option>
                  <option value="4">Ganho média diário (gmd)</option>
                  <option value="5">Tipificação das crias</option>
                  </select>
              </div>
            </div>


            <div class="col-md-12">
                <div class="box-body">
                  <? if($filtro == 1){ include "relatorios/reprodutor/tabela_venda.php"; }?>
                  <? if($filtro == 2){ include "relatorios/reprodutor/tabela_qualidade.php";}?>
                  <? if($filtro == 3){ include "relatorios/reprodutor/tabela_mortes.php";}?>
                  <? if($filtro == 4){ include "relatorios/reprodutor/tabela_gmd.php";}?>
                  <? if($filtro == 5){ include "relatorios/reprodutor/tabela_tipificacao.php";}?>
                </div>
            </div>

          </div>
        </div>
      </div>
</section>
  <!-- /.content -->
