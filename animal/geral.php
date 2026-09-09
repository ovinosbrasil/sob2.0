<script type="text/javascript">

function ativar_geral(){
  saida = 0;
	if(!document.getElementById("nome_animal").value){
    document.getElementById("nome_animal").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("nome_animal").style.border = "1px solid green";}

  if(!document.getElementById("tatuagem").value){
    document.getElementById("tatuagem").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("tatuagem").style.border = "1px solid green";}

  if(!document.getElementById("datepicker").value){
    document.getElementById("datepicker").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("datepicker").style.border = "1px solid green";}

  if(!document.getElementById("sexo").value){
    document.getElementById("sexo").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("sexo").style.border = "1px solid green";}

  if(!document.getElementById("pai").value){
    document.getElementById("pai").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("pai").style.border = "1px solid green";}

  if(!document.getElementById("mae").value){
    document.getElementById("mae").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("mae").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function abrir_avaliacao(x){
  if(x){
      window.location.href = "geral.php?pg=animal&aba=avaliacao&id_animal=<?=$id_animal?>";
  }
}
</script>

<?
$data = $animal[0]['data_de_nascimento'];
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

$data = $animal[0]['data_de_entrada'];
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
$data_de_entrada = $data;


$data = $data_nascimento;
list($dia, $mes, $ano) = explode('/', $data);
// Descobre que dia é hoje e retorna a unix timestamp
$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
// Descobre a unix timestamp da data de nascimento do fulano
$nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
// Depois apenas fazemos o cálculo já citado :)
$anos = floor((((($hoje - $nascimento) / 60) / 60) / 24));
$idade_anos  = floor($anos /365);
$idade_meses = (($anos /365) - $idade_anos) * 12;
$idade_meses = (int)$idade_meses;
$idade_meses = round($idade_meses);


$id_pai = $animal[0]['pai'];
if($animal[0]['terceiro_pai']){
  $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
}else{
  $pai = DBRead('animais', "WHERE id = '$id_pai'");
}

$id_mae = $animal[0]['mae'];
if($animal[0]['terceiro_mae']){
  $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
}else{
  $mae = DBRead('animais', "WHERE id = '$id_mae'");
}
?>



<form role="form" action="animal/_alterar.php?id_animal=<?=$id_animal?>" method="post" onsubmit="return ativar_geral()">
<div class="row">
<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">

    <div class="form-group">
      <label for="exampleInputPassword1">Nome<span style="color:#F00;">*</span></label>
      <input type="text" class="form-control" id="nome_animal" name="nome_animal" value="<?=$animal[0]['nome']?>">
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Tatuagem<span style="color:#F00;">*</span></label>
      <input type="text" class="form-control" id="tatuagem" name="tatuagem" value="<?=$animal[0]['tatuagem']?>">
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">FBB</label>
      <input type="text" class="form-control" id="fbb" name="fbb" value="<?=$animal[0]['fbb']?>">
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Forma de entrada</label>
      <? if($animal[0]['entrada'] == 0){ ?><input type="text" class="form-control" id="entrada" name="entrada" value="Nascimento" readonly="readonly"> <? } ?>
      <? if($animal[0]['entrada'] == 1){ ?><input type="text" class="form-control" id="entrada" name="entrada" value="Compra" readonly="readonly"> <? } ?>
      <? if($animal[0]['entrada'] == 2){ ?><input type="text" class="form-control" id="entrada" name="entrada" value="Rebanho" readonly="readonly"> <? } ?>
    </div>
  </div>
</div>

<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group" >
      <label for="exampleInputPassword1">Data de Nascimento<span style="color:#F00;">*</span></label>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control pull-right" id="datepicker" name="data_de_nascimento" value="<?=$data_nascimento?>">
      </div>
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Data de entrada no rebanho</label>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        <input type="text" class="form-control pull-right" id="datepicker2" name="data_de_entrada" value="<?=$data_de_entrada?>"  >
      </div>
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Idade</label>
      <input type="text" class="form-control" id="idade" name="idade" value="<?=$idade_anos?> Anos <?=$idade_meses?> Meses" readonly="readonly">
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Sexo<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="sexo" name="sexo" onchange="lista_colaborador(this.value)">
        <? if($animal[0]['sexo'] != ''){?> <option value="<?=$animal[0]['sexo']?>"><?=$animal[0]['sexo']?></option><? }else{ ?>
      <option value="x">Selecionar sexo</option> <? } ?>
        <option></option>
        <option value="Macho">Macho</option>
        <option value="Fêmea">Fêmea</option>
      </select>
    </div>
  </div>
</div>


<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group">
      <label for="exampleInputPassword1">Pai<span style="color:#F00;">*</span><a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"><span style="font-size:13px; color:green;"> Novo</span></a></label>
      <input type="text" class="form-control" id="pai" name="pai" value="<?=$pai[0]['nome']?>" onKeyUp="pesquisar_pai(this.value)">
      <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:90%; display:none; margin-top:1%;">
      </div>
    </div>


    <div class="form-group">
      <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="raca" name="raca">
        <?if($animal[0]['raca'] != ''){?> <option value="<?=$animal[0]['raca']?>"><?=$animal[0]['raca']?></option> <? }else{?><option value="">Selecionar raca</option> <? } ?>
        <option value=""></option>
        <?
        $raca = DBRead('raca', "ORDER BY nome asc");
        foreach ($raca as $raca_) { ?>
          <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
        <? } ?>
      </select>
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Observações</label>
      <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="5" style="height:105px; width:100%;"><?=$animal[0]['observacoes']?></textarea>
    </div>


  </div>
</div>


<div class="col-md-3">
  <!-- general form elements -->
  <div class="box-body">
    <div class="form-group">
      <label for="exampleInputPassword1">Mãe<span style="color:#F00;">*</span><a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"> <span style="font-size:13px; color:green;"> Novo</span></a></label>
      <input type="text" class="form-control" id="mae" name="mae" value="<?=$mae[0]['nome']?>" onKeyUp="pesquisar_mae(this.value)">
      <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:90%; display:none; margin-top:1%;">
      </div>
    </div>

    <div class="form-group">
      <label for="exampleInputPassword1">Tipo</label>
      <select class="form-control select" id="tipo" name="tipo" onchange="abrir_avaliacao(this.value)">
      <? if(!$animal[0]['tipo']){?> <option value="">Animal sem Avaliações</option> <? }else{?> <option value="<?=$animal[0]['tipo']?>"><?=$animal[0]['tipo']?></option> <? } ?>
        <option></option>
        <option value="x">Abrir avaliações do animal</option>
      </select>
    </div>

    <? if($animal[0]['entrada'] == 1){ ?>
      <div class="form-group">
        <label for="exampleInputPassword1">Preço da compra</label>
        <input type="text" class="form-control" id="valor" name="preco_de_compra" value="<?=$animal[0]['preco_de_compra']?>">
      </div>
    <? } ?>
    <div class="form-group">
      <button type="submit" class="btn btn-warning" style="margin-top:2%; width:100%;">Alterar animal</button>
      <a href="animal/_imprimir.php?id_animal=<?=$id_animal?>" target="_blank"><button type="button" class="btn btn-primary" style="margin-top:2%; width:100%;">Imprimir animal</button></a>
      <a href="animal/_excluir_animal.php?id_animal=<?=$id_animal?>"><button type="button" class="btn btn-danger" style="margin-top:2%; width:100%;">Excluir animal</button></a>
    </div>

  </div>
</div>

</div>
</form>
