<? $id_lote = $_GET['id_lote']; ?>
<script type="text/javascript">
function validar_te(){
  saida = 0;
  if(!document.getElementById("lote").value){
    document.getElementById("lote").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("lote").style.border = "1px solid green";}

  if(!document.getElementById("data_inicial").value){
    document.getElementById("data_inicial").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data_inicial").style.border = "1px solid green";}

  if(!document.getElementById("pai").value){
    document.getElementById("pai").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("pai").style.border = "1px solid green";}

  if(!document.getElementById("mae").value){
    document.getElementById("mae").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("mae").style.border = "1px solid green";}


  if(!document.getElementById("embrioes").value){
    document.getElementById("embrioes").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("embrioes").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function ativar_femea(){
  saida = 0;
  if(!document.getElementById("receptora").value){
    document.getElementById("receptora").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("receptora").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function cadastrar_monta(x,id_lote,tipo){
  if(x){
    window.location.href = "geral.php?pg=cadastrar_nascimento&x="+x+"&id_lote="+id_lote+"&tipo="+tipo;
  }
}

function excluir_femea(id_controle){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/te/palco_excluir_femea.php?id_controle="+id_controle+"&id_lote=+<?=$id_lote?>";
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

function ativar_excluir_femea(id_controle,id_lote){
    window.location.href = "reproducao/te/_excluir_femea.php?id_controle="+id_controle+"&id_lote="+id_lote;
}

function cadastrar_ultrassom(status, id_lote, id_controle){
    window.location.href = "reproducao/te/_cadastrar_ultrassom.php?id_lote="+id_lote+"&status="+status+"&id_controle="+id_controle;
}

function excluir_lote(){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/te/palco_excluir_lote.php?id_lote=<?=$id_lote?>";
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
    window.location.href = "reproducao/te/_excluir_lote.php?id_lote="+id_lote;
}

function atualizar_numero(valor, id_lote){
    window.location.href = "reproducao/te/_n_embrioes.php?id_lote="+id_lote+"&valor="+valor+"&id_te=<?=$id_lote?>";
}
</script>

<?
$te = DBRead('transplante', "WHERE id = '$id_lote'");
$id_macho = $te[0]['id_pai'];
if(!$te[0]['terceiro_pai']){
  $macho = DBRead('animais', "WHERE id = '$id_macho'");
}else{
  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
}
$id_femea = $te[0]['id_mae'];
if(!$te[0]['terceiro_mae']){
  $femea = DBRead('animais', "WHERE id = '$id_femea'");
}else{
  $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
}
$data = $te[0]['data'];
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

$data = $te[0]['data_coleta'];
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
$data_coleta = $data;
?>


<section class="content-header">
  <h1>
    Exibir Transplante de embriões
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Exibir Transplante de embriões</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="reproducao/te/_alterar.php?id_lote=<?=$id_lote?>" onsubmit="return validar_te()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Lote<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="lote" name="lote" value="<?=$te[0]['codigo']?>">
            </div>


            <div class="form-group">
                <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$data_te?>">
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
              <label for="exampleInputPassword1">Fêmea<span style="color:#F00;">*</span>
                <a href="geral.php?pg=cadastrar_animal&tipo=2" target="_blank"><span style="font-size:11px; color:green;">Novo</span></a></label>
              <input type="text" class="form-control" id="mae" name="femea" onKeyUp="pesquisar_mae(this.value)" value="<?=$femea[0]['nome']?>">
              <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
              </div>
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="raca" name="raca">
                <option value="<?=$te[0]['raca']?>"><?=$te[0]['raca']?></option>
                <option></option>
                <?
                $raca = DBRead('raca', "ORDER BY nome asc");
                foreach ($raca as $raca_) { ?>
                  <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                <? } ?>
              </select>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Tipo de Sêmen<span style="color:#F00;">*</span></label>
                <select class="form-control select" id="tipo_semen" name="tipo_semen">
                  <? if($te[0]['tipo_semen'] != ""){ ?><option value="<?=$te[0]['tipo_semen']?>"><?=$te[0]['tipo_semen']?></option> <? }else{?> <option value="">Selecionar</option><? } ?>
                  <option></option>
                  <option value="A fresco">A fresco</option>
                  <option value="Congelado">Congelado</option>
                </select>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Embriões coletados<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="embrioes" name="embrioes" value="<?=$te[0]['qtd']?>">
            </div>


            <div class="form-group">
                <label for="exampleInputPassword1">Embriões congelados<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="congelados" name="congelados" value="<?=$te[0]['congelados']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Embriões usados<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="usados" name="usados" value="<?=$te[0]['usados']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data coleta<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data" name="data_coleta" value="<?=$data_coleta?>">
                </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-warning" style="width:100%; margin-top:4%;">Alterar lote</button>
              <a href="reproducao/te/_imprimir.php?id_lote=<?=$id_lote?>" target="_blank"><button type="button" class="btn btn-primary" style="width:100%; margin-top:4%;">Imprimir lote</button></a>
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
          <form method="post" action="reproducao/te/_cadastrar_femea.php?id_lote=<?=$id_lote?>" onsubmit="return ativar_femea()">
          <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputPassword1">Adicionar fêmea<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="receptora" name="receptora"/>
                </div>
            </div>

          <div class="col-md-3">
            <div class="form-group">
              <button type="submit" class="btn btn-success" style="width:100%; margin-top:8%;">Adicionar receptora</button>
            </div>
          </div>
        </form>


          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th style="width:5%;">Nº</th>
              <th>Receptora</th>
              <th style="width:15%;">Nº de embriões</th>
              <th style="width:20%;">Ultrassom</th>
              <th style="width:20%;">Nascimento</th>
              <th style="width:3%;">Excluir</th>
            </tr>
            <?
            $transplante_controle = DBRead('transplante_controle', "WHERE id_lote = '$id_lote'");
            foreach ($transplante_controle as $transplante_controle_){
              $id_transplante_controle = $transplante_controle_['id'];
              $x++;
            ?>
            <tr>
              <td><?=$x?></td>
              <td><?=$transplante_controle_['receptora']?></td>
              <td><input type="text" class="form-control" id="receptora" name="receptora" value="<?=$transplante_controle_['n_embrioes']?>" onblur="atualizar_numero(this.value,'<?=$id_transplante_controle?>')"/></td>
              <td>

              <? if($transplante_controle_['ultrassom'] == 1){ ?>
                <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_lote?>, <?=$id_transplante_controle?>)" style="color:green;">
                  <option value="1">Positivo</option>
              <? } ?>
              <? if($transplante_controle_['ultrassom'] == 2){ ?>
                <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_lote?>, <?=$id_transplante_controle?>)" style="color:red;">
                  <option value="1">Negativo</option>
              <? } ?>
              <? if($transplante_controle_['ultrassom'] == 0){ ?>
                <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_lote?>, <?=$id_transplante_controle?>)">
                  <option value="1">Selecionar</option>
              <? } ?>
             <option value=""></option>
             <option value="1">Positivo</option>
             <option value="2">Negativo</option>
           </select></td>
              <td>
                <? if($transplante_controle_['status_nascimento']){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_transplante_controle?>, '3')" style="color:green;">
                    <option value="">Cria cadastrada</option>
                <? } ?>

                <? if($transplante_controle_['status_nascimento'] == 0){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_transplante_controle?>, '3')">
                    <option value="">Selecionar</option>
                <? } ?>
               <option value=""></option>
               <option value="1">Parto simples</option>
               <option value="2">Parto duplo</option>
               <option value="3">Parto triplo</option>
             </select></td>
              <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_femea(<?=$id_transplante_controle?>)">X</button></td>
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
