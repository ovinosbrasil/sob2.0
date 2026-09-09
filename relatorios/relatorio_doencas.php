<?
$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
?>


<section class="content-header">
  <h1>
    Relatório de doenças
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatórios</a></li>
    <li><a href="#">Doenças</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="geral.php?pg=relatorio_doencas" >
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

  </div>

    <div class="col-md-9">
      <div class="box box-success">
        <!-- /.box-header -->
        <div class="box-body">
          <table class="table table-bordered" id="tabela_padrao">
            <tr>
              <th>Nº</th>
              <th>Animal</th>
              <th>Doença</th>
              <th>Data</th>
              <th>Observações</th>
            </tr>
            <?
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
              $data_inicial = $data;
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
              $data_final = $data;
            }
           
            $doencas = DBRead('doencas', "WHERE data >= '$data_inicial' AND data <= '$data_final' ORDER BY data desc");
            foreach ($doencas as $doencas_){
              $x++;
              $data_atual = $doencas_['data'];
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

              $id_animal = $doencas_['id_animal'];
              $animal = DBRead('animais', "WHERE id = '$id_animal'");

              $id_doenca = $doencas_['id_doenca'];
              $nome_doenca = DBRead('doenca', "WHERE id = '$id_doenca'");
            ?>
            <tr>
              <td><?=$x?></td>
              <td onclick="abrir_animal(<?=$animal[0]['id']?>)" style="cursor:pointer;">
                <? if($animal[0]['status'] == 1){ ?> <span style="color:red;"> <? }else{ ?> <span> <? } echo $animal[0]['nome'];?></span></td>
              <td><?=$nome_doenca[0]['nome']?></td>
              <td><?=$data?></td>
              <td><?=$doencas_['obs']?></td>
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
