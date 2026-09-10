<?
function addDayIntoDate($date,$days) {
     $thisyear = substr ( $date, 0, 4 );
     $thismonth = substr ( $date, 4, 2 );
     $thisday =  substr ( $date, 6, 2 );
     $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday + $days, $thisyear );
     return strftime("%Y%m%d", $nextdate);
}

$tipo = $_GET['tipo']; ?>


<script type="text/javascript">
function atualizar(x){
  window.location.href = "geral.php?pg=cadastrar_animal&tipo="+x;
}

function cadastrar_monta(x,id_lote,tipo){
  data = document.getElementById("data_nascimento").value;
  window.location.href = "geral.php?pg=cadastrar_nascimento&x="+x+"&id_lote="+id_lote+"&tipo="+tipo+"&data_de_nascimento="+data;
}

function ativar_compra(){
  saida = 0;
	if(!document.getElementById("nome_animal").value){
    document.getElementById("nome_animal").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("nome_animal").style.border = "1px solid green";}

  if(!document.getElementById("tatuagem").value){
    document.getElementById("tatuagem").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("tatuagem").style.border = "1px solid green";}

  if(!document.getElementById("data_nascimento").value){
    document.getElementById("data_nascimento").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_nascimento").style.border = "1px solid green";}

  if(!document.getElementById("data_entrada").value){
    document.getElementById("data_entrada").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_entrada").style.border = "1px solid green";}

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

  if(!document.getElementById("raca").value){
    document.getElementById("raca").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("raca").style.border = "1px solid green";}

  if(!document.getElementById("valor").value){
    document.getElementById("valor").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("valor").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function ativar_rebanho(){
  saida = 0;
	if(!document.getElementById("nome_animal").value){
    document.getElementById("nome_animal").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("nome_animal").style.border = "1px solid green";}

  if(!document.getElementById("tatuagem").value){
    document.getElementById("tatuagem").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("tatuagem").style.border = "1px solid green";}

  if(!document.getElementById("data_nascimento").value){
    document.getElementById("data_nascimento").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_nascimento").style.border = "1px solid green";}

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

  if(!document.getElementById("raca").value){
    document.getElementById("raca").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("raca").style.border = "1px solid green";}


  if(saida){ return false; }else{ return true; }
}

function ativar_terceiros(){
  saida = 0;
	if(!document.getElementById("nome_animal").value){
    document.getElementById("nome_animal").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("nome_animal").style.border = "1px solid green";}

  if(!document.getElementById("sexo").value){
    document.getElementById("sexo").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("sexo").style.border = "1px solid green";}

  if(!document.getElementById("raca").value){
    document.getElementById("raca").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("raca").style.border = "1px solid green";}


  if(saida){ return false; }else{ return true; }
}

function pesquisar_mae(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/te/lista_mae.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_mae").innerHTML = resposta;
document.getElementById("lista_mae").style.display = 'block';
}
}
PP.send(null);
}

function fechar_lista_mae(){
  document.getElementById("lista_mae").style.display = 'none';
}

function linkar_mae_te(nome){
  window.location.href = "geral.php?pg=lista_te&mae="+nome;
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
              <select class="form-control select" id="tipo_cadastro" name="tipo_cadastro" onchange="atualizar(this.value)">
                <? if($tipo == 1){ ?> <option value="1">Compra</option> <? } ?>
                <? if($tipo == 3){ ?> <option value="3">Nascimento</option> <? } ?>
                <? if($tipo == 2){ ?> <option value="2">Rebanho</option> <? } ?>
                <? if($tipo == 4){ ?> <option value="4">Terceiros</option> <? } ?>
                <? if(!$tipo){ ?><option value="">Selecionar</option><? } ?>
                <option value=""></option>
                <option value="1">Compra</option>
                <option value="2">Rebanho</option>
                <option value="3">Nascimento</option>
                <option value="4">Terceiros</option>
              </select>
            </div>
         </div>

<? if($tipo == 1){ ?>
<!-- FIM COMPRA -->
  <form role="form" action="animal/cadastrar/_compra.php" method="post" onsubmit="return ativar_compra()">
        <div class="col-md-12" style="margin-left:-30px;">
           <div class="col-md-3">
             <!-- general form elements -->
             <div class="box-body">

               <div class="form-group">
                 <label for="exampleInputPassword1">Nome<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="nome_animal" name="nome_animal">
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Tatuagem<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="tatuagem" name="tatuagem" >
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">FBB</label>
                 <input type="text" class="form-control" id="fbb" name="fbb" value="<?=$animal[0]['fbb']?>">
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Sexo<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="sexo" name="sexo" onchange="lista_colaborador(this.value)">
                 <option value="">Selecionar</option>
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
               <div class="form-group" >
                 <label for="exampleInputPassword1">Data de Nascimento<span style="color:#F00;">*</span></label>
                 <div class="input-group date">
                   <div class="input-group-addon">
                     <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control pull-right" id="data_nascimento" name="data_de_nascimento" >
                 </div>
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Data de entrada no rebanho<span style="color:#F00;">*</span></label>
                 <div class="input-group date">
                   <div class="input-group-addon">
                     <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control pull-right" id="data_entrada" name="data_de_entrada">
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
                 <label for="exampleInputPassword1">Pai<span style="color:#F00;">*</span>
                   <a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"><span style="font-size:11px; color:green;">Novo</span> </a></label>
                 <input type="text" class="form-control" id="pai" name="pai" value="<?=$pai[0]['nome']?>" onKeyUp="pesquisar_pai(this.value)">
                 <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
                 </div>
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Mãe<span style="color:#F00;">*</span>
                   <a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"> <span style="font-size:11px; color:green;">Novo</span></a></label>
                 <input type="text" class="form-control" id="mae" name="mae" value="<?=$mae[0]['nome']?>" onKeyUp="pesquisar_mae(this.value)">
                 <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
                 </div>
               </div>

               <div class="form-group">
                 <button type="submit" class="btn btn-success" style="width:100%; margin-top:4%;">Cadastrar animal</button>
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
                   foreach (($raca ?: []) as $raca_) { ?>
                     <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                   <? } ?>
                 </select>
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Preço de compra<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="valor" name="valor">
                 </div>
               </div>

             </div>
           </div>
         </form>
<? } ?>
<!-- FIM COMPRA -->


<? if($tipo == 2){ ?>
<!-- REBANHO -->
  <form role="form" action="animal/cadastrar/_rebanho.php" method="post" onsubmit="return ativar_rebanho()">
        <div class="col-md-12" style="margin-left:-30px;">
           <div class="col-md-3">
             <!-- general form elements -->
             <div class="box-body">

               <div class="form-group">
                 <label for="exampleInputPassword1">Nome<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="nome_animal" name="nome_animal" value="<?=$user[0]['prefixo']?>">
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Tatuagem<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="tatuagem" name="tatuagem" >
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">FBB</label>
                 <input type="text" class="form-control" id="fbb" name="fbb" value="<?=$animal[0]['fbb']?>">
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Sexo<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="sexo" name="sexo" onchange="lista_colaborador(this.value)">
                 <option value="">Selecionar</option>
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
               <div class="form-group" >
                 <label for="exampleInputPassword1">Data de Nascimento<span style="color:#F00;">*</span></label>
                 <div class="input-group date">
                   <div class="input-group-addon">
                     <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control pull-right" id="data_nascimento" name="data_de_nascimento" >
                 </div>
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Data de entrada no rebanho</label>
                 <div class="input-group date">
                   <div class="input-group-addon">
                     <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control pull-right" id="data_entrada" name="data_de_entrada">
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
                 <label for="exampleInputPassword1">Pai<span style="color:#F00;">*</span>
                   <a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"><span style="font-size:11px; color:green;">Novo</span></a></label>
                 <input type="text" class="form-control" id="pai" name="pai" value="<?=$pai[0]['nome']?>" onKeyUp="pesquisar_pai(this.value)">
                 <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
                 </div>
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Mãe<span style="color:#F00;">*</span>
                   <a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"><span style="font-size:11px; color:green;">Novo</span></a></label>
                 <input type="text" class="form-control" id="mae" name="mae" value="<?=$mae[0]['nome']?>" onKeyUp="pesquisar_mae(this.value)">
                 <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
                 </div>
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="raca" name="raca">
                   <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option>
                   <option></option>
                   <?
                   $raca = DBRead('raca', "ORDER BY nome asc");
                   foreach (($raca ?: []) as $raca_) { ?>
                     <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                   <? } ?>
                 </select>
               </div>

               <div class="form-group">
                 <button type="submit" class="btn btn-success" style="width:100%; margin-top:4%;">Cadastrar animal</button>
               </div>
             </div>
           </div>

           </div>
         </form>
<? } ?>
<!-- FIM REBANHO -->

<? if($tipo == 3){
$nome_mae = trim($_POST['mae'] ?? '');
$dataNascimentoInformada = trim($_POST['data_de_nascimento'] ?? '');
$dataNascimentoValidada = DateTime::createFromFormat('!d/m/Y', $dataNascimentoInformada);
$dataNascimentoValida = $dataNascimentoValidada && $dataNascimentoValidada->format('d/m/Y') === $dataNascimentoInformada;

?>
<!--NASCIMENTO -->
  <form role="form" action="geral.php?pg=cadastrar_animal&tipo=3" method="post">
        <div class="col-md-12" style="margin-left:-30px;">
          <div class="box-body">

          <div class="col-md-3">
            <!-- general form elements -->
              <div class="form-group">
                <label for="exampleInputPassword1">Selecionar matriz:</label>
                <input type="text" class="form-control" id="mae" name="mae" onKeyUp="pesquisar_mae(this.value)" value="<?=htmlspecialchars($nome_mae, ENT_QUOTES, 'UTF-8')?>">
                <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:250%; display:none; margin-top:1%;">
                </div>
              </div>
          </div>

          <div class="col-md-3">
            <!-- general form elements -->
              <div class="form-group" >
                <label for="exampleInputPassword1">Data de Nascimento</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_nascimento" name="data_de_nascimento" value="<?=htmlspecialchars($dataNascimentoInformada, ENT_QUOTES, 'UTF-8')?>">
                </div>
              </div>
          </div>



          <div class="col-md-3">
            <!-- general form elements -->
              <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width:50%; margin-top:7%;">Próxima etapa</button>
              </div>
          </div>
        </div>
    </div>
</form>


  <div class="col-md-12">
            <? if(!$nome_mae){ ?>
            Últimos nascimentos cadastrados
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Data de nascimento</th>
              <th>Animal</th>
              <th>sexo</th>
              <th>Pai</th>
              <th>Mãe</th>
            </tr>
            <?
            $nasc = DBRead('animais', "WHERE entrada = 0 ORDER BY id desc LIMIT 15");
            foreach (($nasc ?: []) as $nasc_) {

              $data = $nasc_['data_de_nascimento'];
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


              $id_pai = $nasc_['pai'];
              if($nasc_['terceiro_pai']){
                $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
              }else{
                $pai = DBRead('animais', "WHERE id = '$id_pai'");
              }

              $id_mae = $nasc_['mae'];
              if($nasc_['terceiro_mae']){
                $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
              }else{
                $mae = DBRead('animais', "WHERE id = '$id_mae'");
              }
            ?>
            <? if($nasc_['status'] > 0){ ?> <tr style="color:red;"> <? }else{ ?> <tr> <? } ?>
              <td><?=$data_nascimento?></td>
              <td style="cursor:pointer;" onclick="abrir_animal(<?=$nasc_['id']?>)"><?=$nasc_['nome']?></td>
              <td><?=$nasc_['sexo']?></td>
              <? if($nasc_['terceiro_pai']){ ?> <td onclick="abrir_terceiro(<?=$pai[0]['id']?>)" style="cursor:pointer;"><?=$pai[0]['nome']?></td> <? } ?>
              <? if(!$nasc_['terceiro_pai']){ ?> <td onclick="abrir_animal(<?=$pai[0]['id']?>)" style="cursor:pointer;"><?=$pai[0]['nome']?></td> <? } ?>
              <? if($nasc_['terceiro_mae']){ ?> <td onclick="abrir_terceiro(<?=$mae[0]['id']?>)" style="cursor:pointer;"><?=$mae[0]['nome']?></td> <? } ?>
              <? if(!$nasc_['terceiro_mae']){ ?> <td onclick="abrir_animal(<?=$mae[0]['id']?>)" style="cursor:pointer;"><?=$mae[0]['nome']?></td> <? } ?>
            </tr>
          <? } ?>
            </table>
          <? }elseif (!$dataNascimentoValida){ ?>
            <p role="alert">Informe uma data de nascimento válida no formato dia/mês/ano para consultar os lotes da matriz.</p>
          <? }else{ ?>

          Lotes de reprodução da matriz
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Tipo</th>
              <th>Lote</th>
              <th>Macho</th>
              <th>Data</th>
              <th>Previsão de parto</th>
              <th>Receptora</th>
              <th>Cadastrar</th>
            </tr>
            <?
            $mae = DBRead('animais', "WHERE nome = '$nome_mae'");
            if($mae[0]['id'] <= 0){
                $mae = DBRead('terceiros', "WHERE nome = '$nome_mae'");
            }
            $id_mae = $mae[0]['id'];


            $monta = DBRead('monta_controle', "WHERE id_animal = '$id_mae'");
            foreach (($monta ?: []) as $monta_){
              $id_monta_controle = $monta_['id'];
              $id_monta = $monta_['id_monta'];
              $lote = DBRead('monta', "WHERE id = '$id_monta'");
              $id_macho = $lote[0]['id_animal'];
              if($lote[0]['terceiro']){
                $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
              }else{
                $macho = DBRead('animais', "WHERE id = '$id_macho'");
              }

              $data = $lote[0]['data_inicio'];
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
              $data_inicio = $data;

              $data = $lote[0]['data_fim'];
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
              $data_fim = $data;


              $data = explode("/", $data_inicio);
              list($dia, $mes, $ano) = $data;
              $data = "$ano$mes$dia";
              $nextdate = addDayIntoDate($data,140);
              $data[0] = $nextdate[6];
            	$data[1] = $nextdate[7];
            	$data[2] = "/";
            	$data[3] = $nextdate[4];
            	$data[4] = $nextdate[5];
            	$data[5] = "/";
            	$data[6] = $nextdate[0];
            	$data[7] = $nextdate[1];
            	$data[8] = $nextdate[2];
            	$data[9] = $nextdate[3];
            	$data_previsao1 = $data;

              $data = explode("/", $data_fim);
              list($dia, $mes, $ano) = $data;
              $data = "$ano$mes$dia";
              $nextdate = addDayIntoDate($data,160);
              $data[0] = $nextdate[6];
            	$data[1] = $nextdate[7];
            	$data[2] = "/";
            	$data[3] = $nextdate[4];
            	$data[4] = $nextdate[5];
            	$data[5] = "/";
            	$data[6] = $nextdate[0];
            	$data[7] = $nextdate[1];
            	$data[8] = $nextdate[2];
            	$data[9] = $nextdate[3];
            	$data_previsao2 = $data;

              $data_parto_ = $dataNascimentoValidada->format('Y-m-d');

              $data_atual = $data_previsao1;
              $data = '0';
              $data['0'] = $data_atual['6'];
              $data['1'] = $data_atual['7'];
              $data['2'] = $data_atual['8'];
              $data['3'] = $data_atual['9'];
              $data['4'] = "-";
              $data['5'] = $data_atual['3'];
              $data['6'] = $data_atual['4'];
              $data['7'] = "-";
              $data['8'] = $data_atual['0'];
              $data['9'] = $data_atual['1'];
              $data_previsao1_ = $data;

              $data_atual = $data_previsao2;
              $data = '0';
              $data['0'] = $data_atual['6'];
              $data['1'] = $data_atual['7'];
              $data['2'] = $data_atual['8'];
              $data['3'] = $data_atual['9'];
              $data['4'] = "-";
              $data['5'] = $data_atual['3'];
              $data['6'] = $data_atual['4'];
              $data['7'] = "-";
              $data['8'] = $data_atual['0'];
              $data['9'] = $data_atual['1'];
              $data_previsao2_ = $data;

              if($data_parto_ >= '1990-01-01'){
                if(($data_parto_ >= $data_previsao1_) && ($data_parto_ <= $data_previsao2_)){
            ?>
              <? if($monta_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
              <? if(!$monta_['status_nascimento']){ ?> <tr> <? } ?>
              <td>Monta natural</td>
              <td>Lote <?=$lote[0]['codigo']?></td>
              <? if($lote[0]['terceiro']){ ?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
              <? if(!$lote[0]['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
              <td>Inicial: <?=$data_inicio?> Final: <?=$data_fim?></td>
              <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
              <td></td>
              <td>
                  <? if($monta_['status_nascimento']){ ?>
                <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_monta_controle?>, '1')" style="color:green;">
                <? }else{ ?> <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_monta_controle?>, '1')"> <? } ?>
                  <? if($monta_['status_nascimento']){?> <option value="" style="color:green;">Cadastrado</option> <? }else{ ?>
                 <option value="">Selecionar</option><? } ?>
                 <option value=""></option>
                 <option value="1">Parto simples</option>
                 <option value="2">Parto duplo</option>
                 <option value="3">Parto triplo</option>
               </select>
             </td>
            </tr>
          <? }
        }else{ ?>

          <? if($monta_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$monta_['status_nascimento']){ ?> <tr> <? } ?>
          <td>Monta natural</td>
          <td>Lote <?=$lote[0]['codigo']?></td>
          <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td>
          <td>Inicial: <?=$data_inicio?> Final: <?=$data_fim?></td>
          <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
          <td></td>
          <td>
            <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_monta_controle?>, '1')">
             <option value="">Selecionar</option>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select>
         </td>
        </tr>
      <? } }
          //INSEMINAÇÃO
          $inseminacao = DBRead('inseminacao_controle', "WHERE id_femea = '$id_mae'");
          foreach (($inseminacao ?: []) as $inseminacao_) {
            $id_inseminacao = $inseminacao_['id_lote'];
            $id_inseminacao_controle = $inseminacao_['id'];
            $lote = DBRead('inseminacao', "WHERE id = '$id_inseminacao'");
            $id_macho = $lote[0]['id_macho'];
            if($lote[0]['terceiro']){
              $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
            }else{
              $macho = DBRead('animais', "WHERE id = '$id_macho'");
            }


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
            $data_inseminacao = $data;


            $data = explode("/", $data_inseminacao);
            list($dia, $mes, $ano) = $data;
            $data = "$ano$mes$dia";
            $nextdate = addDayIntoDate($data,140);
            $data[0] = $nextdate[6];
            $data[1] = $nextdate[7];
            $data[2] = "/";
            $data[3] = $nextdate[4];
            $data[4] = $nextdate[5];
            $data[5] = "/";
            $data[6] = $nextdate[0];
            $data[7] = $nextdate[1];
            $data[8] = $nextdate[2];
            $data[9] = $nextdate[3];
            $data_previsao1 = $data;

            $data = explode("/", $data_inseminacao);
            list($dia, $mes, $ano) = $data;
            $data = "$ano$mes$dia";
            $nextdate = addDayIntoDate($data,160);
            $data[0] = $nextdate[6];
            $data[1] = $nextdate[7];
            $data[2] = "/";
            $data[3] = $nextdate[4];
            $data[4] = $nextdate[5];
            $data[5] = "/";
            $data[6] = $nextdate[0];
            $data[7] = $nextdate[1];
            $data[8] = $nextdate[2];
            $data[9] = $nextdate[3];
            $data_previsao2 = $data;

            $data_parto_ = $dataNascimentoValidada->format('Y-m-d');

            $data_atual = $data_previsao1;
            $data = '0';
            $data['0'] = $data_atual['6'];
            $data['1'] = $data_atual['7'];
            $data['2'] = $data_atual['8'];
            $data['3'] = $data_atual['9'];
            $data['4'] = "-";
            $data['5'] = $data_atual['3'];
            $data['6'] = $data_atual['4'];
            $data['7'] = "-";
            $data['8'] = $data_atual['0'];
            $data['9'] = $data_atual['1'];
            $data_previsao1_ = $data;

            $data_atual = $data_previsao2;
            $data = '0';
            $data['0'] = $data_atual['6'];
            $data['1'] = $data_atual['7'];
            $data['2'] = $data_atual['8'];
            $data['3'] = $data_atual['9'];
            $data['4'] = "-";
            $data['5'] = $data_atual['3'];
            $data['6'] = $data_atual['4'];
            $data['7'] = "-";
            $data['8'] = $data_atual['0'];
            $data['9'] = $data_atual['1'];
            $data_previsao2_ = $data;

            if($data_parto_ >= '1990-01-01'){
              if(($data_parto_ >= $data_previsao1_) && ($data_parto_ <= $data_previsao2_)){

          ?>
          <? if($inseminacao_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$inseminacao_['status_nascimento']){ ?> <tr> <? } ?>
            <td>Inseminação artificial</td>
            <td>Lote <?=$lote[0]['codigo']?></td>
            <? if($lote[0]['terceiro']){?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <? if(!$lote[0]['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <td><?=$data_inseminacao?></td>
            <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
            <td></td>
            <td>
              <? if($inseminacao_['status_nascimento']){ ?>
            <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_inseminacao_controle?>, '2')" style="color:green;">
            <? }else{ ?> <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_inseminacao_controle?>, '2')"> <? } ?>
              <? if($inseminacao_['status_nascimento']){?> <option value="" style="color:green;">Cadastrado</option> <? }else{ ?>
             <option value="">Selecionar</option><? } ?>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select>
         </td>
          </tr>
        <? } }else{ ?>

          <? if($inseminacao_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$inseminacao_['status_nascimento']){ ?> <tr> <? } ?>
            <td>Inseminação artificial</td>
            <td>Lote <?=$lote[0]['codigo']?></td>
            <? if($lote[0]['terceiro']){?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <? if(!$lote[0]['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <td><?=$data_inseminacao?></td>
            <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
            <td></td>
            <td>
              <select class="form-control select" id="tipo_parto" name="tipo_parto"  onchange="cadastrar_monta(this.value, <?=$id_inseminacao_controle?>, '2')">
             <option value="">Selecionar</option>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select>
         </td>
       </tr> <? } }

          //TE
          $te = DBRead('transplante', "WHERE id_mae = '$id_mae'");
          foreach (($te ?: []) as $te_){
            $id_te = $te_['id'];
            $id_macho = $te_['id_pai'];
            if($te_['terceiro_pai']){
              $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
            }else{
              $macho = DBRead('animais', "WHERE id = '$id_macho'");
            }


            $data = $te_['data'];
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
            $data_te = $data;


            $data = explode("/", $data_te);
            list($dia, $mes, $ano) = $data;
            $data = "$ano$mes$dia";
            $nextdate = addDayIntoDate($data,140);
            $data[0] = $nextdate[6];
            $data[1] = $nextdate[7];
            $data[2] = "/";
            $data[3] = $nextdate[4];
            $data[4] = $nextdate[5];
            $data[5] = "/";
            $data[6] = $nextdate[0];
            $data[7] = $nextdate[1];
            $data[8] = $nextdate[2];
            $data[9] = $nextdate[3];
            $data_previsao1 = $data;

            $data = explode("/", $data_te);
            list($dia, $mes, $ano) = $data;
            $data = "$ano$mes$dia";
            $nextdate = addDayIntoDate($data,160);
            $data[0] = $nextdate[6];
            $data[1] = $nextdate[7];
            $data[2] = "/";
            $data[3] = $nextdate[4];
            $data[4] = $nextdate[5];
            $data[5] = "/";
            $data[6] = $nextdate[0];
            $data[7] = $nextdate[1];
            $data[8] = $nextdate[2];
            $data[9] = $nextdate[3];
            $data_previsao2 = $data;

            $data_parto_ = $dataNascimentoValidada->format('Y-m-d');

            $data_atual = $data_previsao1;
            $data = '0';
            $data['0'] = $data_atual['6'];
            $data['1'] = $data_atual['7'];
            $data['2'] = $data_atual['8'];
            $data['3'] = $data_atual['9'];
            $data['4'] = "-";
            $data['5'] = $data_atual['3'];
            $data['6'] = $data_atual['4'];
            $data['7'] = "-";
            $data['8'] = $data_atual['0'];
            $data['9'] = $data_atual['1'];
            $data_previsao1_ = $data;

            $data_atual = $data_previsao2;
            $data = '0';
            $data['0'] = $data_atual['6'];
            $data['1'] = $data_atual['7'];
            $data['2'] = $data_atual['8'];
            $data['3'] = $data_atual['9'];
            $data['4'] = "-";
            $data['5'] = $data_atual['3'];
            $data['6'] = $data_atual['4'];
            $data['7'] = "-";
            $data['8'] = $data_atual['0'];
            $data['9'] = $data_atual['1'];
            $data_previsao2_ = $data;

            if($data_parto_ >= '1990-01-01'){
              if(($data_parto_ >= $data_previsao1_) && ($data_parto_ <= $data_previsao2_)){

            $lote = DBRead('transplante_controle', "WHERE id_lote = '$id_te'");
            foreach (($lote ?: []) as $lote_){
          ?>
          <? if($lote_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$lote_['status_nascimento']){ ?> <tr> <? } ?>
            <td>T.E.</td>
            <td>Lote <?=$te_['codigo']?></td>
            <? if($te_['terceiro_pai']){ ?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <? if(!$te_['terceiro_pai']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <td><?=$data_te?></td>
            <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
            <td><?=$lote_['receptora']?></td>
            <td><select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$lote_['id']?>, '3')">
             <option value="">Selecionar</option>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select></td>
          </tr>
        <? } } }else{
          $lote = DBRead('transplante_controle', "WHERE id_lote = '$id_te'");
          foreach (($lote ?: []) as $lote_){

          ?>
          <? if($lote_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$lote_['status_nascimento']){ ?> <tr> <? } ?>
            <td>T.E.</td>
            <td>Lote <?=$te_['codigo']?></td>
            <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td>
            <td><?=$data_te?></td>
            <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
            <td><?=$lote_['receptora']?></td>
            <td><select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$lote_['id']?>, '3')">
             <option value="">Selecionar</option>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select></td>
          </tr>
        <? } } } ?>
      </table>
    <div class="col-md-12" style="margin-left:0.2%; margin-top:-1%;">
      <span style="color: green;">Legenda: Cria cadastrada (verde) </span>
    </div>
</div>
<? } } ?>
<!-- FIM NASCIMENTO -->

<? if($tipo == 4){ ?>
<!-- TERCEIRO -->
  <form role="form" action="animal/cadastrar/_terceiros.php" method="post" onsubmit="return ativar_terceiros()">
        <div class="col-md-12" style="margin-left:-30px;">
           <div class="col-md-3">
             <!-- general form elements -->
             <div class="box-body">

               <div class="form-group">
                 <label for="exampleInputPassword1">Nome<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="nome_animal" name="nome_animal">
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">FBB</label>
                 <input type="text" class="form-control" id="fbb" name="fbb" value="<?=$animal[0]['fbb']?>">
               </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">Sexo<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="sexo" name="sexo" onchange="lista_colaborador(this.value)">
                 <option value="">Selecionar</option>
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
                 <label for="exampleInputPassword1">Pai</label>
                 <input type="text" class="form-control" id="pai" name="pai" value="<?=$pai[0]['nome']?>" onKeyUp="pesquisar_pai(this.value)">
                 </div>


               <div class="form-group">
                 <label for="exampleInputPassword1">Mãe</label>
                 <input type="text" class="form-control" id="mae" name="mae" value="<?=$mae[0]['nome']?>" onKeyUp="pesquisar_mae(this.value)">
                </div>


               <div class="form-group">
                 <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="raca" name="raca">
                   <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option>
                   <option></option>
                   <?
                   $raca = DBRead('raca', "ORDER BY nome asc");
                   foreach (($raca ?: []) as $raca_) { ?>
                     <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                   <? } ?>
                 </select>
               </div>
               <div class="form-group">
                 <button type="submit" class="btn btn-success" style="width:100%; margin-top:4%;">Cadastrar animal</button>
               </div>
             </div>
           </div>
        </div>
    </form>
<? } ?>
<!-- FIM TERCEIRO -->

  </div>
</div>
</div>

</div>
</section>
