<script type="text/javascript">
function ativar_vacina(){
  saida = 0;
	if(!document.getElementById("vacina").value){
    document.getElementById("vacina").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("vacina").style.border = "1px solid green";}

  if(!document.getElementById("data_vacina").value){
    document.getElementById("data_vacina").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_vacina").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function atualizar_vacina(x){
  if(x == 'x'){
    document.getElementById("nova_vacina_").style.display = 'block';
  }
}

function excluir_vacina(id){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "animal/vacina/palco_excluir.php?id="+id;
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

function ativar_excluir_vacina(id){
    window.location.href = "animal/vacina/_excluir.php?id="+id;
}
</script>

<form role="form" action="animal/vacina/_cadastrar.php?id_animal=<?=$id_animal?>" method="post" onsubmit="return ativar_vacina()">
<div class="row">
<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">

    <div class="form-group">
      <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control pull-right" id="data_vacina" name="data_vacina">
      </div>
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Vacina<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="vacina" name="vacina" onchange="atualizar_vacina(this.value)">
        <option value="">Selecionar doença</option>
        <option></option>
        <?
        $vacina = DBRead('vacina', "ORDER BY nome asc");
        foreach ($vacina as $vacinas) { ?>
          <option value="<?=$vacinas['id']?>"><?=$vacinas['nome']?></option>
          <? } ?>
        <option style="color:green;" value="x">Cadastrar nova vacina</option>
      </select>
    </div>

    <div class="form-group" id="nova_vacina_" style="display:none;">
      <label for="exampleInputPassword1">Nova vacina<span style="color:#F00;">*</span></label>
        <input type="text" class="form-control" id="nova_vacina" name="nova_vacina">
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Observações</label>
      <textarea  class="form-control" name="observacoes_vacina" id="observacoes_vacina" cols="45" rows="6" style="height:135px; width:100%;"><?=$animal[0]['obs']?></textarea>
    </div>
    <button type="submit" class="btn btn-success" style="margin-top:0%; width:100%;">Cadastrar vacina</button>
  </div>
</div>

<div class="col-md-8">
  <table class="table table-bordered" id="tabela_padrao" width="98%">
    <tr>
      <th></th>
      <th>Vacina</th>
      <th>Data</th>
      <th>Observação</th>
      <th>Excluir</th>
    </tr>
    <?
    $vacina = DBRead('vacinas', "WHERE id_animal = '$id_animal' ORDER BY data asc");
    foreach ($vacina as $vacina_) {
      $y++;
      $id_vacina = $vacina_['id_vacina'];
      $nome_vacina = DBRead('vacina', "WHERE id = '$id_vacina'");
      $data = $vacina_['data'];
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
      $data_vacina = $data;
    ?>
      <td><?=$y?></td>
      <td><?=$nome_vacina[0]['nome']?></td>
      <td><?=$data_vacina?></td>
      <td><?=$vacina_['obs']?></td>
      <td><button type="button" class="btn btn-danger" style="margin-top:0%; padding:3%; padding-left:5%; padding-right:5%;" onclick="excluir_vacina(<?=$vacina_['id']?>)">X</button></td>
    </tr>
  <? } ?>
    </table>
</div>
</div>
</form>
