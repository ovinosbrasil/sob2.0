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


function excluir_venda(id){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "exposicao/palco_excluir_venda.php?id="+id;
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

function fechar_excluir_venda(){
  document.getElementById("transparencia").style.display = 'none';
  document.getElementById("palco_excluir").style.display = 'none';
}

function ativar_excluir_venda(id){
    window.location.href = "exposicao/_excluir_venda.php?id_animal="+id+"&id_evento=<?=$id_evento?>";
}
</script>

<section class="content-header">
  <h1>
  &nbsp;
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-trophy"></i> Exposição</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">

    <div class="col-md-12">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <div class="col-md-4" style="margin-top:0%;">
            <div class="form-group">
                <h3>Relatório de vendas - <?=$evento[0]['nome']?></h3>
            </div>
          </div>

          <div class="col-md-4" style="float:right;">
            <div class="form-group">
                <a href="geral.php?pg=exposicao&id_exposicao=<?=$id_evento?>"><button type="submit" class="btn btn-primary" style="width:49%; margin-top:2%;">Dados do evento</button></a>
                <a href="geral.php?pg=julgamento&id_exposicao=<?=$id_evento?>"><button type="submit" class="btn btn-primary" style="width:49%; margin-top:2%;">Pista de julgamento</button></a>
            </div>
          </div>

          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Data</th>
              <th>Animal</th>
              <th>Comprador</th>
              <th>Valor</th>
              <th>Parcelas</th>
              <th>Tipo de venda</th>
              <th>Pagamento</th>
              <th>Excluir</th>
            </tr>
            <?
            $animal = DBRead('animais_evento', "WHERE id_julgamento = '$id_evento'");
            foreach ($animal as $animal_){
              $id_animal = $animal_['id_animal'];
              $venda = DBRead('vendas', "WHERE id_animal = '$id_animal' AND (tipo_venda = 'Exposição' || tipo_venda = 'Leilão')");
              if($venda[0]['id'] > 0){
                $x++;
                $animal = DBRead('animais', "WHERE id = '$id_animal'");
                $id_comprador = $venda[0]['comprador'];
                $comprador = DBRead('mercado', "WHERE id = '$id_comprador'");
                $data = $venda[0]['data'];
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
            <tr>
              <td><?=$x?></td>
              <td><?=$data?></td>
              <td onclick="abrir_animal(<?=$animal[0]['id']?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
              <td><?=$comprador[0]['nome']?></td>
              <td>R$ <?=number_format($venda[0]['preco_de_venda'],2,",",".");?></td>
              <td><?=$venda[0]['parcelas']?></td>
              <td><?=$venda[0]['tipo_venda']?></td>
              <td><?=$venda[0]['forma_de_pagamento']?></td>
              <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_venda(<?=$animal[0]['id']?>)">X</button></td>
              </tr>
            <? }} ?>
            </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
