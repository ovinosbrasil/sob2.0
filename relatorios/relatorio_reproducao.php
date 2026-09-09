<? $filtro = $_GET['filtro']; ?>

<script type="text/javascript">
function atualizar(filtro){
    window.location.href = "geral.php?pg=relatorio_reproducao&filtro="+filtro;
}

function atualizar_tabela(x){
      window.location.href = "geral.php?pg=relatorio_reproducao&filtro=<?=$filtro?>&filtro2="+x;
}

function abrir_monta(id){
  window.open('geral.php?pg=monta&id_lote='+id, '_blank');
}

function abrir_ia(id){
  window.open('geral.php?pg=inseminacao&id_lote='+id, '_blank');
}

function abrir_te(id){
  window.open('geral.php?pg=te&id_lote='+id, '_blank');
}
</script>

<section class="content-header">
  <h1>
    Relatório de Reprodução
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatório</a></li>
    <li><a href="#">Reprodução</a></li>
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
                  <? if($filtro){ ?>
                  <option value="<?=$filtro?>"><?=$filtro?></option> <? }else{?> <option>Selecionar</option> <? } ?>
                  <option></option>
                  <option value="Monta natural">Monta natural</option>
                  <option value="Inseminação artificial">Inseminação artificial</option>
                  <option value="Transplante de embriões">Transplante de embriões</option>
                </select>
            </div>
      </div>
    </div>
      <!-- /.col -->
</div>



<?
//MONTA
if($filtro == 'Monta natural'){ ?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Lote - Macho</th>
              <th>Data</th>
              <th onclick="atualizar_tabela(1)" style="cursor:pointer;">Fêmeas<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(2)" style="cursor:pointer;">Ultrassom<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(3)" style="cursor:pointer;">Crias Vivas<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(4)" style="cursor:pointer;">Mortes nasc.<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
            </tr>
            <?

            if($filtro2 == 0){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '0' ORDER BY id desc"); }
            if($filtro2 == 1){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '0' ORDER BY femeas desc"); }
            if($filtro2 == 2){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '0' ORDER BY ultrassom desc"); }
            if($filtro2 == 3){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '0' ORDER BY crias desc"); }
            if($filtro2 == 4){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '0' ORDER BY mortes desc"); }
            foreach ($lote as $lote_){
              $x++;
              $id_lote = $lote_['id_lote'];
              $monta = DBRead('monta', "WHERE id = '$id_lote'");
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

              $id_macho = $monta[0]['id_animal'];
              if($monta[0]['terceiro']){
                $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
              }else{
                $macho = DBRead('animais', "WHERE id = '$id_macho'");
              }
              ?>

              <tr>
              <td><?=$x?></td>
              <td onclick="abrir_monta(<?=$lote_['id_lote']?>)" style="cursor:pointer;" ><?=$monta[0]['codigo']?> - <?=$macho[0]['nome']?></td>
              <td><?=$data_inicial?> - <?=$data_final?></td>
              <td><?=$lote_['femeas']?></td>
              <td><span style="color:green;"><?=number_format($lote_['ultrassom'],1,",",".");?>%</span> &nbsp;&nbsp; <span style="color:red;"><?=number_format(100-$lote_['ultrassom'],1,",",".");?>%</span></td>
              <td><?=$lote_['vivos'];?></td>
              <td><?=$lote_['mortes'];?></td>
              </tr>
            <? } ?>
            </table>

        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>
<? } //FIM MONTA NATURAL

if($filtro == 'Inseminação artificial'){ ?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Lote - Macho</th>
              <th>Data</th>
              <th onclick="atualizar_tabela(1)" style="cursor:pointer;">Fêmeas<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(2)" style="cursor:pointer;">Ultrassom<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(3)" style="cursor:pointer;">Crias Vivas<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(4)" style="cursor:pointer;">Mortes nasc.<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
            </tr>
            <?

            if($filtro2 == 0){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '1' ORDER BY id desc"); }
            if($filtro2 == 1){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '1' ORDER BY femeas desc"); }
            if($filtro2 == 2){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '1' ORDER BY ultrassom desc"); }
            if($filtro2 == 3){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '1' ORDER BY crias desc"); }
            foreach ($lote as $lote_){
              $x++;
              $id_lote = $lote_['id_lote'];
              $ia = DBRead('inseminacao', "WHERE id = '$id_lote'");
              $data = $ia [0]['data'];
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

              $id_macho = $ia[0]['id_macho'];
              if($ia[0]['terceiro']){
                $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
              }else{
                $macho = DBRead('animais', "WHERE id = '$id_macho'");
              }
              ?>

              <tr>
              <td><?=$x?></td>
              <td onclick="abrir_ia(<?=$lote_['id_lote']?>)" style="cursor:pointer;" ><?=$ia[0]['codigo']?> - <?=$macho[0]['nome']?></td>
              <td><?=$data?></td>
              <td><?=$lote_['femeas']?></td>
              <td><span style="color:green;"><?=number_format($lote_['ultrassom'],1,",",".");?>%</span> &nbsp;&nbsp; <span style="color:red;"><?=number_format(100-$lote_['ultrassom'],1,",",".");?>%</span></td>
              <td><?=$lote_['vivos'];?></td>
              <td><?=$lote_['mortes'];?></td>
              </tr>
            <? } ?>
            </table>

        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>
<? } //FIM IA

if($filtro == 'Transplante de embriões'){ ?>
    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Lote - Macho - Fêmea</th>
              <th>Data</th>
              <th onclick="atualizar_tabela(1)" style="cursor:pointer;">Receptoras<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(2)" style="cursor:pointer;">Ultrassom<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(3)" style="cursor:pointer;">Crias Vivas<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
              <th onclick="atualizar_tabela(4)" style="cursor:pointer;">Mortes nasc.<i style="opacity: 0.7;" class="glyphicon glyphicon-triangle-bottom"></i></th>
            </tr>
            <?
            if($filtro2 == 0){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '2' ORDER BY id desc"); }
            if($filtro2 == 1){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '2' ORDER BY femeas desc"); }
            if($filtro2 == 2){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '2' ORDER BY ultrassom desc"); }
            if($filtro2 == 3){ $lote = DBRead('lotes_reproducao', "WHERE tipo = '2' ORDER BY crias desc"); }
            foreach ($lote as $lote_){
              $x++;
              $id_lote = $lote_['id_lote'];
              $te = DBRead('transplante', "WHERE id = '$id_lote'");
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

              $id_macho = $te[0]['id_pai'];
              if($te[0]['terceiro_pai']){
                $macho = DBRead('terceiros', "WHERE id = '$id_macho'");
              }else{
                $macho = DBRead('animais', "WHERE id = '$id_macho'");
              }

              $id_femea = $te[0]['id_mae'];
              if($te[0]['terceiro_mae']){
                $femea = DBRead('terceiros', "WHERE id = '$id_femea'");
              }else{
                $femea = DBRead('animais', "WHERE id = '$id_femea'");
              }
              ?>
              <tr>
              <td><?=$x?></td>
              <td onclick="abrir_te(<?=$lote_['id_lote']?>)" style="cursor:pointer;" ><?=$te[0]['codigo']?> - <?=$macho[0]['nome']?> - <?=$femea[0]['nome']?></td>
              <td><?=$data?></td>
              <td><?=$lote_['femeas']?></td>
              <td><span style="color:green;"><?=number_format($lote_['ultrassom'],1,",",".");?>%</span> &nbsp;&nbsp; <span style="color:red;"><?=number_format(100-$lote_['ultrassom'],1,",",".");?>%</span></td>
              <td><?=$lote_['vivos'];?></td>
              <td><?=$lote_['mortes'];?></td>
              </tr>
            <? } ?>
            </table>
        </div>
        <!-- /.box-body -->
      </div>
    <!-- /.col -->
    </div>
<? } //FIM TE ?>

  </div>
</section>
  <!-- /.content -->
