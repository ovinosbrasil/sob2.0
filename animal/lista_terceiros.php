<?php
$sexo = $_GET['sexo'] ?? '';
if (!in_array($sexo, array('', 'Macho', 'Fêmea'), true)) {
  $sexo = '';
}
$filtroSexo = $sexo === '' ? '' : "WHERE sexo = '$sexo'";
$contagem = DBRead('terceiros', $filtroSexo, 'COUNT(*) AS total');
$qtd = (int)($contagem[0]['total'] ?? 0);
$qtd_pag = (int)ceil($qtd / 40);
$paginaInformada = filter_var($_GET['pag'] ?? 0, FILTER_VALIDATE_INT);
$pagina = min(max(0, $paginaInformada === false ? 0 : $paginaInformada), max(0, $qtd_pag - 1));
$loop = $pagina * 40;
$animais = $qtd > 0 ? (DBRead('terceiros', "$filtroSexo ORDER BY id desc LIMIT $loop,40") ?: array()) : array();
$sexoUrl = rawurlencode($sexo);
?>

<script type="text/javascript">
function atualizar(sexo){
    window.location.href = "geral.php?pg=lista_terceiros&sexo="+sexo;
}


function excluir_terceiro(id_animal){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "animal/palco_excluir_terceiro.php?id_animal="+id_animal;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_excluir").innerHTML = resposta;
}
}
PP.send(null);
document.getElementById("transparencia").style.display = 'block';
document.getElementById("palco_excluir").style.display = 'block';
}

function fechar_excluir_terceiro(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_terceiro(id_animal){
    window.location.href = "animal/_excluir_terceiro.php?id_animal="+id_animal;
}
</script>

<section class="content-header">
  <h1>
    Animais de Terceiros
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-github-alt"></i> Animais</a></li>
    <li><a href="#">Terceiros</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">

            <div class="form-group">
                <label for="exampleInputPassword1">Sexo<span style="color:#F00;">*</span></label>
                <select class="form-control select" id="filtro" name="filtro" onchange="atualizar(this.value)">
                  <option value="" <?=$sexo === '' ? 'selected' : ''?>>Todos</option>
                  <option value="Macho" <?=$sexo === 'Macho' ? 'selected' : ''?>>Macho</option>
                  <option value="Fêmea" <?=$sexo === 'Fêmea' ? 'selected' : ''?>>Fêmea</option>

                </select>
            </div>

      </div>
    </div>
      <!-- /.col -->
</div>

    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Sexo</th>
              <th>Pai</th>
              <th>Mãe</th>
              <th>Excluir</th>
            </tr>
            <?
            $x = $loop;
            if (!$animais) { ?>
            <tr><td colspan="6" class="text-center">Nenhum animal de terceiros encontrado.</td></tr>
            <?php }
            foreach ($animais as $animais_){
              $x++;
            ?>
            <tr>
              <td><?=$x?></td>
              <td onclick="abrir_terceiro(<?=$animais_['id']?>)" style="cursor:pointer;" ><?=$animais_['nome']?></td>
              <td><?=$animais_['sexo']?></td>
              <td><?=$animais_['pai']?></td>
              <td><?=$animais_['mae']?></td>
              <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_terceiro(<?=$animais_['id']?>)">X</button></td>
              </tr>
            <? } ?>
            </table>

            <?php if ($qtd_pag > 1) { ?>
            <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <?php if ($pagina > 0) { ?>
                <li><a href="geral.php?pg=lista_terceiros&amp;pag=<?=$pagina-1?>&amp;sexo=<?=$sexoUrl?>">&laquo;</a></li>
                <?php } ?>
                <?php for ($x = 0; $x < $qtd_pag; $x++) { ?>
                  <?php if ($x === $pagina) { ?>
                  <li class="active"><span><?=$x+1?></span></li>
                  <?php } else { ?>
                  <li><a href="geral.php?pg=lista_terceiros&amp;pag=<?=$x?>&amp;sexo=<?=$sexoUrl?>"><?=$x+1?></a></li>
                  <?php } ?>
                <?php } ?>
                <?php if ($pagina < $qtd_pag - 1) { ?>
                <li><a href="geral.php?pg=lista_terceiros&amp;pag=<?=$pagina+1?>&amp;sexo=<?=$sexoUrl?>">&raquo;</a></li>
                <?php } ?>
              </ul>
            </div>
            <?php } ?>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
