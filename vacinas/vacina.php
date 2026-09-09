<? $id_lote = $_GET['id_lote']; ?>

<script type="text/javascript">
function focus(){
  document.getElementById("animal").focus();
}

function validar_vacina(){
  saida = 0;
  if(!document.getElementById("lote").value){
    document.getElementById("lote").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("lote").style.border = "1px solid green";}

  if(!document.getElementById("data_inicial").value){
    document.getElementById("data_inicial").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_inicial").style.border = "1px solid green";}

  if(!document.getElementById("vacina").value){
    document.getElementById("vacina").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("vacina").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}


function excluir_vacina(id_vacina){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "vacinas/palco_excluir_vacina.php?id_vacina="+id_vacina;
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

function fechar_excluir_vacina(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_vacina(id_vacina){
    window.location.href = "vacinas/_excluir_vacina.php?id_vacina="+id_vacina+"&id_lote=<?=$id_lote?>";
}

function pesquisar_animal_vacina(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "vacinas/lista_animal.php?nome="+nome+"&id_lote=<?=$id_lote?>";
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_animal_vacina").innerHTML = resposta;
document.getElementById("lista_animal_vacina").style.display = 'block';
}
}
PP.send(null);
}

function linkar_animal_vacina(nome){
  document.getElementById("animal").value = nome;
  document.getElementById("lista_animal_vacina").style.display = 'none';
}

function fechar_lista_animal_vacina(){
  document.getElementById("lista_animal_vacina").style.display = 'none';
}

function ativar_animal(){
  saida = 0;
  if(!document.getElementById("animal").value){
    document.getElementById("animal").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("animal").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

</script>

<?
$id_lote = $_GET['id_lote'];
$lote = DBRead('lote_vacina', "WHERE id = '$id_lote'");

$data = $lote[0]['data'];
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

$id_vacina = $lote[0]['id_vacina'];
$vacina = DBRead('vacina', "WHERE id = '$id_vacina'");

?>


<section class="content-header">
  <h1>
    Exibir lote de vacinação
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-eyedropper"></i> Vacinas</a></li>
    <li><a href="#">Vacina</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="vacinas/_alterar.php?id_lote=<?=$id_lote?>" onsubmit="return validar_vacina()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Lote<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="lote" name="lote" value="<?=$lote[0]['nome']?>">
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Vacina<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="vacina" name="vacina" onchange="atualizar_vacina(this.value)">
                <option value="<?=$vacina[0]['id']?>"><?=$vacina[0]['nome']?></option>
                <option></option>
                <?
                $vacina = DBRead('vacina', "ORDER BY nome asc");
                foreach ($vacina as $vacina_) { ?>
                  <option value="<?=$vacina_['id']?>"><?=$vacina_['nome']?></option>
                <? } ?>
                <option style="color:green;" value="x">Cadastrar nova vacina</option>
              </select>
            </div>

            <div class="form-group" id="nova_vacina_" style="display:none;">
              <label for="exampleInputPassword1">Nova vacina<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="nova_vacina" name="nova_vacina">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data inicial<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$data?>">
                </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-warning" style="width:100%; margin-top:4%;">Alterar lote de vacina</button>
            </div>
        </div>
      </form>
			</div>
      <!-- /.col -->
    </div>


    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <form method="post" action="vacinas/_cadastrar_animal.php?id_lote=<?=$id_lote?>" onsubmit="return ativar_animal()">
          <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputPassword1">Adicionar Animal<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="animal" name="animal" value="<?=$mae[0]['nome']?>" onkeypress="pesquisar_animal_vacina(this.value)">
                <div id="lista_animal_vacina" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
                </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <button type="submit" class="btn btn-success" style="width:100%; margin-top:8%;">Adicionar Animal</button>
            </div>
          </div>
        </form>

        <div class="col-md-10">
          <table class="table table-bordered" id="tabela_padrao" >
            <tr>
              <th style="width:10%;">Nº</th>
              <th>Animal</th>
              <th style="width:3%;">Excluir</th>
            </tr>
            <?
            $vacina = DBRead('vacinas', "WHERE id_lote = '$id_lote'");
            foreach ($vacina as $vacina_){
              $x++;
              $id_animal = $vacina_['id_animal'];
              $animal = DBRead('animais', "WHERE id = '$id_animal'");

            ?>
            <tr>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animal[0]['id']?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
              <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_vacina(<?=$vacina_['id']?>)">X</button></td>
              </tr>
            <? } ?>
            </table>
        </div></div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
