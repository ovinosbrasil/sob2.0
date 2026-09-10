<?
$id_evento = $_GET['id_exposicao'];
$evento = DBRead('julgamento', "WHERE id = '$id_evento'");

$data = $evento[0]['data'];
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
<script type="text/javascript">
function validar(){
  saida = 0;
  if(!document.getElementById("evento").value){
    document.getElementById("evento").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("evento").style.border = "1px solid green";}

  if(!document.getElementById("data").value){
    document.getElementById("data").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("data").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}

function julgamento(id_animal, status){
  if(status){
    window.location.href = "exposicao/_animal_julgamento.php?id_animal="+id_animal+"&id_evento=<?=$id_evento?>";
  }else{
    window.location.href = "exposicao/_excluir_julgamento.php?id_animal="+id_animal+"&id_evento=<?=$id_evento?>";
  }
}

function leilao(id_animal, status){
  if(status){
    window.location.href = "exposicao/_animal_leilao.php?id_animal="+id_animal+"&id_evento=<?=$id_evento?>";
  }else{
    window.location.href = "exposicao/_excluir_leilao.php?id_animal="+id_animal+"&id_evento=<?=$id_evento?>";
  }
}

function excluir_animal(id_animal){
    window.location.href = "exposicao/_excluir_animal.php?id_animal="+id_animal+"&id_evento=<?=$id_evento?>";
}

function pesquisar_animal_exposicao(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "exposicao/lista_animal.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_animal_exposicao").innerHTML = resposta;
document.getElementById("lista_animal_exposicao").style.display = 'block';
}
}
PP.send(null);
}

function linkar_animal_exposicao(id){
  window.location.href = "exposicao/_cadastrar_animal.php?id_animal="+id+"&id_evento=<?=$id_evento?>";
}

function fechar_lista_animal_exposicao(){
  document.getElementById("lista_animal_exposicao").style.display = 'none';
}

function vender(id_animal){
  window.location.href = "geral.php?pg=vender_animal_exposicao&id_animal="+id_animal+"&id_evento=<?=$id_evento?>";
}
</script>

<section class="content-header">
  <h1>
    Exposição
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-trophy"></i> Exposição</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="exposicao/_alterar.php?id_evento=<?=$id_evento?>" onsubmit="return validar()">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Evento<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="evento" name="evento" value="<?=$evento[0]['nome']?>">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data" name="data" value="<?=$data?>">
                </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Cidade</label>
                  <input type="text" class="form-control" id="cidade" name="cidade" value="<?=$evento[0]['cidade']?>">
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Local</label>
                <input type="text" class="form-control" id="local" name="local" value="<?=$evento[0]['local']?>">
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-warning" style="width:100%; margin-top:4%;">Alterar Exposição</button>
            </div>
        </div>
      </form>
			</div>
      <!-- /.col -->

      <div class="box">
        <div class="form-group" style="text-align:center; margin:2%;">
          <a href="geral.php?pg=premiacao"><button type="submit" class="btn btn-success" style="width:98%; margin-bottom:2%;">Relatório de premiação</button></a>
        </div>
      </div>

  </div>

    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">

          <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputPassword1">Adicionar Animal<span style="color:#F00;">*</span></label>
                <input type="text" class="form-control" id="animal" name="animal" value="<?=$mae[0]['nome']?>" onKeyUp="pesquisar_animal_exposicao(this.value)">
                <div id="lista_animal_exposicao" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:300%; display:none; margin-top:1%;">
                </div>
            </div>
          </div>

          <div class="col-md-6" style="float:right;">
            <div class="form-group">
                <a href="geral.php?pg=julgamento&id_exposicao=<?=$id_evento?>"><button type="submit" class="btn btn-primary" style="width:49%; margin-top:4%;">Pista de julgamento</button></a>
                <a href="geral.php?pg=relatorio_vendas_exposicao&id_exposicao=<?=$id_evento?>"><button type="submit" class="btn btn-success" style="width:49%; margin-top:4%; margin-left:1%">Relatório de vendas</button></a>
            </div>
          </div>

          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Tipo</th>
              <th>Sexo</th>
              <th>Pista de julgamento</th>
              <th>Leilão</th>
              <th>Venda</th>
              <th style="width:3%;">Excluir</th>
            </tr>
            <?
            $julgamento = DBRead('animais_evento', "WHERE id_julgamento = '$id_evento' ORDER BY id_animal desc");
            if (!$julgamento) {
              echo '<tr><td colspan="8" class="text-center">Nenhum animal cadastrado nesta exposição.</td></tr>';
            }
            foreach (($julgamento ?: array()) as $julgamento_){
              $x++;
              $id_animal = $julgamento_['id_animal'];
              $animal = DBRead('animais', "WHERE id = '$id_animal'");
              $venda = DBRead('vendas', "WHERE id_animal = '$id_animal'")
            ?>
            <tr>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animal[0]['id']?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
              <td><?=$animal[0]['tipo']?></td>
              <td><?=$animal[0]['sexo']?></td>
              <td>
                <? if($julgamento_['julgamento']){?>
                  <button type="button" class="btn btn-success" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;">Sim</button>
                  &nbsp;&nbsp;<button type="button" class="btn btn" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="julgamento(<?=$animal[0]['id']?>, 0)">Não</button>
                <? }else{ ?>
                  <button type="button" class="btn btn" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="julgamento(<?=$animal[0]['id']?>, 1)">Sim</button>
                  &nbsp;&nbsp;<button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;">Não</button>
                <? } ?>
              </td>
              <td>
                <? if($julgamento_['leilao']){?>
                  <button type="button" class="btn btn-success" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;">Sim</button>
                  &nbsp;&nbsp;<button type="button" class="btn btn" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="leilao(<?=$animal[0]['id']?>, 0)">Não</button>
                <? }else{ ?>
                  <button type="button" class="btn btn" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="leilao(<?=$animal[0]['id']?>, 1)">Sim</button>
                  &nbsp;&nbsp;<button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;">Não</button>
                <? } ?>
              </td>
              <td>
                <? if($venda[0]['id'] > 0){?>
                  <button type="button" class="btn btn-success" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;">Vendido</button>
                <? }else{ ?>
                  <button type="button" class="btn btn" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="vender(<?=$animal[0]['id']?>)">Vender</button>
                <? } ?>
              </td>
              <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_animal(<?=$animal[0]['id']?>)">X</button></td>
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
