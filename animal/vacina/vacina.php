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

function excluir_vacina(botao){
  confirmarExclusao({
    titulo: 'Excluir vacina?',
    nome: botao.getAttribute('data-descricao'),
    descricao: 'Confirme se deseja excluir esta registro de vacina. Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      window.location.href = 'animal/vacina/_excluir.php?id=' + encodeURIComponent(botao.getAttribute('data-id'));
    }
  });
}
</script>

<form class="registros-animal" role="form" action="animal/vacina/_cadastrar.php?id_animal=<?=$id_animal?>" method="post" onsubmit="return ativar_vacina()">
<div class="row">
<div class="col-md-4">
  <h3 class="titulo-registros">Cadastrar vacina</h3>
  <div class="box-body">

    <div class="form-group">
      <label for="data_vacina">Data<span style="color:#F00;">*</span></label>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control pull-right" id="data_vacina" name="data_vacina">
      </div>
    </div>

    <div class="form-group">
      <label for="vacina">Vacina<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="vacina" name="vacina" onchange="atualizar_vacina(this.value)">
        <option value="">Selecionar vacina</option>
        <option></option>
        <?
        $vacina = DBRead('vacina', "ORDER BY nome asc");
        foreach (($vacina ?: []) as $vacinas) { ?>
          <option value="<?=$vacinas['id']?>"><?=$vacinas['nome']?></option>
          <? } ?>
        <option style="color:green;" value="x">Cadastrar nova vacina</option>
      </select>
    </div>

    <div class="form-group" id="nova_vacina_" style="display:none;">
      <label for="nova_vacina">Nova vacina<span style="color:#F00;">*</span></label>
        <input type="text" class="form-control" id="nova_vacina" name="nova_vacina">
    </div>

    <div class="form-group">
      <label for="observacoes_vacina">Observações</label>
      <textarea  class="form-control" name="observacoes_vacina" id="observacoes_vacina" cols="45" rows="5"><?=$animal[0]['obs']?></textarea>
    </div>
    <div class="acoes-registros"><button type="submit" class="btn btn-success">Cadastrar vacina</button></div>
  </div>
</div>

<div class="col-md-8">
  <h3 class="titulo-registros">Histórico de vacinas</h3>
  <div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
    <tr>
      <th>#</th>
      <th>Vacina</th>
      <th>Data</th>
      <th>Observação</th>
      <th class="text-center"><span class="sr-only">Excluir</span></th>
    </tr>
    </thead>
    <tbody>
    <?
    $vacina = DBRead('vacinas', "WHERE id_animal = '$id_animal' ORDER BY data asc");
    foreach (($vacina ?: []) as $vacina_) {
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
    <tr>
      <td><?=$y?></td>
      <td><?=$nome_vacina[0]['nome']?></td>
      <td><?=$data_vacina?></td>
      <td><?=$vacina_['obs']?></td>
      <td class="text-center"><button type="button" class="btn btn-link text-danger" style="padding:0; color:#dd4b39;" title="Excluir vacina" aria-label="Excluir vacina" data-id="<?=(int)$vacina_['id']?>" data-descricao="<?=htmlspecialchars(($nome_vacina[0]['nome'] ?? '') . ' — ' . $data_vacina, ENT_QUOTES, 'UTF-8')?>" onclick="excluir_vacina(this)"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
    </tr>
  <? } ?>
    </tbody>
    </table>
  </div>
</div>
</div>
</form>
