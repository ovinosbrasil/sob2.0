<script type="text/javascript">
function ativar_doenca(){
  saida = 0;
	if(!document.getElementById("doenca").value){
    document.getElementById("doenca").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("doenca").style.border = "1px solid green";}

  if(!document.getElementById("data_doenca").value){
    document.getElementById("data_doenca").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_doenca").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function atualizar_doenca(x){
  if(x == 'x'){
    document.getElementById("nova_doenca_").style.display = 'block';
  }
}

function excluir_doenca(botao){
  confirmarExclusao({
    titulo: 'Excluir doença?',
    nome: botao.getAttribute('data-descricao'),
    descricao: 'Confirme se deseja excluir esta registro de doença. Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      window.location.href = 'animal/doenca/_excluir.php?id=' + encodeURIComponent(botao.getAttribute('data-id'));
    }
  });
}
</script>

<form class="registros-animal" role="form" action="animal/doenca/_cadastrar.php?id_animal=<?=$id_animal?>" method="post" onsubmit="return ativar_doenca()">
<div class="row">
<div class="col-md-4">
  <h3 class="titulo-registros">Cadastrar doença</h3>
  <div class="box-body">

    <div class="form-group">
      <label for="data_doenca">Data<span style="color:#F00;">*</span></label>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control pull-right" id="data_doenca" name="data_doenca">
      </div>
    </div>

    <div class="form-group">
      <label for="doenca">Doença<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="doenca" name="doenca" onchange="atualizar_doenca(this.value)">
        <option value="">Selecionar doença</option>
        <option></option>
        <?
        $doenca = DBRead('doenca', "ORDER BY nome asc");
        foreach (($doenca ?: []) as $doencas) { ?>
          <option value="<?=$doencas['id']?>"><?=$doencas['nome']?></option>
          <? } ?>
        <option style="color:green;" value="x">Cadastrar nova doença</option>
      </select>
    </div>

    <div class="form-group" id="nova_doenca_" style="display:none;">
      <label for="nova_doenca">Nova doença<span style="color:#F00;">*</span></label>
        <input type="text" class="form-control" id="nova_doenca" name="nova_doenca">
    </div>

    <div class="form-group">
      <label for="observacoes_doenca">Observações</label>
      <textarea  class="form-control" name="observacoes_doenca" id="observacoes_doenca" cols="45" rows="5"><?=$animal[0]['obs']?></textarea>
    </div>
    <div class="acoes-registros"><button type="submit" class="btn btn-success">Cadastrar doença</button></div>
  </div>
</div>

<div class="col-md-8">
  <h3 class="titulo-registros">Histórico de doenças</h3>
  <div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
    <tr>
      <th>#</th>
      <th>Doença</th>
      <th>Data</th>
      <th>Observação</th>
      <th class="text-center"><span class="sr-only">Excluir</span></th>
    </tr>
    </thead>
    <tbody>
    <?
    $doenca = DBRead('doencas', "WHERE id_animal = '$id_animal' ORDER BY data asc");
    foreach (($doenca ?: []) as $doenca_) {
      $x++;
      $id_doenca = $doenca_['id_doenca'];
      $nome_doenca = DBRead('doenca', "WHERE id = '$id_doenca'");
      $data = $doenca_['data'];
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
      $data_doenca = $data;
    ?>
    <tr>
      <td><?=$x?></td>
      <td><?=$nome_doenca[0]['nome']?></td>
      <td><?=$data_doenca?></td>
      <td><?=$doenca_['obs']?></td>
      <td class="text-center"><button type="button" class="btn btn-link text-danger" style="padding:0; color:#dd4b39;" title="Excluir doença" aria-label="Excluir doença" data-id="<?=(int)$doenca_['id']?>" data-descricao="<?=htmlspecialchars(($nome_doenca[0]['nome'] ?? '') . ' — ' . $data_doenca, ENT_QUOTES, 'UTF-8')?>" onclick="excluir_doenca(this)"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
    </tr>
  <? } ?>
    </tbody>
    </table>
  </div>
</div>
</div>
</form>
