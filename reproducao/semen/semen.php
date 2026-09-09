<script type="text/javascript">
function validar(){
  saida = 0;
  if(!document.getElementById("macho").value){
    document.getElementById("macho").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("macho").style.border = "1px solid green";}

  if(!document.getElementById("data").value){
    document.getElementById("data").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data").style.border = "1px solid green";}

  if(!document.getElementById("qtd").value){
    document.getElementById("qtd").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("qtd").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}


function pesquisar_pai_monta(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/monta/lista_pai.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_pai").innerHTML = resposta;
document.getElementById("lista_pai").style.display = 'block';
}
}
PP.send(null);
}

function fechar_lista_pai(){
  document.getElementById("lista_pai").style.display = 'none';
}

function linkar_pai_monta(nome){
  document.getElementById("lista_pai").style.display = 'none';
  document.getElementById("macho").value = nome;
}


function atualizar_embriao(tipo, id_embriao){
  if(tipo == 1){
    alterar_embriao(id_embriao);
  }
  if(tipo == 2){
    excluir_embriao(id_embriao);
  }
  if(tipo == 3){
    window.location.href = "geral.php?pg=vender_semen&id_embriao="+id_embriao;
  }
}

function excluir_embriao(id_embriao){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/semen/palco_excluir.php?id_embriao="+id_embriao;
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

function fechar_excluir(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir(id_embriao){
    window.location.href = "reproducao/semen/_excluir.php?id_embriao="+id_embriao;
}

function alterar_embriao(id_embriao){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/semen/palco_alterar.php?id_embriao="+id_embriao;
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

function fechar_alterar(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}
</script>


<section class="content-header">
  <h1>
    Banco de Sêmen
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Sêmen</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <form method="post" action="reproducao/semen/_cadastrar.php" onsubmit="return validar()">
            <div class="form-group">
                <label for="exampleInputPassword1">Macho<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="macho" name="macho" value="<?=$pai?>" onKeyUp="pesquisar_pai_monta(this.value)">
                <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
            </div>
          </div>


          <div class="form-group">
              <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="data" name="data">
              </div>
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">Quantidade<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="qtd" name="qtd">
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">Botijão</label>
              <input type="text" class="form-control" id="botijao" name="botijao">
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">ID Palheta</label>
              <input type="text" class="form-control" id="palheta" name="palheta">
          </div>

          <div class="form-group">
              <label for="exampleInputPassword1">Qualidade</label>
              <input type="text" class="form-control" id="qualidade" name="qualidade">
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-success" style="width:100%;">Adicionar Sêmen</button>
          </div>
        </form>
      </div>
    </div>
  <!-- /.box-body -->
</div>
<!-- /.col -->



      <div class="col-md-9">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">

            <div class="form-group" style="float:right;">
              <a href="geral.php?pg=vendas_semen"><button type="button" class="btn btn-primary">Lista de vendas</button></a>
            </div>

            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Data</th>
                <th>Macho</th>
                <th>Quantidade</th>
                <th>Botijão</th>
                <th>ID Palheta</th>
                <th>Qualidade</th>
                <th>Funções</th>
              </tr>
              <?
                $embriao = DBRead('semen', "WHERE qtd > 0 ORDER BY data desc");
                foreach ($embriao as $embriao_){
                $id_macho = $embriao_['id_animal'];
                if($embriao_['terceiro']){
                  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
                }else{
                  $macho = DBRead('animais', "WHERE id = '$id_macho'");
                }

                $data = $embriao_['data'];
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
                ?>
                <tr>
                    <td><?=$data?></td>
                    <td><?=$macho[0]['nome']?></td>
                    <td><?=$embriao_['qtd']?></td>
                    <td><?=$embriao_['botijao']?></td>
                    <td><?=$embriao_['palheta']?></td>
                    <td><?=$embriao_['qualidade']?></td>
                    <td>
                      <select name="" id="" onchange="atualizar_embriao(this.value,<?=$embriao_['id']?>)" style="margin:0%; padding:0%; height:30px;">
                      <option value="">Selecionar</option>
                      <option value=""></option>
                      <option value="1">Alterar</option>
                      <option value="3">Vender</option>
                      <option value="2">Excluir</option>
                    </select>
                  </td>
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
