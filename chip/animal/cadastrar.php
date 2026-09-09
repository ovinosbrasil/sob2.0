<script type="text/javascript">
function palco_cadastro(id_animal){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "chip/animal/palco_cadastro.php?id_animal="+id_animal;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("palco_cadastro").innerHTML = resposta;
document.getElementById("palco_cadastro").style.display = 'block';
document.getElementById("transparencia").style.display = 'block';
document.getElementById("chip").focus();
}
}
PP.send(null);
}

function fechar_palco(){
  document.getElementById("palco_cadastro").style.display = 'none';
  document.getElementById("transparencia").style.display = 'none';
}

function pesquisar_animal_chip(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "chip/animal/tabela_animais.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("tabela_animais").innerHTML = resposta;
document.getElementById("tabela_animais").style.display = 'block';
}
}
PP.send(null);
}
</script>



<section class="content-header">
  <h1>
  Cadastrar chip
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-github-alt"></i> Chip</a></li>
    <li><a href="#">Cadastrar</a></li>
  </ol>
</section>

<div class="row" id="palco_cadastro" style=" z-index:9999999999;  left:40%; top:5%;  position:absolute; position:fixed;"></div>


<section class="content">
  <div class="row">
    <div class="col-md-12">
      	<div class="box box-success">
          <div class="box-body">
      <!-- Custom Tabs -->

      <div class="col-md-4">
          <div class="form-group">
            <label for="exampleInputPassword1">Pesquisar animal</label>
              <input type="text" name="animal" class="form-control" id="animal" name="animal" onKeyUp="pesquisar_animal_chip(this.value)">
          </div>
      </div>

      <div class="col-md-12">
          <div class="form-group" id="tabela_animais">
          <table class="table table-bordered" id="tabela_padrao" width="70%">
              <tr>
                <th>Animal</th>
                <th>Sexo</th>
                <th>Data de nascimento</th>
                <th>Fbb</th>
                <th>Status</th>
              </tr>
              <?
              $animal = DBRead('animais',"WHERE chip > '0'");
              foreach ($animal as $animais) {
                $data = $animais['data_de_nascimento'];
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
                $data_nascimento = $data;
              ?>
              <tr>
                <td><?=$animais['nome']?></td>
                <td><?=$animais['sexo']?></td>
                <td><?=$data_nascimento?></td>
                <td><?=$animais['fbb']?></td>
                <td>
                  <?
                  if($animais['chip'] < 1){ ?>
                    <button type="button" class="btn btn-success" onclick="palco_cadastro(<?=$animais['id']?>)">Cadastrar chip</button>
                  <?  }else{ ?>
                  <a href="chip/animal/_excluir.php?id_animal=<?=$animais['id']?>"><button type="button" class="btn btn-warning" >Excluir chip</button></a>
                <? } ?>
                </td>
              </tr>
              <? } ?>
            </table>
          </div>
      </div>
    </div>
</div>
</section>


