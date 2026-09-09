<script type="text/javascript">
function te_lote(id_lote){
  window.location.href = "geral.php?pg=te&id_lote="+id_lote;
}

function pesquisar_pai_te(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/te/lista_pai.php?nome="+nome;
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

function linkar_pai_te(nome){
  window.location.href = "geral.php?pg=lista_te&pai="+nome;
}

function pesquisar_mae_te(nome){
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

function excluir_lote_te(id_lote){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "reproducao/te/palco_excluir_lote.php?id_lote="+id_lote;
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
    Pesquisar Transplante de embriões
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Trans. de embriões</a></li>
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
                <select class="form-control select" onchange="te_lote(this.value)" name="profissional" id="profissional">
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


            <div class="form-group">
                <label for="exampleInputPassword1">Mãe</label>
                <input type="text" class="form-control" id="mae" name="mae" value="<?=$mae?>" onKeyUp="pesquisar_mae_te(this.value)">
                <div id="lista_mae" style="border-style:solid; border-width:thin; height:auto; border-color: #bab1b4; position:absolute; z-index:99999; background:#fff; width:150%; display:none; margin-top:1%;">
            </div>
          </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Pai</label>
                <input type="text" class="form-control" id="pai" name="pai" value="<?=$pai?>" onKeyUp="pesquisar_pai_te(this.value)">
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
              <a href="geral.php?pg=cadastrar_te" > <button type="submit" class="btn btn-success" style="float:right; margin-bottom:1%;">Adicionar novo lote</button> </a>
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
                <th>Fêmea</th>
                <th>Data</th>
                <th>Previsão</th>
                <th>Excluir</th>
              </tr>
              <?
                $lote = DBRead('transplante', "ORDER BY data desc LIMIT 15");
                foreach ($lote as $lote_){
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
                $data_te = $data;


                $data = explode("/", $data);
                list($dia, $mes, $ano) = $data;
                $data = "$ano$mes$dia";
                $nextdate = addDayIntoDate($data,146);
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

                $data = explode("/", $data);
                list($dia, $mes, $ano) = $data;
                $data = "$ano$mes$dia";
                $nextdate = addDayIntoDate($data,161);
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
                    <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$lote_['codigo']?></td>
                    <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$macho[0]['nome']?></td>
                    <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$femea[0]['nome']?></td>
                    <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" >Inicial: <?=$data_te?></td>
                    <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$data_previsao1?> até <?=$data_previsao2?></td>
                    <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_lote_te(<?=$lote_['id']?>)">X</button></td>
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
                  <th>Fêmea</th>
                  <th>Data</th>
                  <th>Previsão</th>
                  <th>Excluir</th>
                </tr>
                <?
                  $pai_ = DBRead('animais', "WHERE nome = '$pai'");
                  if($pai_[0]['id'] > 0){
                    $id_macho = $pai_[0]['id'];
                    $lote = DBRead('transplante', "WHERE id_pai = '$id_macho' AND terceiro_pai = '0' ORDER BY data desc");
                  }else{
                    $pai_ = DBRead('terceiros', "WHERE nome = '$pai'");
                    $id_macho = $pai_[0]['id'];
                    $lote = DBRead('transplante', "WHERE id_pai = '$id_macho' AND terceiro_pai = '1' ORDER BY data desc");
                  }



                  foreach ($lote as $lote_){
                  $id_femea = $lote_['id_mae'];
                  if($lote_['terceiro_mae']){
                    $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
                  }else{
                    $femea = DBRead('animais', "WHERE id = '$id_femea'");
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
                  $data_te = $data;


                  $data = explode("/", $data);
                  list($dia, $mes, $ano) = $data;
                  $data = "$ano$mes$dia";
                  $nextdate = addDayIntoDate($data,146);
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

                  $data = explode("/", $data);
                  list($dia, $mes, $ano) = $data;
                  $data = "$ano$mes$dia";
                  $nextdate = addDayIntoDate($data,161);
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
                      <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$lote_['codigo']?></td>
                      <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$pai_[0]['nome']?></td>
                      <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$femea[0]['nome']?></td>
                      <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" >Inicial: <?=$data_te?></td>
                      <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$data_previsao1?> até <?=$data_previsao2?></td>
                      <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_lote_te(<?=$lote_['id']?>)">X</button></td>
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
                    <th>Fêmea</th>
                    <th>Data</th>
                    <th>Previsão</th>
                    <th>Excluir</th>
                  </tr>
                  <?
                    $mae_ = DBRead('animais', "WHERE nome = '$mae'");
                    if($mae_[0]['id'] > 0){
                      $id_mae = $mae_[0]['id'];
                      $lote = DBRead('transplante', "WHERE id_mae = '$id_mae' AND terceiro_mae = '0' ORDER BY id desc");
                    }else{
                      $mae_ = DBRead('terceiros', "WHERE nome = '$mae'");
                      $id_mae = $mae_[0]['id'];
                      $lote = DBRead('transplante', "WHERE id_mae = '$id_mae' AND terceiro_mae = '1' ORDER BY id desc");
                    }
                    foreach ($lote as $lote_){
                    $id_macho = $lote_['id_pai'];
                    if($lote_['terceiro_pai']){
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
                    $data_te = $data;

                    $data = explode("/", $data_te);
                    list($dia, $mes, $ano) = $data;
                    $data = "$ano$mes$dia";
                    $nextdate = addDayIntoDate($data,146);
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
                    $nextdate = addDayIntoDate($data,161);
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
                        <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$lote_['codigo']?></td>
                        <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$macho[0]['nome']?></td>
                        <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$mae_[0]['nome']?></td>
                        <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" >Inicial: <?=$data_te?></td>
                        <td onclick="te_lote(<?=$lote_['id']?>)" style="cursor:pointer;" ><?=$data_previsao1?> até <?=$data_previsao2?></td>
                        <td><button type="button" class="btn btn-danger" style="padding:0%; padding-left:5%; padding-right:5%; height:20px;" onclick="excluir_lote_te(<?=$lote_['id']?>)">X</button></td>
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
