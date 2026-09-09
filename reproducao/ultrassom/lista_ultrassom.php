<?
$tipo = $_GET['tipo'];
$reproducao = $_GET['reproducao'];
$id_animal = $_GET['id_animal'];
$terceiro = $_GET['terceiro'];

if($id_animal){
  if($terceiro){
    $animal = DBRead('terceiros', "WHERE id = '$id_animal'");
  }else{
  $animal = DBRead('animais', "WHERE id = '$id_animal'");
}}


?>

<script type="text/javascript">
function focus(){
  document.getElementById("mae").focus();
}

function pesquisar_mae_(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/ultrassom/lista_mae.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_mae_").innerHTML = resposta;
document.getElementById("lista_mae_").style.display = 'block';
}
}
PP.send(null);
}

function linkar_animal_ultrassom(id, terceiro){
  window.location.href = "geral.php?pg=lista_ultrassom&id_animal="+id+"&tipo=0&terceiro="+terceiro;
}

function fechar_lista_mae_ultrassom(){
  document.getElementById("lista_mae_").style.display = 'none';
}

function atualizar_ultrassom(x){
  window.location.href = "geral.php?pg=lista_ultrassom&tipo="+x;
}
function atualizar_ultrassom2(y){
  window.location.href = "geral.php?pg=lista_ultrassom&reproducao="+y+"&tipo=1";
}

function linkar_lote(id_lote, reproducao){
  window.location.href = "geral.php?pg=lista_ultrassom&id_lote="+id_lote+"&reproducao="+reproducao+"&tipo=1";
}

function cadastrar_ultrassom(status, id_lote, id_lote2, reproducao){
    window.location.href = "reproducao/ultrassom/_cadastrar_ultrassom.php?id_lote="+id_lote+"&status="+status+"&id_lote2="+id_lote2+"&reproducao="+reproducao+"&tipo=1";
}
function cadastrar_ultrassom2(status, id_lote, id_lote2, reproducao, terceiro){
    window.location.href = "reproducao/ultrassom/_cadastrar_ultrassom.php?id_lote="+id_lote+"&status="+status+"&id_lote2="+id_lote2+"&reproducao="+reproducao+"&tipo=0&id_animal=<?=$id_animal?>&terceiro="+terceiro;
}
</script>

<section class="content-header">
  <h1>
    Pesquisar Ultrassom
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Ultrassom</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Tipo</label>
                <select class="form-control select"  name="profissional" id="profissional" onchange="atualizar_ultrassom(this.value)">
                  <? if(!$tipo){ ?><option value="0">Animal</option> <? } ?>
                  <? if($tipo){ ?><option value="1">Lote de reprodução</option> <? } ?>
                  <option value=""></option>
                  <option value="0">Animal</option>
                  <option value="1">Lote de reprodução</option>
                </select>
            </div>

            <? if(!$tipo){?>

            <form method="post" action="reproducao/ultrassom/_pesquisar_animal.php">
              <div class="form-group">
                  <label for="exampleInputPassword1">Animal</label>
                  <input type="text" class="form-control" id="mae" name="mae" value="<?=$animal[0]['nome']?>" onKeyUp="pesquisar_mae_(this.value)">
                  <div id="lista_mae_" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
                  </div>
              </div>
          </form>
          <? } ?>

          <? if($tipo){ ?>
            <div class="form-group">
                  <label for="exampleInputPassword1">Tipo de reprodução</label>
                  <select class="form-control select"  name="profissional" id="profissional" onchange="atualizar_ultrassom2(this.value)">
                    <? if($reproducao == 0){ ?><option value="0">Monta natural</option> <? } ?>
                    <? if($reproducao == 1){ ?><option value="1">Inseminação artifical</option> <? } ?>
                    <? if($reproducao == 2){ ?><option value="2">Transplante de embriões</option> <? } ?>
                    <? if($reproducao == ''){ ?><option value="">Selecionar tipo</option> <? } ?>
                    <option value=""></option>
                    <option value="0">Monta natural</option>
                    <option value="1">Inseminação artificial</option>
                    <option value="2">Transplante de embriões</option>
                  </select>
              </div>


              <? if($reproducao == 0){ ?>
              <div class="form-group">
                  <label for="exampleInputPassword1">Lote</label>
                  <select class="form-control select" onchange="linkar_lote(this.value, 0)" name="profissional" id="profissional">
                    <option value="">Selecionar</option>
                    <option value=""></option>
                    <?
                    $lote = DBRead('monta', "ORDER BY id desc");
                    foreach ($lote as $lote_) {
                      $id_macho = $lote_['id_animal'];
                      if($lote_['terceiro']){
                        $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
                      }else{
                        $macho = DBRead('animais', "WHERE id = '$id_macho'");
                      }
                    ?>
                      <option value="<?=$lote_['id']?>"><?=$lote_['codigo']?> - <?=$macho[0]['nome']?></option>
                    <?}?>
                  </select>
              </div>
              <? } ?>


              <? if($reproducao == 1){ ?>
              <div class="form-group">
                  <label for="exampleInputPassword1">Lote</label>
                  <select class="form-control select" onchange="linkar_lote(this.value, 1)" name="profissional" id="profissional">
                    <option value="">Selecionar</option>
                    <option value=""></option>
                    <?
                    $lote = DBRead('inseminacao', "ORDER BY id desc");
                    foreach ($lote as $lote_) {
                      $id_macho = $lote_['id_macho'];
                      if($lote_['terceiro']){
                        $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
                      }else{
                        $macho = DBRead('animais', "WHERE id = '$id_macho'");
                      }
                    ?>
                      <option value="<?=$lote_['id']?>"><?=$lote_['codigo']?> - <?=$macho[0]['nome']?></option>
                    <?}?>
                  </select>
              </div>
              <? } ?>


              <? if($reproducao == 2){ ?>
                <div class="form-group">
                    <label for="exampleInputPassword1">Lote</label>
                    <select class="form-control select" onchange="linkar_lote(this.value, 2)" name="profissional" id="profissional">
                      <option value="">Selecionar</option>
                      <option value=""></option>
                      <?
                      $lote = DBRead('transplante', "ORDER BY id desc");
                      foreach ($lote as $lote_) {
                        $id_macho = $lote_['id_pai'];
                        if($lote_['terceiro_pai']){
                          $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
                        }else{
                          $macho = DBRead('animais', "WHERE id = '$id_macho'");
                        }

                        $id_femea = $lote_['id_mae'];
                        if($lote_['terceiro_mae']){
                          $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
                        }else{
                          $femea = DBRead('animais', "WHERE id = '$id_femea'");
                        }
                      ?>
                        <option value="<?=$lote_['id']?>"><?=$lote_['codigo']?> - <?=$macho[0]['nome']?> - <?=$femea[0]['nome']?></option>
                      <?}?>
                    </select>
                </div>
              <? } ?>

              <? } ?>
            </div>
          </div>
          </div>
          <!-- /.box-body -->


<? if($tipo){ ?>
      <div class="col-md-9">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">

            <?
            $reproducao = $_GET['reproducao'];

            if($reproducao == 0){
            $id_lote = $_GET['id_lote'];
            $monta = DBRead('monta', "WHERE id = '$id_lote'");
            $id_macho = $monta[0]['id_animal'];
            if(!$monta[0]['terceiro']){
              $macho = DBRead('animais', "WHERE id = '$id_macho'");
            }else{
              $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
            }
            $data = $monta[0]['data_inicio'];
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
            $data_inicial = $data;

            $data = $monta[0]['data_fim'];
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
            $data_final = $data;
            ?>

            <h4>Lote: <?=$monta[0]['codigo']," - Macho:",  $macho[0]['nome']?></h4>
            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Nº</th>
                <th>Fêmea</th>
                <th style="width:20%;">Ultrassom</th>
              </tr>
              <?
              $monta_controle = DBRead('monta_controle', "WHERE id_monta = '$id_lote'");
              foreach ($monta_controle as $monta_controle_){
                $x++;
                $id_animal = $monta_controle_['id_animal'];
                $id_monta_controle = $monta_controle_['id'];
                if(!$monta_controle_['terceiro']){
                  $femea = DBRead('animais', "WHERE id = '$id_animal'");
                }else{
                  $femea = DBRead('terceiros', "WHERE id = '$id_animal'");
                }
              ?>
              <tr>
                <td><?=$x?></td>
                <td onclick="abrir_animal(<?=$femea[0]['id']?>)" style="cursor:pointer;" ><?=$femea[0]['nome']?></td>
                <td>

                <? if($monta_controle_['ultrassom'] == 1){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_monta_controle?>, <?=$id_lote?>, <?=$reproducao?>)" style="color:green;">
                    <option value="1">Positivo</option>
                <? } ?>
                <? if($monta_controle_['ultrassom'] == 2){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_monta_controle?>, <?=$id_lote?>, <?=$reproducao?>)" style="color:red;">
                    <option value="1">Negativo</option>
                <? } ?>
                <? if($monta_controle_['ultrassom'] == 0){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_monta_controle?>, <?=$id_lote?>, <?=$reproducao?>)">
                    <option value="1">Selecionar</option>
                <? } ?>
               <option value=""></option>
               <option value="1">Positivo</option>
               <option value="2">Negativo</option>
             </select></td>
              </tr>
              <? } ?>
              </table>
            <? } ?>


            <?
            if($reproducao == 1){
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
            <h4>Lote: <?=$inseminacao[0]['codigo']," - Macho:",  $macho[0]['nome']?></h4>
            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Nº</th>
                <th>Fêmea</th>
                <th style="width:20%;">Ultrassom</th>
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
                <td>
                <? if($inseminacao_controle_['ultrassom'] == 1){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_inseminacao_controle?>, <?=$id_lote?>, <?=$reproducao?>)" style="color:green;">
                    <option value="1">Positivo</option>
                <? } ?>
                <? if($inseminacao_controle_['ultrassom'] == 2){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_inseminacao_controle?>, <?=$id_lote?>, <?=$reproducao?>)" style="color:red;">
                    <option value="1">Negativo</option>
                <? } ?>
                <? if($inseminacao_controle_['ultrassom'] == 0){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_inseminacao_controle?>, <?=$id_lote?>, <?=$reproducao?>)">
                    <option value="1">Selecionar</option>
                <? } ?>
               <option value=""></option>
               <option value="1">Positivo</option>
               <option value="2">Negativo</option>
             </select></td>
                </tr>
              <? } ?>
              </table>
            <? } ?>


            <?
            if($reproducao == 2){
              $id_lote = $_GET['id_lote'];
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
            ?>
            <h4>Lote: <?=$te[0]['codigo']," - Macho:",  $macho[0]['nome']," - Fêmea:",  $femea[0]['nome']?></h4>
            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th style="width:5%;">Nº</th>
                <th>Receptora</th>
                <th style="width:20%;">Ultrassom</th>
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
                <td>

                <? if($transplante_controle_['ultrassom'] == 1){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_transplante_controle?>, <?=$id_lote?>, <?=$reproducao?>)" style="color:green;">
                    <option value="1">Positivo</option>
                <? } ?>
                <? if($transplante_controle_['ultrassom'] == 2){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_transplante_controle?>, <?=$id_lote?>, <?=$reproducao?>)" style="color:red;">
                    <option value="1">Negativo</option>
                <? } ?>
                <? if($transplante_controle_['ultrassom'] == 0){ ?>
                  <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom(this.value, <?=$id_transplante_controle?>, <?=$id_lote?>, <?=$reproducao?>)">
                    <option value="1">Selecionar</option>
                <? } ?>
               <option value=""></option>
               <option value="1">Positivo</option>
               <option value="2">Negativo</option>
              </select></td>
                </tr>
              <? } ?>
              </table>
            <? } ?>
          </div>
        </div>
    </div>
  <? } ?>


  <? if(!$tipo){ $x++;?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <?
          if($animal[0]['sexo'] == 'Fêmea'){ ?>
          <table class="table table-bordered" id="tabela_padrao" width="98%">
            <tr>
              <th>Nº</th>
              <th>Tipo</th>
              <th>Lote</th>
              <th>Data</th>
              <th>Receptora</th>
              <th>Ultrassom</th>
            </tr>

            <?
            $monta = DBRead('monta_controle', "WHERE id_animal = '$id_animal' AND terceiro = '$terceiro'");
            foreach ($monta as $monta_) {
              $id_monta = $monta_['id_monta'];
              $id_monta_controle = $monta_['id'];
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
              $data_inicial = $data;

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
              $data_final = $data;
            ?>
              <tr>
              <td><?=$x++?></td>
              <td>Monta natural</td>
              <td><?=$lote[0]['codigo']?></td>
              <td><?=$data_inicial?> - <?=$data_final?></td>
              <td>--</td>
              <td>
              <? if($monta_['ultrassom'] == 1){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_monta_controle?>, <?=$id_monta?>, 0, <?=$terceiro?>)" style="color:green;">
              <option value="1">Positivo</option>
              <? } ?>
              <? if($monta_['ultrassom'] == 2){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_monta_controle?>, <?=$id_monta?>, 0, <?=$terceiro?>)" style="color:red;">
              <option value="1">Negativo</option>
              <? } ?>
              <? if($monta_['ultrassom'] == 0){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_monta_controle?>, <?=$id_monta?>, 0, <?=$terceiro?>)">
              <option value="1">Selecionar</option>
              <? } ?>
              <option value=""></option>
              <option value="1">Positivo</option>
               <option value="2">Negativo</option>
              </select></td>
            </tr>
          <? }


          //INSEMINAÇÃO
          $inseminacao = DBRead('inseminacao_controle', "WHERE id_femea = '$id_animal' AND terceiro = '$terceiro'");
          foreach ($inseminacao as $inseminacao_) {
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
            $data_inicial = $data;

          ?>
          <tr>
            <td><?=$x++?></td>
            <td>Inseminação artificial</td>
            <td><?=$lote[0]['codigo']?></td>
            <td><?=$data_inicial?></td>
            <td>--</td>
            <td>
            <? if($inseminacao_['ultrassom'] == 1){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_inseminacao_controle?>, <?=$id_inseminacao?>, 1, <?=$terceiro?>)" style="color:green;">
                <option value="1">Positivo</option>
            <? } ?>
            <? if($inseminacao_['ultrassom'] == 2){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_inseminacao_controle?>, <?=$id_inseminacao?>, 1, <?=$terceiro?>)" style="color:red;">
                <option value="1">Negativo</option>
            <? } ?>
            <? if($inseminacao_['ultrassom'] == 0){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_inseminacao_controle?>, <?=$id_inseminacao?>, 1, <?=$terceiro?>)">
                <option value="1">Selecionar</option>
            <? } ?>
           <option value=""></option>
           <option value="1">Positivo</option>
           <option value="2">Negativo</option>
         </select></td>
          </tr>
          <? }

          //TE
          $te = DBRead('transplante', "WHERE id_mae = '$id_animal' AND terceiro_mae = '$terceiro'");
          foreach ($te as $te_) {
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


            $te_controle = DBRead('transplante_controle', "WHERE id_lote = '$id_te'");
            foreach ($te_controle as $te_controle_) {
              $id_inseminacao_controle = $te_controle_['id'];
          ?>

          <tr>
            <td><?=$x++?></td>
            <td>Trans. de embriões</td>
            <td><?=$te_['codigo']?></td>
            <td><?=$data_te?></td>
            <td><?=$te_controle_['receptora']?></td>
            <td>
            <? if($te_controle_['ultrassom'] == 1){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_inseminacao_controle?>, <?=$id_te?>, 2, <?=$terceiro?>)" style="color:green;">
                <option value="1">Positivo</option>
            <? } ?>
            <? if($te_controle_['ultrassom'] == 2){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_inseminacao_controle?>, <?=$id_te?>, 2, <?=$terceiro?>)" style="color:red;">
                <option value="1">Negativo</option>
            <? } ?>
            <? if($te_controle_['ultrassom'] == 0){ ?>
              <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_ultrassom2(this.value, <?=$id_inseminacao_controle?>, <?=$id_te?>, 2, <?=$terceiro?>)">
                <option value="1">Selecionar</option>
            <? } ?>
           <option value=""></option>
           <option value="1">Positivo</option>
           <option value="2">Negativo</option>
         </select></td>
          </tr>
        <? } }?>
          </table>
        </div>
      </div>
    </div>
  <? } } ?>
  </div>
</section>
  <!-- /.content -->
