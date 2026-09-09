
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
                  ?<? if($sexo != ''){ ?> <option value="<?=$sexo?>"><?=$sexo?></option><? } else{ ?>
                  <option value="">Todos</option><? } ?>
                  <option value=""></option>
                  <option value="Macho">Macho</option>
                  <option value="Fêmea">Fêmea</option>

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
            $loop = $_GET['pag'];
            $loop = $loop*40;
            $fim = $loop+40;
            $sexo = $_GET['sexo'];

            if($sexo == ''){ $animais = DBRead('terceiros', "ORDER BY id desc LIMIT $loop,40"); }else{
            $animais = DBRead('terceiros', "WHERE sexo = '$sexo' ORDER BY id desc LIMIT $loop,40"); }
            $x = $loop;
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

            <div class="box-footer clearfix">
            <ul class="pagination pagination-sm no-margin pull-right">
              <? $pag = $_GET['pag']+1; ?>
              <li><a href="geral.php?pg=lista_terceiros&pag=<?=$pag-2?>&sexo=<?=$sexo?>">&laquo;</a></li>

              <?
              if($sexo == ''){ $animais = DBRead('terceiros'); }else{
              $animais = DBRead('terceiros', "WHERE sexo = '$sexo'"); }
              $qtd = count($animais);
              $qtd_pag = $qtd/40;
              $x = 0;
              while($x < $qtd_pag){?>
                <? if($x+1 == $pag){ ?><li><a href="#"><span style="color:red;"><?=$x+1?></span></a></li>
              <? }else{ ?><li><a href="geral.php?pg=lista_terceiros&pag=<?=$x?>&sexo=<?=$sexo?>"><?=$x+1?></a></li><? } ?>
              <? $x++;} ?>
              <li><a href="geral.php?pg=lista_terceiros&pag=<?=$pag?>&sexo=<?=$sexo?>">&raquo;</a></li>
            </ul>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
