<?php
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
$data_evento = $data;
?>


<script type="text/javascript">
function excluir_julgamento(id_animal){
    window.location.href = "exposicao/_excluir_julgamento.php?id_animal="+id_animal+"&id_evento=<?=$id_evento?>&tipo=1";
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
  window.location.href = "exposicao/_cadastrar_animal_completo.php?id_animal="+id+"&id_evento=<?=$id_evento?>";
}

function fechar_lista_animal_exposicao(){
  document.getElementById("lista_animal_exposicao").style.display = 'none';
}
</script>



<section class="content-header">
  <h1>
    Pista de julgamento - <?=$evento[0]['nome']?>
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
                <a href="geral.php?pg=exposicao&id_exposicao=<?=$id_evento?>"><button type="submit" class="btn btn-primary" style="width:49%; margin-top:4%;">Dados do evento</button></a>
                <a href="geral.php?pg=relatorio_vendas_exposicao&id_exposicao=<?=$id_evento?>"><button type="submit" class="btn btn-success" style="width:49%; margin-top:4%; margin-left:1%">Relatório de vendas</button></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <h3 style="margin-top:0%;">Lista dos Machos</h3>
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Tipo</th>
              <th>Pai</th>
              <th>Mãe</th>
              <th>Nascimento</th>
              <th>Idade</th>
              <th>Categoria</th>
              <th style="width:3%;">Excluir</th>
            </tr>
            <?
            $julgamento = DBRead('julgamento_controle', "WHERE id_julgamento = '$id_evento' ORDER BY categoria asc");
            foreach ($julgamento as $julgamento_){
              $id_animal = $julgamento_['id_animal'];
              $animal = DBRead('animais', "WHERE id = '$id_animal'");
              if($animal[0]['sexo'] == "Macho"){
                $x++;
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

                $data = $data_nascimento;
                list($dia, $mes, $ano) = explode('/', $data);
                list($dia_evento, $mes_evento, $ano_evento) = explode('/', $data_evento);
                // Descobre que dia é hoje e retorna a unix timestamp
                $data_do_evento = mktime(0, 0, 0, $mes_evento, $dia_evento, $ano_evento);
                // Descobre a unix timestamp da data de nascimento do fulano
                $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
                // Depois apenas fazemos o cálculo já citado :)
                $anos = floor((((($data_do_evento - $nascimento) / 60) / 60) / 24));
                $idade_anos  = floor($anos /365);
                $idade_meses = (($anos /365) - $idade_anos) * 12;
                $idade_meses = (int)$idade_meses;
                $idade_meses = round($idade_meses);

            ?>
            <tr>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animal[0]['id']?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
              <td><?=$animal[0]['tipo']?></td>
              <td><?=$pai[0]['nome']?></td>
              <td><?=$mae[0]['nome']?></td>
              <td><?=$data_nascimento?></td>
              <td><?=$idade_anos?>A <?=$idade_meses?>M</td>
              <td><?=$julgamento_['categoria']?></td>
              <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_julgamento(<?=$animal[0]['id']?>)">X</button></td>
              </tr>
            <? } } ?>
            </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

    <div class="col-md-9" style="float:right;">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <h3 style="margin-top:0%;">Lista das Fêmeas</h3>
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Tipo</th>
              <th>Pai</th>
              <th>Mãe</th>
              <th>Nascimento</th>
              <th>Idade</th>
              <th>Categoria</th>
              <th style="width:3%;">Excluir</th>
            </tr>
            <?
            $x=0;
            $julgamento = DBRead('julgamento_controle', "WHERE id_julgamento = '$id_evento' ORDER BY categoria asc");
            foreach ($julgamento as $julgamento_){
              $id_animal = $julgamento_['id_animal'];
              $animal = DBRead('animais', "WHERE id = '$id_animal'");
              if($animal[0]['sexo'] == "Fêmea"){
                $x++;
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

                $data = $data_nascimento;
                list($dia, $mes, $ano) = explode('/', $data);
                list($dia_evento, $mes_evento, $ano_evento) = explode('/', $data_evento);
                // Descobre que dia é hoje e retorna a unix timestamp
                $data_do_evento = mktime(0, 0, 0, $mes_evento, $dia_evento, $ano_evento);
                // Descobre a unix timestamp da data de nascimento do fulano
                $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
                // Depois apenas fazemos o cálculo já citado :)
                $anos = floor((((($data_do_evento - $nascimento) / 60) / 60) / 24));
                $idade_anos  = floor($anos /365);
                $idade_meses = (($anos /365) - $idade_anos) * 12;
                $idade_meses = (int)$idade_meses;
                $idade_meses = round($idade_meses);

            ?>
            <tr>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animal[0]['id']?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
              <td><?=$animal[0]['tipo']?></td>
              <td><?=$pai[0]['nome']?></td>
              <td><?=$mae[0]['nome']?></td>
              <td><?=$data_nascimento?></td>
              <td><?=$idade_anos?>A <?=$idade_meses?>M</td>
              <td><?=$julgamento_['categoria']?></td>
              <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_julgamento(<?=$animal[0]['id']?>)">X</button></td>
              </tr>
            <? } } ?>
            </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

    <div class="col-md-9" style="float:right;">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <h3 style="margin-top:0%;">Progênie de pai</h3>
          <?
          $progenie = DBRead('progene',"WHERE id_lote = '$id_evento' AND qtd > '1' AND sexo = 'M'");
          if($progenie[0]['id'] > 0){
           foreach ($progenie as $progenie_){
             $id_animal = $progenie_['id_animal'];
             $pai = DBRead('animais', "WHERE id = '$id_animal'");
          ?>
              <table class="table table-bordered" id="tabela_padrao" style="margin-bottom:1.5%; width:70%;">
                <tr>
                  <th><?=$pai[0]['nome']?></th>
                </tr>
                <tr>
                  <th>Cria</th>
                  <th>Mãe</th>
                  <th>Sexo</th>
                  <th>Tipo</th>
                </tr>
                <?
                $crias = DBRead('julgamento_controle', "WHERE id_julgamento = '$id_evento' AND pai = '$id_animal'");
                foreach ($crias as $crias_) {
                  $id_cria = $crias_['id_animal'];
                  $cria = DBRead('animais', "WHERE id = '$id_cria'");
                  $id_mae = $cria[0]['mae'];
                  if($cria[0]['terceiro_mae']){
                    $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
                  }else{
                    $mae = DBRead('animais', "WHERE id = '$id_mae'");
                  }
                ?>
                <tr>
                  <td><?=$cria[0]['nome']?></td>
                  <td><?=$mae[0]['nome']?></td>
                  <td><?=$cria[0]['sexo']?></td>
                  <td><?=$cria[0]['tipo']?></td>
                </tr>
              <? } ?>
                </table>
          <? } } ?>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

    <div class="col-md-9" style="float:right;">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <h3 style="margin-top:0%;">Progênie de mãe</h3>
          <?
          $progenie = DBRead('progene',"WHERE id_lote = '$id_evento' AND qtd > '1' AND sexo = 'F'");
          if($progenie[0]['id'] > 0){
           foreach ($progenie as $progenie_){
             $id_animal = $progenie_['id_animal'];
             $mae = DBRead('animais', "WHERE id = '$id_animal'");
          ?>
              <table class="table table-bordered" id="tabela_padrao" style="margin-bottom:1.5%; width:70%;">  
                <tr>
                  <th><?=$mae[0]['nome']?></th>
                </tr>
                <tr>
                  <th>Cria</th>
                  <th>Pai</th>
                  <th>Sexo</th>
                  <th>Tipo</th>
                </tr>
                <?
                $crias = DBRead('julgamento_controle', "WHERE id_julgamento = '$id_evento' AND mae = '$id_animal'");
                foreach ($crias as $crias_) {
                  $id_cria = $crias_['id_animal'];
                  $cria = DBRead('animais', "WHERE id = '$id_cria'");
                  $id_pai = $cria[0]['pai'];
                  if($cria[0]['terceiro_pai']){
                    $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
                  }else{
                    $pai = DBRead('animais', "WHERE id = '$id_pai'");
                  }
                ?>
                <tr>
                  <td><?=$cria[0]['nome']?></td>
                  <td><?=$pai[0]['nome']?></td>
                  <td><?=$cria[0]['sexo']?></td>
                  <td><?=$cria[0]['tipo']?></td>
                </tr>
              <? } ?>
                </table>
          <? } } ?>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>

  </div>
</section>
  <!-- /.content -->
