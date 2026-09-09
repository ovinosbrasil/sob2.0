<script type="text/javascript">
function abrir_vacina(id_lote){
  window.location.href = "geral.php?pg=vacina&id_lote="+id_lote;
}

function excluir_lote_vacina(id_lote){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "vacinas/palco_excluir_lote.php?id_lote="+id_lote;
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

function fechar_excluir_lote(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_lote(id_lote){
    window.location.href = "vacinas/_excluir_lote.php?id_lote="+id_lote;
}
</script>

<section class="content-header">
  <h1>
    Pesquisar Vacinas
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-eyedropper"></i> Vacinas</a></li>
    <li><a href="#">Pesquisa</a></li>
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
                <label for="exampleInputPassword1">Lote de vacina</label>
                <select class="form-control select" onchange="abrir_vacina(this.value)" name="vacina" id="vacina">
                  <option value="">Selecionar</option>
                  <option value=""></option>
                  <?
                  $vacina = DBRead('lote_vacina', "ORDER BY id desc");
                  foreach ($vacina as $vacina_) {
                  ?>
                    <option value="<?=$vacina_['id']?>"><?=$vacina_['nome']?></option>
                  <? } ?>
                </select>
            </div>
        </div>
          <!-- /.box-body -->
			</div>
      <!-- /.col -->
    </div>


      <div class="col-md-9">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
              <a href="geral.php?pg=cadastrar_vacina" > <button type="submit" class="btn btn-success" style="float:right; margin-bottom:1%;">Adicionar novo lote</button> </a>
            </div>
            Últimos lotes cadastrados
            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Lote</th>
                <th>Vacina</th>
                <th>Data</th>
                <th>Animais</th>
                <th>Excluir</th>
              </tr>
              <?
                $lote = DBRead('lote_vacina', "ORDER BY id desc LIMIT 15");
                foreach ($lote as $lote_){
                $id_vacina = $lote_['id_vacina'];
                $vacina = DBRead('vacina', "WHERE id = '$id_vacina'");

                $data = $lote_['data'];
                $data_atual = $data;
                $data = '0';
                $data['0'] = $data_atual['8'];
                $data['1'] = $data_atual['9'];
                $data['2'] = "/";
                $data['3'] = $data_atual['5'];
                $data['4'] = $data_atual['6'];
                $data['5'] = "/";
                $data['6'] = $data_atual['0'];
                $data['7'] = $data_atual['1'];
                $data['8'] = $data_atual['2'];
                $data['9'] = $data_atual['3'];
                $data_fim = $data;

                $id_lote = $lote_['id'];
                $qtd = DBRead('vacinas', "WHERE id_lote = '$id_lote'");
                if($qtd[0]['id'] > 0){
                    $qtd = count($qtd);
                }else{
                    $qtd = 0;
                }

                ?>
                <tr>
                    <td onclick="abrir_vacina(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$lote_['nome']?></td>
                    <td onclick="abrir_vacina(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$vacina[0]['nome']?></td>
                    <td onclick="abrir_vacina(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$data?></td>
                    <td onclick="abrir_vacina(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$qtd?></td>
                    <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_lote_vacina(<?=$lote_['id']?>)">X</button></td>
                  </tr>
                <? } ?>
                </table>
          </div>
          <!-- /.box-body -->
        </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
</section>
  <!-- /.content -->
