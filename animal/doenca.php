<script type="text/javascript">
function desfazer_venda(){
    window.location.href = "animal/saida/_desfazer_saida.php?id_animal=<?=$id_animal?>";
}

function ativar(){
  saida = 0;
	if(!document.getElementById("tipo_saida").value){
    document.getElementById("tipo_saida").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("tipo_saida").style.border = "1px solid green";}

  if(!document.getElementById("data_saida").value){
    document.getElementById("data_saida").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_saida").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function atualizar(x){
  if(x == 1){
    document.getElementById("tipos_de_mortes").style.display = 'block';
  }else{
    document.getElementById("tipos_de_mortes").style.display = 'none';
  }
}
</script>
<?
if($animal[0]['data_de_saida']){
$data = $animal[0]['data_de_saida'];
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
$data_saida = $data;
}
?>
<form role="form" action="animal/saida/_saida.php?id_animal=<?=$id_animal?>" method="post" onsubmit="return ativar()">
<div class="row">
<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control pull-right" id="data_doenca" name="data_doenca">
      </div>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Doença<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="tipo_saida" name="tipo_saida" onchange="atualizar(this.value)">
        <option>Selecionar doença</option>
        <option></option>
        <?
        $doenca = DBRead('doenca', "ORDER BY nome asc");
        foreach ($doenca as $doencas) { ?>
          <option value="<?=$doencas['nome']?>"><?=$doencas['nome']?></option>
          <? } ?>
      </select>
    </div>

    <div class="form-group" style="margin-top:3%;">
      <label for="exampleInputPassword1">Observações</label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="6" style="height:135px; width:100%;"><?=$animal[0]['observacoes_de_saida']?></textarea>
    </div>
  </div>
</div>

</form>
