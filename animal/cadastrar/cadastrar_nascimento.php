<?
$qtd = $_GET['x'];
$id_lote = $_GET['id_lote'];
$tipo = $_GET['tipo'];
$y = $_GET['y'];
if(!$y){
  $data = $_GET['data_de_nascimento'];
  $y=1;
}else{
  $data = $_GET['data'];
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
}

//MONTA
if($tipo == 1){
    $monta_controle = DBRead('monta_controle', "WHERE id = '$id_lote'");
    $id_mae = $monta_controle[0]['id_animal'];
    $id_monta = $monta_controle[0]['id_monta'];
    $monta = DBRead('monta', "WHERE id = '$id_monta'");
    $id_pai = $monta[0]['id_animal'];

if($monta[0]['terceiro']){
  $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
}else{
  $pai = DBRead('animais', "WHERE id = '$id_pai'");
}

if($monta_controle[0]['terceiro']){
  $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
}else{
  $mae = DBRead('animais', "WHERE id = '$id_mae'");
}}

// IA
if($tipo == 2){
    $inseminacao_controle = DBRead('inseminacao_controle', "WHERE id = '$id_lote'");
    $id_mae = $inseminacao_controle[0]['id_femea'];
    $id_inseminacao = $inseminacao_controle[0]['id_lote'];
    $inseminacao = DBRead('inseminacao', "WHERE id = '$id_inseminacao'");
    $id_pai = $inseminacao[0]['id_macho'];


if($inseminacao[0]['terceiro']){
  $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
}else{
  $pai = DBRead('animais', "WHERE id = '$id_pai'");
}

if($inseminacao_controle[0]['terceiro']){
  $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
}else{
  $mae = DBRead('animais', "WHERE id = '$id_mae'");
}}

// TE
if($tipo == 3){
    $transplante_controle = DBRead('transplante_controle', "WHERE id = '$id_lote'");
    $id_transplante = $transplante_controle[0]['id_lote'];
    $transplante = DBRead('transplante', "WHERE id = '$id_transplante'");
    $id_pai = $transplante[0]['id_pai'];
    $id_mae = $transplante[0]['id_mae'];
    $receptora = $transplante_controle[0]['receptora'];

if($transplante[0]['terceiro_pai']){
  $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
}else{
  $pai = DBRead('animais', "WHERE id = '$id_pai'");
}

if($transplante[0]['terceiro_mae']){
  $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
}else{
  $mae = DBRead('animais', "WHERE id = '$id_mae'");
}}

if($tipo == 2){ $pre = "IA"; }
if($tipo == 3){ $pre = "TE"; }

?>


<script type="text/javascript">
function ativar_nascimento(){
  saida = 0;
	if((!document.getElementById("nome_animal").value) || (document.getElementById("nome_animal").value == '<?=$user[0]['prefixo']?>')){
    document.getElementById("nome_animal").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("nome_animal").style.border = "1px solid green";}

  if(!document.getElementById("tatuagem").value){
    document.getElementById("tatuagem").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("tatuagem").style.border = "1px solid green";}

  if(!document.getElementById("sexo").value){
    document.getElementById("sexo").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("sexo").style.border = "1px solid green";}

  if(!document.getElementById("data_nascimento").value){
    document.getElementById("data_nascimento").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_nascimento").style.border = "1px solid green";}

  if(!document.getElementById("raca").value){
    document.getElementById("raca").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("raca").style.border = "1px solid green";}

  if(!document.getElementById("peso").value){
    document.getElementById("peso").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("peso").style.border = "1px solid green";}

  if(!document.getElementById("status").value){
    document.getElementById("status").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("status").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}
</script>

<section class="content-header">
  <h1>
    Cadastrar animal
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-plus"></i> Cadastrar</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <!-- left column -->
    <div class="col-md-12">
      <!-- general form elements -->
      <div class="box box-success">
        <!-- form start -->
          <div class="box-body">

          <div class="col-md-3" style="margin-left:-10px;">
            <div class="form-group">
              <label for="exampleInputEmail1">Tipo de cadastro<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" value="Nascimento - Cria <?=$y?>/<?=$qtd?>" readonly="readonly">
            </div>
         </div>


  <form role="form" action="animal/cadastrar/_nascimento.php?qtd=<?=$qtd?>&id_lote=<?=$id_lote?>&tipo=<?=$tipo?>&y=<?=$y?>&receptora=<?=$receptora?>" method="post" onsubmit="return ativar_nascimento()">
        <div class="col-md-12" style="margin-left:-30px;">
           <div class="col-md-3">
             <!-- general form elements -->
             <div class="box-body">

               <div class="form-group">
                 <label for="exampleInputPassword1">Nome<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="nome_animal" name="nome_animal" value="<?=$user[0]['prefixo']?><?=$pre?>">
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Tatuagem<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="tatuagem" name="tatuagem" >
               </div>


               <div class="form-group">
                 <label for="exampleInputPassword1">Sexo<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="sexo" name="sexo">
                 <option value="">Selecionar</option>
                   <option value=""></option>
                   <option value="Macho">Macho</option>
                   <option value="Fêmea">Fêmea</option>
                 </select>
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
                   <? if($y > 1){?> <input type="text" class="form-control pull-right" id="data_nascimento" name="data_de_nascimento" value="<?=$data?>" readonly="readonly"> <? }else{ ?> <input type="text" class="form-control pull-right" id="data_nascimento" name="data_de_nascimento"
                    value="<?=$data?>"><? }?>
                 </div>
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
                 <label for="exampleInputPassword1">Pai<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="pai" name="pai" value="<?=$pai[0]['nome']?>" readonly="readonly">
                 <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:90%; display:none; margin-top:1%;">
                 </div>
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Mãe<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="mae" name="mae" value="<?=$mae[0]['nome']?>" readonly="readonly">
                 <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:90%; display:none; margin-top:1%;">
                 </div>
               </div>


               <div class="form-group">
                 <label for="exampleInputPassword1">Peso de nascimento (kg)<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="peso" name="peso" value="300">
               </div>

             </div>
           </div>

           <div class="col-md-3">
             <!-- general form elements -->
             <div class="box-body">
               <div class="form-group">
                 <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="raca" name="raca">
                   <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option>
                   <option></option>
                   <?
                   $raca = DBRead('raca', "ORDER BY nome asc");
                   foreach ($raca as $raca_) { ?>
                     <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                   <? } ?>
                 </select>
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Estado do animal<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="status" name="status">
                  <option value="">Selecionar</option>
                   <option value=""></option>
                   <option value="0">Vivo</option>
                   <option value="1">Morto</option>
                 </select>
                 </div>
               </div>

               <div class="form-group">
                 <button type="submit" class="btn btn-success" style="width:100%; margin-top:4%;">Cadastrar animal</button>
               </div>

             </div>
           </div>
         </form>

    </div>
  </div>
</div>
</div>
</section>
