<?
function addDayIntoDate($date,$days) {
     $thisyear = substr ( $date, 0, 4 );
     $thismonth = substr ( $date, 4, 2 );
     $thisday =  substr ( $date, 6, 2 );
     $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday + $days, $thisyear );
     return strftime("%Y%m%d", $nextdate);
}
?>

<script type="text/javascript">
function atualizar(filtro){
    window.location.href = "geral.php?pg=lista_rebanho&filtro="+filtro;
}

function atualizar2(sexo){
    window.location.href = "geral.php?pg=lista_rebanho&filtro=Sexo&sexo="+sexo;
}
</script>

<?
$filtro = $_GET['filtro'];
if(!$filtro){ $filtro = 'Todos';}
?>


<section class="content-header">
  <h1>
    Rebanho
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-github-alt"></i> Animais</a></li>
    <li><a href="#">Rebanho</a></li>
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
                <label for="exampleInputPassword1">Filtro<span style="color:#F00;">*</span></label>
                <select class="form-control select" id="filtro" name="filtro" onchange="atualizar(this.value)">
                  <option value="<?=$filtro?>"><?=$filtro?></option>
                  <option></option>
                  <option value="Todos">Todos</option>
                  <option value="Rebanho">Rebanho</option>
                  <option value="Sexo">Sexo</option>
                  <option value="Reprodutores">Reprodutores</option>
                  <option value="Matrizes">Matrizes</option>
                </select>
            </div>



<? if($filtro == 'Sexo'){ $sexo = $_GET['sexo']; ?>
  <div class="form-group">
      <label for="exampleInputPassword1">Sexo<span style="color:#F00;">*</span></label>
      <select class="form-control select" id="filtro" name="filtro" onchange="atualizar2(this.value)">
        <? if($sexo == ''){?> <option value="">Selecionar</option> <? }else{ ?>
        <option value="<?=$sexo?>"><?=$sexo?></option> <? } ?>
        <option></option>
        <option value="Macho">Macho</option>
        <option value="Fêmea">Fêmea</option>
      </select>
      </div>
<? } ?>

<? if(($filtro != 'Reprodutores') && ($filtro != 'Matrizes')){ ?>
  <div class="form-group">
    <a href="animal/_imprimir_rebanho.php?filtro=<?=$filtro?>&sexo=<?=$sexo?>" target="_new"> <button type="submit" class="btn btn-primary" style="margin-bottom:1%; width:100%;">Imprimir relatório</button> </a>
  </div>
<? } ?>

      </div>
    </div>
      <!-- /.col -->
</div>

<?
//TODOS ANIMAIS
if($filtro == 'Todos'){ ?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Sexo</th>
              <th>Nascimento</th>
              <th>Idade</th>
              <th>Entrada</th>
              <th>tipo</th>
            </tr>
            <?
            $loop = $_GET['pag'];
            $loop = $loop*40;
            $fim = $loop+40;
            $x = $loop;
            $animais = DBRead('animais', "ORDER BY id desc LIMIT $loop,40");
            foreach ($animais as $animais_){
              $x++;
              $data_atual = $animais_['data_de_nascimento'];
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

              list($dia, $mes, $ano) = explode('/', $data);
              // Descobre que dia é hoje e retorna a unix timestamp
              $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
              // Descobre a unix timestamp da data de nascimento do fulano
              $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
              // Depois apenas fazemos o cálculo já citado :)
              $anos = floor((((($hoje - $nascimento) / 60) / 60) / 24));
              $idade_anos  = floor($anos /365);
              $idade_meses = (($anos /365) - $idade_anos) * 12;
              $idade_meses = (int)$idade_meses;
              $idade_meses = round($idade_meses);
              ?>

              <? if($animais_['status'] > 0){ ?><tr style="color:red;"> <? }else{?><tr> <? } ?>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animais_['id']?>)" style="cursor:pointer;" ><?=$animais_['nome']?></td>
              <td><?=$animais_['sexo']?></td>
              <td><?=$data?></td>
              <td><?=$idade_anos?> Anos <?=$idade_meses?> Meses</td>
              <td>
              <?
              if($animais_['entrada'] == 0){echo "Nascimento";}
              if($animais_['entrada'] == 1){echo "Compra";}
              ?>
              </td>
              <td><?=$animais_['tipo']?></td>
              </tr>
            <? } ?>
            </table>

            <div class="box-footer clearfix">
            <ul class="pagination pagination-sm no-margin pull-right">
              <? $pag = $_GET['pag']+1; ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag-2?>">&laquo;</a></li>

              <?
              $animal = DBRead('animais');
              $qtd = count($animal);
              $qtd_pag = $qtd/40;
              $x = 0;
              while($x < $qtd_pag){?>
                <? if($x+1 == $pag){ ?><li><a href="#"><span style="color:red;"><?=$x+1?></span></a></li>
              <? }else{ ?><li><a href="geral.php?pg=lista_rebanho&pag=<?=$x?>"><?=$x+1?></a></li><? } ?>
              <? $x++;} ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag?>">&raquo;</a></li>
            </ul>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>
<? } //FIM TODOS ANIMAIS ?>


<?
//REBANHO
if($filtro == 'Rebanho'){ ?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Sexo</th>
              <th>Nascimento</th>
              <th>Idade</th>
              <th>Entrada</th>
              <th>tipo</th>
            </tr>
            <?
            $loop = $_GET['pag'];
            $loop = $loop*40;
            $fim = $loop+40;
            $x = $loop;
            $animais = DBRead('animais', "WHERE status = '0' ORDER BY id desc LIMIT $loop,40");
            foreach ($animais as $animais_){
              $x++;
              $data_atual = $animais_['data_de_nascimento'];
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

              list($dia, $mes, $ano) = explode('/', $data);
              // Descobre que dia é hoje e retorna a unix timestamp
              $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
              // Descobre a unix timestamp da data de nascimento do fulano
              $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
              // Depois apenas fazemos o cálculo já citado :)
              $anos = floor((((($hoje - $nascimento) / 60) / 60) / 24));
              $idade_anos  = floor($anos /365);
              $idade_meses = (($anos /365) - $idade_anos) * 12;
              $idade_meses = (int)$idade_meses;
              $idade_meses = round($idade_meses);
              ?>

              <tr>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animais_['id']?>)" style="cursor:pointer;" ><?=$animais_['nome']?></td>
              <td><?=$animais_['sexo']?></td>
              <td><?=$data?></td>
              <td><?=$idade_anos?> Anos <?=$idade_meses?> Meses</td>
              <td>
              <?
              if($animais_['entrada'] == 0){echo "Nascimento";}
              if($animais_['entrada'] == 1){echo "Compra";}
              ?>
              </td>
              <td><?=$animais_['tipo']?></td>
              </tr>
            <? } ?>
            </table>

            <div class="box-footer clearfix">
            <ul class="pagination pagination-sm no-margin pull-right">
              <? $pag = $_GET['pag']+1; ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag-2?>&filtro=Rebanho">&laquo;</a></li>

              <?
              $animal = DBRead('animais', "WHERE status = 0");
              $qtd = count($animal);
              $qtd_pag = $qtd/40;
              $x = 0;
              while($x < $qtd_pag){?>
                <? if($x+1 == $pag){ ?><li><a href="#"><span style="color:red;"><?=$x+1?></span></a></li>
              <? }else{ ?><li><a href="geral.php?pg=lista_rebanho&pag=<?=$x?>&filtro=Rebanho"><?=$x+1?></a></li><? } ?>
              <? $x++;} ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag?>&filtro=Rebanho">&raquo;</a></li>
            </ul>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>
<? } //FIM REBANHO ?>


<?
//TODOS SEXO
if($filtro == 'Sexo'){ ?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Sexo</th>
              <th>Nascimento</th>
              <th>Idade</th>
              <th>Entrada</th>
              <th>tipo</th>
            </tr>
            <?
            $loop = $_GET['pag'];
            $loop = $loop*40;
            $fim = $loop+40;
            $x = $loop;
            $animais = DBRead('animais', "WHERE status = '0' AND sexo = '$sexo' ORDER BY id desc LIMIT $loop,40");
            foreach ($animais as $animais_){
              $x++;
              $data_atual = $animais_['data_de_nascimento'];
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

              list($dia, $mes, $ano) = explode('/', $data);
              // Descobre que dia é hoje e retorna a unix timestamp
              $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
              // Descobre a unix timestamp da data de nascimento do fulano
              $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
              // Depois apenas fazemos o cálculo já citado :)
              $anos = floor((((($hoje - $nascimento) / 60) / 60) / 24));
              $idade_anos  = floor($anos /365);
              $idade_meses = (($anos /365) - $idade_anos) * 12;
              $idade_meses = (int)$idade_meses;
              $idade_meses = round($idade_meses);

            ?>
            <? if($animais_['status'] > 0){ ?><tr style="color:red;"> <? }else{?><tr> <? } ?>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animais_['id']?>)" style="cursor:pointer;" ><?=$animais_['nome']?></td>
              <td><?=$animais_['sexo']?></td>
              <td><?=$data?></td>
              <td><?=$idade_anos?> Anos <?=$idade_meses?> Meses</td>
              <td>
              <?
              if($animais_['entrada'] == 0){echo "Nascimento";}
              if($animais_['entrada'] == 1){echo "Compra";}
              ?>
              </td>
              <td><?=$animais_['tipo']?></td>
              </tr>
            <? } ?>
            </table>

            <div class="box-footer clearfix">
            <ul class="pagination pagination-sm no-margin pull-right">
              <? $pag = $_GET['pag']+1; ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag-2?>&filtro=Sexo&sexo=<?=$sexo?>">&laquo;</a></li>

              <?
              $animal = DBRead('animais', "WHERE status = '0' AND sexo = '$sexo'");
              $qtd = count($animal);
              $qtd_pag = $qtd/40;
              $x = 0;
              while($x < $qtd_pag){?>
                <? if($x+1 == $pag){ ?><li><a href="#"><span style="color:red;"><?=$x+1?></span></a></li>
              <? }else{ ?><li><a href="geral.php?pg=lista_rebanho&pag=<?=$x?>&filtro=Sexo&sexo=<?=$sexo?>"><?=$x+1?></a></li><? } ?>
              <? $x++;} ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag?>&filtro=Sexo&sexo=<?=$sexo?>">&raquo;</a></li>
            </ul>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>
<? } //FIM SEXO ?>


<?
//REPRODUTORES
if($filtro == 'Reprodutores'){?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Nascimento</th>
              <th>Idade</th>
              <th>Entrada</th>
              <th>tipo</th>
              <th>Crias</th>
            </tr>
            <?
            $loop = $_GET['pag'];
            $loop = $loop*40;
            $fim = $loop+40;
            $x = $loop;
            $reprodutor = DBRead('reprodutor', "ORDER BY id_macho desc LIMIT $loop,40");
            foreach ($reprodutor as $reprodutor_){
            $id_animal = $reprodutor_['id_macho'];
            $animal = DBRead('animais',"WHERE id = '$id_animal'");
              $x++;
              $data_atual = $animal[0]['data_de_nascimento'];
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

              list($dia, $mes, $ano) = explode('/', $data);
              // Descobre que dia é hoje e retorna a unix timestamp
              $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
              // Descobre a unix timestamp da data de nascimento do fulano
              $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
              // Depois apenas fazemos o cálculo já citado :)
              $anos = floor((((($hoje - $nascimento) / 60) / 60) / 24));
              $idade_anos  = floor($anos /365);
              $idade_meses = (($anos /365) - $idade_anos) * 12;
              $idade_meses = (int)$idade_meses;
              $idade_meses = round($idade_meses);

            ?>
              <? if($animal[0]['status'] > 0){ ?><tr style="color:red;"> <? }else{?><tr> <? } ?>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animal[0]['id']?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
              <td><?=$data?></td>
              <td><?=$idade_anos?> Anos <?=$idade_meses?> Meses</td>
              <td>
              <?
              if($animal[0]['entrada'] == 0){echo "Nascimento";}
              if($animal[0]['entrada'] == 1){echo "Compra";}
              ?>
              </td>
              <td><?=$animal[0]['tipo']?></td>
              <td><?=$reprodutor_['qtd_crias']?></td>
              </tr>
            <? } ?>
            </table>

            <div class="box-footer clearfix">
            <ul class="pagination pagination-sm no-margin pull-right">
              <? $pag = $_GET['pag']+1; ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag-2?>&filtro=Reprodutores">&laquo;</a></li>

              <?
              $reprodutor = DBRead('reprodutor');
              $qtd = count($reprodutor);
              $qtd_pag = $qtd/40;
              $x = 0;
              while($x < $qtd_pag){?>
                <? if($x+1 == $pag){ ?><li><a href="#"><span style="color:red;"><?=$x+1?></span></a></li>
              <? }else{ ?><li><a href="geral.php?pg=lista_rebanho&pag=<?=$x?>&filtro=Reprodutores"><?=$x+1?></a></li><? } ?>
              <? $x++;} ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag?>&filtro=Reprodutores">&raquo;</a></li>
            </ul>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>
<? } //FIM REPRODUTORES ?>

<?
//MATRIZES
if($filtro == 'Matrizes'){?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Nascimento</th>
              <th>Idade</th>
              <th>Entrada</th>
              <th>tipo</th>
              <th>Crias</th>
            </tr>
            <?
            $loop = $_GET['pag'];
            $loop = $loop*40;
            $fim = $loop+40;
            $x = $loop;
            $matriz = DBRead('matriz', "ORDER BY id_femea desc LIMIT $loop,40");
            foreach ($matriz as $matriz_){
            $id_animal = $matriz_['id_femea'];
            $animal = DBRead('animais',"WHERE id = '$id_animal'");
              $x++;
              $data_atual = $animal[0]['data_de_nascimento'];
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

              list($dia, $mes, $ano) = explode('/', $data);
              // Descobre que dia é hoje e retorna a unix timestamp
              $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
              // Descobre a unix timestamp da data de nascimento do fulano
              $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
              // Depois apenas fazemos o cálculo já citado :)
              $anos = floor((((($hoje - $nascimento) / 60) / 60) / 24));
              $idade_anos  = floor($anos /365);
              $idade_meses = (($anos /365) - $idade_anos) * 12;
              $idade_meses = (int)$idade_meses;
              $idade_meses = round($idade_meses);

            ?>
            <? if($animal[0]['status'] > 0){ ?><tr style="color:red;"> <? }else{?><tr> <? } ?>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animal[0]['id']?>)" style="cursor:pointer;" ><?=$animal[0]['nome']?></td>
              <td><?=$data?></td>
              <td><?=$idade_anos?> Anos <?=$idade_meses?> Meses</td>
              <td>
              <?
              if($animal[0]['entrada'] == 0){echo "Nascimento";}
              if($animal[0]['entrada'] == 1){echo "Compra";}
              ?>
              </td>
              <td><?=$animal[0]['tipo']?></td>
              <td><?=$matriz_['qtd_crias']?></td>
              </tr>
            <? } ?>
            </table>

            <div class="box-footer clearfix">
            <ul class="pagination pagination-sm no-margin pull-right">
              <? $pag = $_GET['pag']+1; ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag-2?>&filtro=Matrizes">&laquo;</a></li>

              <?
              $reprodutor = DBRead('matriz');
              $qtd = count($reprodutor);
              $qtd_pag = $qtd/40;
              $x = 0;
              while($x < $qtd_pag){?>
                <? if($x+1 == $pag){ ?><li><a href="#"><span style="color:red;"><?=$x+1?></span></a></li>
              <? }else{ ?><li><a href="geral.php?pg=lista_rebanho&pag=<?=$x?>&filtro=Matrizes"><?=$x+1?></a></li><? } ?>
              <? $x++;} ?>
              <li><a href="geral.php?pg=lista_rebanho&pag=<?=$pag?>&filtro=Matrizes">&raquo;</a></li>
            </ul>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>
<? } //FIM MATRIZES ?>
  </div>
</section>
  <!-- /.content -->
