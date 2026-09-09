<script type="text/javascript">
function inseminacao_lote(id_lote){
  window.location.href = "geral.php?pg=inseminacao&id_lote="+id_lote;
}

function pesquisar_pai_inseminacao(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/inseminacao/lista_pai.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_pai").innerHTML = resposta;
document.getElementById("lista_pai").style.display = 'block';
}
}
PP.send(null);
}

function fechar_lista_pai(){
  document.getElementById("lista_pai").style.display = 'none';
}

function linkar_pai_monta(nome){
  window.location.href = "geral.php?pg=lista_inseminacao&pai="+nome;
}

function pesquisar_mae_inseminacao(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/inseminacao/lista_mae.php?nome="+nome;
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

function linkar_mae_monta(nome){
  window.location.href = "geral.php?pg=lista_inseminacao&mae="+nome;
}

function excluir_lote_inseminacao(id_lote){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/inseminacao/palco_excluir_lote.php?id_lote="+id_lote;
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
function addDayIntoDate($date,$days) {
     $thisyear = substr ( $date, 0, 4 );
     $thismonth = substr ( $date, 4, 2 );
     $thisday =  substr ( $date, 6, 2 );
     $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday + $days, $thisyear );
     return strftime("%Y%m%d", $nextdate);
}

$pai = $_GET['pai'];
$mae = $_GET['mae'];
?>


<section class="content-header">
  <h1>
    Pesquisar Inseminação artificial
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Inseminação artificial</a></li>
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
                <label for="exampleInputPassword1">Lote</label>
                <select class="form-control select" onchange="inseminacao_lote(this.value)" name="profissional" id="profissional">
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


            <div class="form-group">
                <label for="exampleInputPassword1">Mãe</label>
                <input type="text" class="form-control" id="mae" name="mae" value="<?=$mae?>" onKeyUp="pesquisar_mae_inseminacao(this.value)">
                <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
            </div>
          </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Pai</label>
                <input type="text" class="form-control" id="pai" name="pai" value="<?=$pai?>" onKeyUp="pesquisar_pai_inseminacao(this.value)">
                <div id="lista_pai" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
            </div>
          </div>
        </div>
          <!-- /.box-body -->
			</div>
      <!-- /.col -->
    </div>


      <div class="col-md-9">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
              <a href="geral.php?pg=cadastrar_inseminacao" > <button type="submit" class="btn btn-success" style="float:right; margin-bottom:1%;">Adicionar novo lote</button> </a>
            </div>
            <?

            //SEM BUSCA
            if((!$pai) && (!$mae)){
            ?>
            Últimos lotes cadastrados
            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Lote</th>
                <th>Macho</th>
                <th>Data</th>
                <th>Previsão</th>
                <th>Excluir</th>
              </tr>
              <?
                $lote = DBRead('inseminacao', "ORDER BY id desc LIMIT 15");
                foreach ($lote as $lote_){
                $id_macho = $lote_['id_macho'];
                if($lote_['terceiro']){
                  $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
                }else{
                  $macho = DBRead('animais', "WHERE id = '$id_macho'");
                }

                $data = $lote_['data'];
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
                ?>
                <tr>
                    <td onclick="inseminacao_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$lote_['codigo']?></td>
                    <td onclick="inseminacao_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$macho[0]['nome']?></td>
                    <td onclick="inseminacao_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$data_inseminacao?></td>
                    <td onclick="inseminacao_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$data_previsao1?> até <?=$data_previsao2?></td>
                    <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_lote_inseminacao(<?=$lote_['id']?>)">X</button></td>
                  </tr>
                <? } ?>
                </table>
              <? }


              //BUSCA PAI
              if($pai){
              ?>
              <table class="table table-bordered" id="tabela_padrao">
                <tr>
                  <th>Lote</th>
                  <th>Macho</th>
                  <th>Data</th>
                  <th>Previsão</th>
                  <th>Excluir</th>
                </tr>
                <?
                  $pai_ = DBRead('animais', "WHERE nome = '$pai'");
                  if($pai_[0]['id'] > 0){
                    $id_macho = $pai_[0]['id'];
                    $lote = DBRead('inseminacao', "WHERE id_macho = '$id_macho' AND terceiro = '0' ORDER BY codigo desc");
                  }else{
                    $pai_ = DBRead('terceiros', "WHERE nome = '$pai'");
                    $id_macho = $pai_[0]['id'];
                    $lote = DBRead('inseminacao', "WHERE id_macho = '$id_macho' AND terceiro = '1' ORDER BY codigo desc");
                  }

                  foreach ($lote as $lote_){
                  $data = $lote_['data'];
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
                  ?>
                  <tr>
                      <td onclick="inseminacao_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$lote_['codigo']?></td>
                      <td onclick="inseminacao_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$pai_[0]['nome']?></td>
                      <td onclick="inseminacao_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$data_inseminacao?></td>
                      <td onclick="inseminacao_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$data_previsao1?> até <?=$data_previsao2?></td>
                      <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_lote_inseminacao(<?=$lote_['id']?>)">X</button></td>
                    </tr>
                  <? } ?>
                  </table>
                <? }


                //BUSCA MAE
                if($mae){
                ?>
                <table class="table table-bordered" id="tabela_padrao">
                  <tr>
                    <th>Lote</th>
                    <th>Macho</th>
                    <th>Data</th>
                    <th>Previsão</th>
                    <th>Excluir</th>
                  </tr>
                  <?
                    $mae_ = DBRead('animais', "WHERE nome = '$mae'");
                    if($mae_[0]['id'] > 0){
                      $id_mae = $mae_[0]['id'];
                      $lote_controle = DBRead('inseminacao_controle', "WHERE id_femea = '$id_mae' AND terceiro = '0' ORDER BY id desc");
                    }else{
                      $mae_ = DBRead('terceiros', "WHERE nome = '$mae'");
                      $id_mae = $mae_[0]['id'];
                      $lote_controle = DBRead('inseminacao_controle', "WHERE id_femea = '$id_mae' AND terceiro = '1' ORDER BY id desc");
                    }
                    foreach ($lote_controle as $lote_controle_){
                    $id_lote = $lote_controle_['id_lote'];
                    $lote = DBRead('inseminacao', "WHERE id = '$id_lote'");
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

                    $data = explode("/", $data_inseminacao );
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
                    ?>
                    <tr>
                        <td onclick="inseminacao_lote(<?=$lote[0]['id']?>)" style="cursor:pointer;" ><?=$lote[0]['codigo']?></td>
                        <td onclick="inseminacao_lote(<?=$lote[0]['id']?>)" style="cursor:pointer;" ><?=$macho[0]['nome']?></td>
                        <td onclick="inseminacao_lote(<?=$lote[0]['id']?>)" style="cursor:pointer;" ><?=$data_inseminacao?></td>
                        <td onclick="inseminacao_lote(<?=$lote[0]['id']?>)" style="cursor:pointer;" ><?=$data_previsao1?> até <?=$data_previsao2?></td>
                        <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_lote_inseminacao(<?=$lote[0]['id']?>)">X</button></td>
                      </tr>
                    <? } ?>
                    </table>
                  <? } ?>
          </div>
          <!-- /.box-body -->
        </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
</section>
  <!-- /.content -->
