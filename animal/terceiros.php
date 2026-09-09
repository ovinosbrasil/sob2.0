<script type="text/javascript">
function terceiros(){
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


function excluir_terceiro(id_animal){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "animal/palco_excluir_terceiro.php?id_animal="+id_animal;
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

function fechar_excluir_terceiro(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_terceiro(id_animal){
    window.location.href = "animal/_excluir_terceiro.php?id_animal="+id_animal;
}
</script>



<?
$id_animal = $_GET['id_animal'];
$animal = DBRead('terceiros', "WHERE id = '$id_animal'");
?>

<section class="content-header">
  <h1>
    <? echo $animal[0]['nome']; ?> (Terceiros)
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-plus"></i> Terceiros</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <!-- left column -->
    <div class="col-md-6">
      <!-- general form elements -->
      <div class="box box-success">
        <!-- form start -->
          <div class="box-body">
            <form role="form" action="animal/_alterar_terceiro.php?id_animal=<?=$id_animal?>" method="post" onsubmit="return terceiros()">
                  <div class="col-md-12" style="margin-left:-30px;">
                     <div class="col-md-6">
                       <!-- general form elements -->
                       <div class="box-body">

                         <div class="form-group">
                           <label for="exampleInputPassword1">Nome<span style="color:#F00;">*</span></label>
                           <input type="text" class="form-control" id="nome_animal" name="nome_animal" value="<?=$animal[0]['nome']?>">
                         </div>

                         <div class="form-group">
                           <label for="exampleInputPassword1">FBB</label>
                           <input type="text" class="form-control" id="fbb" name="fbb" value="<?=$animal[0]['fbb']?>">
                         </div>

                         <div class="form-group">
                           <label for="exampleInputPassword1">Sexo<span style="color:#F00;">*</span></label>
                           <select class="form-control select" id="sexo" name="sexo" onchange="lista_colaborador(this.value)">
                             <option value="<?=$animal[0]['sexo']?>"><?=$animal[0]['sexo']?></option>
                             <option value=""></option>
                             <option value="Macho">Macho</option>
                             <option value="Fêmea">Fêmea</option>
                           </select>
                         </div>

                       </div>
                     </div>



                     <div class="col-md-6">
                       <!-- general form elements -->
                       <div class="box-body">
                         <div class="form-group">
                           <label for="exampleInputPassword1">Pai</label>
                           <input type="text" class="form-control" id="pai" name="pai" value="<?=$animal[0]['pai']?>">
                           </div>


                         <div class="form-group">
                           <label for="exampleInputPassword1">Mãe</label>
                           <input type="text" class="form-control" id="mae" name="mae" value="<?=$animal[0]['mae']?>">
                          </div>


                         <div class="form-group">
                           <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
                           <select class="form-control select" id="raca" name="raca">
                             <option value="<?=$animal[0]['raca']?>"><?=$animal[0]['raca']?></option>
                             <option></option>
                             <?
                             $raca = DBRead('raca', "ORDER BY nome asc");
                             foreach ($raca as $raca_) { ?>
                               <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                             <? } ?>
                           </select>
                         </div>
                         <div class="form-group">
                           <button type="submit" class="btn btn-warning" style="width:100%; margin-top:4%;">Alterar animal</button>
                         </div>
                         <div class="form-group">
                           <button type="button" class="btn btn-danger" style="width:100%; margin-top:4%;" onclick="excluir_terceiro(<?=$id_animal?>)">Excluir animal</button>
                         </div>
                       </div>
                     </div>
                  </div>
              </form>
          </div>
        </div>
   </div>

   <div class="col-md-6">
     <!-- general form elements -->
     <div class="box box-success">
       <!-- form start -->
         <div class="box-body">
           Lista de crias
           <table class="table table-bordered" id="tabela_padrao">
             <tr>
               <th>Animal</th>
               <th>Data de nascimento</th>
               <th>sexo</th>
               <th>Tipo</th>
               <th>Status</th>
             </tr>
             <?
             if($animal[0]['sexo'] == "Macho"){ $crias = DBRead('animais', "WHERE pai = '$id_animal' AND terceiro_pai = '1' ORDER BY data_de_nascimento desc"); }
             if($animal[0]['sexo'] == "Fêmea"){ $crias = DBRead('animais', "WHERE mae = '$id_animal' AND terceiro_mae = '1' ORDER BY data_de_nascimento desc"); }
             foreach ($crias as $crias_) {

               $data = $crias_['data_de_nascimento'];
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

              <?
              if($crias_['status'] == '0'){ ?> <tr style="color:#2a8595;"><? }
              if($crias_['status'] == '1'){ ?> <tr style="color:red;"><? }
              if($crias_['status'] == '2'){ ?> <tr style="color:green;"><? } ?>
               <td style="cursor:pointer;" onclick="abrir_animal(<?=$crias_['id']?>)"><?=$crias_['nome']?></td>
               <td><?=$data_nascimento?></td>
               <td><?=$crias_['sexo']?></td>
               <td><?=$crias_['tipo']?></td>
               <td>
               <? if($crias_['status'] == '0'){ ?> <span style="color:#37abc0;">Rebanho<? }
               if($crias_['status'] == '1'){ ?> <span style="color:red;">Morto<? }
               if($crias_['status'] == '2'){ ?> <span style="color:green;">Vendido<? }
               if($crias_['status'] == '3'){ ?> <span style="color:red;">Empréstimo<? }
               if($crias_['status'] == '4'){ ?> <span style="color:red;">Doação<? }
               if($crias_['status'] == '5'){ ?> <span style="color:red;">Abate<? } ?>
               </td>
             </tr>
           <? } ?>
             </table>
         </div>
       </div>
  </div>
</div>
</section>
