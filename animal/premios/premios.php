<script type="text/javascript">
function ativar_premio(){
  saida = 0;
	if(!document.getElementById("categoria").value){
    document.getElementById("categoria").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("categoria").style.border = "1px solid green";}

  if(!document.getElementById("posicao").value){
    document.getElementById("posicao").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("posicao").style.border = "1px solid green";}

  if(!document.getElementById("exposicao").value){
    document.getElementById("exposicao").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("exposicao").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function atualizar(x){
  if(x == 'x'){
    document.getElementById("nova_vacina_").style.display = 'block';
  }
}

function excluir_premio(botao){
  confirmarExclusao({
    titulo: 'Excluir prêmio?',
    nome: botao.getAttribute('data-descricao'),
    descricao: 'Confirme se deseja excluir este registro de prêmio. Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      window.location.href = 'animal/premios/_excluir.php?id=' + encodeURIComponent(botao.getAttribute('data-id'));
    }
  });
}
</script>

<form class="registros-animal" role="form" action="animal/premios/_cadastrar.php?id_animal=<?=$id_animal?>" method="post" onsubmit="return ativar_premio()">
<div class="row">
<div class="col-md-4">
  <h3 class="titulo-registros">Cadastrar prêmio</h3>
  <div class="box-body">

    <div class="form-group">
      <label for="posicao">Posição<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="posicao" name="posicao">
        <option value="">Selecionar</option>
        <option value=""></option>
        <option value="1° Posição">1° Prêmio</option>
        <option value="2° Posição">2° Prêmio</option>
        <option value="3° Posição">3° Prêmio</option>
        <option value="4° Posição">4° Prêmio</option>
        <option value="5° Posição">5° Prêmio</option>
        <option value="6° Posição">6° Prêmio</option>
        <option value="7° Posição">7° Prêmio</option>
        <option value="8° Posição">8° Prêmio</option>
        <option value="Campeão">Campeão</option>
        <option value="Res. Campeão">Res. Campeão</option>
        <option value="Grande Campeão">Grande Campeão</option>
        <option value="Res. Grande Campeão">Res. Grande Campeão</option>
      </select>
    </div>

    <div class="form-group">
      <label for="categoria">Categoria<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="categoria" name="categoria">
        <option value="">Selecionar</option>
       <option value=""></option>
       <option value="Borrego(a) Menor I">Borrego(a) Menor I</option>
       <option value="Borrego(a) Menor II">Borrego(a) Menor II</option>
       <option value="Borrego(a) Maior I">Borrego(a) Maior I</option>
       <option value="Borrego(a) Maior II">Borrego(a) Maior II</option>
       <option value="Jovem I">Jovem I</option>
       <option value="Jovem II">Jovem II</option>
       <option value="Adulto Menor I">Adulto Menor I</option>
       <option value="Adulto Menor II">Adulto Menor II</option>
       <option value="Adulto I">Adulto I</option>
       <option value="Adulto II">Adulto II</option>
       <option value="Adulto Maior I">Adulto Maior I</option>
       <option value="Adulto Maior II">Adulto Maior II</option>
       <option value="Sênior I">Sênior I</option>
       <option value="Sênior II">Sênior II</option>
       <option value="Progênie de Pai">Progênie de Pai</option>
       <option value="Progênie de Mãe">Progênie de Mãe</option>
      </select>
    </div>

    <div class="form-group" id="nova_vacina_">
      <label for="exposicao">Exposição<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="exposicao" name="exposicao">
        <option value="">Selecionar</option>
       <option value=""></option>
       <?
       $exposicao = DBRead('julgamento', "ORDER BY data asc");
       foreach (($exposicao ?: []) as $exposicao_) { ?>
        <option value="<?=$exposicao_['id']?>"><?=$exposicao_['nome']?> - <?=$exposicao_['cidade']?></option>.
      <? } ?>
      </select>
    </div>
    <div class="acoes-registros"><button type="submit" class="btn btn-success">Cadastrar prêmio</button></div>
  </div>
</div>

<div class="col-md-8">
  <h3 class="titulo-registros">Histórico de prêmios</h3>
  <div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
    <tr>
      <th>#</th>
      <th>Prêmios</th>
      <th>Exposição</th>
      <th>Data</th>
      <th class="text-center"><span class="sr-only">Excluir</span></th>
    </tr>
    </thead>
    <tbody>
    <?
    $premio = DBRead('premio', "WHERE id_animal = '$id_animal'");
    foreach (($premio ?: []) as $premio_) {
      $z++;
      $id_exposicao = $premio_['id_julgamento'];
      $exposicao = DBRead('julgamento', "WHERE id = '$id_exposicao'");
      $data = $exposicao[0]['data'];
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
      $data_exposicao = $data;
    ?>
    <tr>
      <td><?=$z?></td>
      <td><?=$premio_['premio']?></td>
      <td><?=$exposicao[0]['nome']?> - <?=$exposicao[0]['cidade']?></td>
      <td><?=$data_exposicao?></td>
      <td class="text-center"><button type="button" class="btn btn-link text-danger" style="padding:0; color:#dd4b39;" title="Excluir prêmio" aria-label="Excluir prêmio" data-id="<?=(int)$premio_['id']?>" data-descricao="<?=htmlspecialchars(($premio_['premio'] ?? '') . ' — ' . ($exposicao[0]['nome'] ?? ''), ENT_QUOTES, 'UTF-8')?>" onclick="excluir_premio(this)"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
    </tr>
  <? } ?>
    </tbody>
    </table>
  </div>
</div></div>
</form>
