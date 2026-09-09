<?
$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];

if ($data_inicial) {
  $data = $data_inicial;
  $data_atual = $data;
  $data = '0';
  $data['0'] = $data_atual['6'];
  $data['1'] = $data_atual['7'];
  $data['2'] = $data_atual['8'];
  $data['3'] = $data_atual['9'];
  $data['4'] = "-";
  $data['5'] = $data_atual['3'];
  $data['6'] = $data_atual['4'];
  $data['7'] = "-";
  $data['8'] = $data_atual['0'];
  $data['9'] = $data_atual['1'];
  $data_inicial_ = $data;
}

if ($data_final) {
  $data = $data_final;
  $data_atual = $data;
  $data = '0';
  $data['0'] = $data_atual['6'];
  $data['1'] = $data_atual['7'];
  $data['2'] = $data_atual['8'];
  $data['3'] = $data_atual['9'];
  $data['4'] = "-";
  $data['5'] = $data_atual['3'];
  $data['6'] = $data_atual['4'];
  $data['7'] = "-";
  $data['8'] = $data_atual['0'];
  $data['9'] = $data_atual['1'];
  $data_final_ = $data;
}


$nascimento = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial_' AND data_de_nascimento <= '$data_final_'");
$monta=$inseminacao=$trasnplante=$morte=0;
foreach ($nascimento as $animal){
  $total++;
  if($animal['tipo_reproducao'] == 'Monta Natural'){ $monta++;}
  if($animal['tipo_reproducao'] == 'Inseminação Artificial'){ $inseminacao++;}
  if($animal['tipo_reproducao'] == 'Embrionagem'){ $transplante++;}
  if($animal['causa_da_perda'] == 'Nascimento'){ $morte++; }
}

$monta_ = $total ? ($monta*100)/$total : 0;
$inseminacao_ = $total ? ($inseminacao*100)/$total : 0;
$transplante_ = $total ? ($transplante*100)/$total : 0;
$morte_ = $total ? ($morte*100)/$total : 0;
?>


<section class="content-header">
  <h1>
    Relatório de nascimentos
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatórios</a></li>
    <li><a href="#">Nascimentos</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="geral.php?pg=relatorio_nascimentos" onsubmit="return validar_montar()">
          <!-- /.box-header -->
          <div class="box-body">

            <div class="form-group">
                <label for="exampleInputPassword1">Data inicial<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$data_inicial?>">
                </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data final<span style="color:#F00;">*</span></label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_final" name="data_final" value="<?=$data_final?>">
                </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary" style="width:100%; margin-top:4%;">Pesquisar</button>
            </div>

        </div>
      </form>
			</div>
      <!-- /.col -->

      <div class="box box-success">
        <div class="box-body">


        <div class="form-group">
          <label>Nascimento </label> <span class="badge bg-blue"><?=$total?></span>
        </div>
        <div class="form-group">
            <label>Mortes </label>  <span class="badge bg-blue"><?=$morte?></span> <span class="badge bg-green"><?=number_format($morte_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Monta natural </label>  <span class="badge bg-blue"><?=$monta?></span> <span class="badge bg-green"><?=number_format($monta_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Inseminação artificial </label>  <span class="badge bg-blue"><?=$inseminacao?></span> <span class="badge bg-green"><?=number_format($inseminacao_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Trans. de embriões </label>  <span class="badge bg-blue"><?=$transplante?></span> <span class="badge bg-green"><?=number_format($transplante_, 2, ',', '.')?>%</span>
        </div>
      </div>
    </div>
  </div>

    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Nascimento</th>
              <th>Tipo de reprodução</th>
              <th>Morte no nasc.</th>
            </tr>
            <?
            $nascimento = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial_' AND data_de_nascimento <= '$data_final_' ORDER BY data_de_nascimento asc");
            foreach ($nascimento as $animal){
              $x++;
              $data_atual = $animal['data_de_nascimento'];
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
            ?>
            <tr>
              <td><?=$x?></td>

               <? if($animal['status'] == 0){ ?> <td onclick="abrir_animal(<?=$animal['id']?>)" style="cursor:pointer;"> <? }else{ ?>
                <td onclick="abrir_animal(<?=$animal['id']?>)" style="cursor:pointer; color:red;"> <? } ?> <?=$animal['nome']?></td>
              <td><?=$data?></td>
              <td><?=$animal['tipo_reproducao']?></td>
              <?
              if($animal['causa_da_perda'] == 'Nascimento'){?> <td style="color:red;"> <? echo 'Sim';}else{ ?> <td> <? echo 'não'; } ?>
            </td>
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
