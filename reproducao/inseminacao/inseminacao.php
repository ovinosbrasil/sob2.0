<? $id_lote = $_GET['id_lote']; ?>
<script type="text/javascript">
function focus(){
  document.getElementById("mae").focus();
}

function validar_inseminacao(){
  saida = 0;
  if(!document.getElementById("lote").value){
    document.getElementById("lote").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("lote").style.border = "1px solid green";}

  if(!document.getElementById("data_inicial").value){
    document.getElementById("data_inicial").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_inicial").style.border = "1px solid green";}

  if(!document.getElementById("semen").value){
    document.getElementById("semen").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("semen").style.border = "1px solid green";}

  if(!document.getElementById("pai").value){
    document.getElementById("pai").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("pai").style.border = "1px solid green";}

  if(!document.getElementById("raca").value){
    document.getElementById("raca").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("raca").style.border = "1px solid green";}

  if(!document.getElementById("notificacao").value){
    document.getElementById("notificacao").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("notificacao").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function ativar_femea(){
  saida = 0;
  if(!document.getElementById("mae").value){
    document.getElementById("mae").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("mae").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function cadastrar_inseminacao(x,id_lote,tipo){
  if(x){
    window.location.href = "geral.php?pg=cadastrar_nascimento&x="+x+"&id_lote="+id_lote+"&tipo="+tipo;
  }
}

function excluir_femea(id_mae){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/inseminacao/palco_excluir_femea.php?id_mae="+id_mae+"&id_lote=+<?=$id_lote?>";
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

function fechar_excluir_femea(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_femea(id_mae,id_lote){
    window.location.href = "reproducao/inseminacao/_excluir_femea.php?id_mae="+id_mae+"&id_lote="+id_lote;
}

function cadastrar_ultrassom(status, id_lote){
    window.location.href = "reproducao/inseminacao/_cadastrar_ultrassom.php?id_lote="+id_lote+"&status="+status;
}


function excluir_lote(){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/inseminacao/palco_excluir_lote.php?id_lote=<?=$id_lote?>";
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

function fechar_excluir_lote(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_lote(id_lote){
    window.location.href = "reproducao/inseminacao/_excluir_lote.php?id_lote="+id_lote;
}
</script>

<?
$id_lote = $_GET['id_lote'];
$inseminacao = DBRead('inseminacao', "WHERE id = '$id_lote'");
$id_macho = $inseminacao[0]['id_macho'];
if(!$inseminacao[0]['terceiro']){
  $macho = DBRead('animais', "WHERE id = '$id_macho'");
}else{
  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
}
$data = $inseminacao[0]['data'];
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
$data = $data;
?>


<section class="content-header">
  <h1>
    Inseminação artificial
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Exibir Inseminação artificial</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="reproducao/inseminacao/_alterar.php?id_lote=<?=$id_lote?>" onsubmit="return validar_inseminacao()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Lote<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="lote" name="lote" value="<?=$inseminacao[0]['codigo']?>">
            </div>


            <div class="form-group">
                <label for="exampleInputPassword1">Data inicial<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$data?>">
                </div>
            </div>


            <div class="form-group">
              <label for="exampleInputPassword1">Macho<span style="color:#F00;">*</span>
                <a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"><span style="font-size:11px; color:green;">Novo</span></a></label>
              <input type="text" class="form-control" id="pai" name="macho" onKeyUp="pesquisar_pai(this.value)" value="<?=$macho[0]['nome']?>">
              <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
              </div>
            </div>


            <div class="form-group">
              <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="raca" name="raca">
                <option value="<?=$inseminacao[0]['raca']?>"><?=$inseminacao[0]['raca']?></option>
                <option></option>
                <?
                $raca = DBRead('raca', "ORDER BY nome asc");
                foreach ($raca as $raca_) { ?>
                  <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                <? } ?>
              </select>
            </div>


            <div class="form-group">
              <label for="exampleInputPassword1">Sêmen<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="semen" name="semen">
                <option value="<?=$inseminacao[0]['semen']?>"><?=$inseminacao[0]['semen']?></option>
                <option value=""></option>
                <option value="A fresco">A fresco</option>
                <option value="Congelado">Congelado</option>
                <option value="Refrigerado">Refrigerado</option>
              </select>
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Notificação<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="notificacao" name="notificacao">
                <option value="<?=$inseminacao[0]['notificacao']?>"><?=$inseminacao[0]['notificacao']?></option>
                <option></option>
                <option value="PO">PO</option>
                <option value="PC">PC</option>
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-warning" style="width:100%; margin-top:4%;">Alterar lote</button>
              <a href="reproducao/inseminacao/_imprimir.php?id_lote=<?=$id_lote?>" target="_blank"><button type="button" class="btn btn-primary" style="width:100%; margin-top:4%;">Imprimir lote</button></a>
              <a onclick="excluir_lote()"><button type="button" class="btn btn-danger" style="width:100%; margin-top:4%;">Excluir lote</button></a>
            </div>

        </div>
      </form>
			</div>
      <!-- /.col -->
    </div>

    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <form method="post" action="reproducao/inseminacao/_cadastrar_femea.php?id_lote=<?=$id_lote?>" onsubmit="return ativar_femea()">
          <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputPassword1">Adicionar fêmea<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="mae" name="mae" value="<?=$mae[0]['nome']?>" onKeyUp="pesquisar_mae(this.value)">
                <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
                </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <button type="submit" class="btn btn-success" style="width:100%; margin-top:8%;">Adicionar fêmea</button>
            </div>
          </div>
        </form>


          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Fêmea</th>
              <th>Tipo</th>
              <th style="width:20%;">Ultrassom</th>
              <th style="width:20%;">Nascimento</th>
              <th style="width:3%;">Excluir</th>
            </tr>
            <?
            $inseminacao_controle = DBRead('inseminacao_controle', "WHERE id_lote = '$id_lote'");
            foreach ($inseminacao_controle as $inseminacao_controle_){
              $x++;
              $id_animal = $inseminacao_controle_['id_femea'];
              $id_inseminacao_controle = $inseminacao_controle_['id'];
              if(!$inseminacao_controle_['terceiro']){
                $femea = DBRead('animais', "WHERE id = '$id_animal'");
              }else{
                $femea = DBRead('terceiros', "WHERE id = '$id_animal'");
              }
            ?>
            <tr>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$femea[0]['id']?>)" style="cursor:pointer;" ><?=$femea[0]['nome']?></td>
              <td><?=$femea[0]['tipo']?></td>
              <td>

              <? if($inseminacao_controle_['ultrassom'] == 1){ ?>
                <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_inseminacao_controle?>)" style="color:green;">
                  <option value="1">Positivo</option>
              <? } ?>
              <? if($inseminacao_controle_['ultrassom'] == 2){ ?>
                <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_inseminacao_controle?>)" style="color:red;">
                  <option value="1">Negativo</option>
              <? } ?>
              <? if($inseminacao_controle_['ultrassom'] == 0){ ?>
                <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_inseminacao_controle?>)">
                  <option value="1">Selecionar</option>
              <? } ?>
             <option value=""></option>
             <option value="1">Positivo</option>
             <option value="2">Negativo</option>
           </select></td>
              <td>
                <? if($inseminacao_controle_['status_nascimento']){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_inseminacao(this.value, <?=$id_inseminacao_controle?>, '2')" style="color:green;">
                    <option value="">Cria cadastrada</option>
                <? } ?>

                <? if($inseminacao_controle_['status_nascimento'] == 0){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_inseminacao(this.value, <?=$id_inseminacao_controle?>, '2')">
                    <option value="">Selecionar</option>
                <? } ?>
               <option value=""></option>
               <option value="1">Parto simples</option>
               <option value="2">Parto duplo</option>
               <option value="3">Parto triplo</option>
             </select></td>
              <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_femea(<?=$femea[0]['id']?>)">X</button></td>
              </tr>
            <? } ?>
            </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
