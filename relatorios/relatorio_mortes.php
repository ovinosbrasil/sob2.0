<?
$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
?>


<section class="content-header">
  <h1>
    Relatório de mortes
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-book"></i> Relatórios</a></li>
    <li><a href="#">Mortes</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
				<div class="box box-success">
          <form method="post" action="geral.php?pg=relatorio_mortes" onsubmit="return validar_montar()">
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
        


        $mortes = DBRead('animais', "WHERE (status = '1' OR status = '5') AND (data_de_saida >= '$data_inicial' AND data_de_saida <= '$data_final') ORDER BY data_de_saida desc");
        $verminose=$clostridiose=$pneumonia=$acidente=$parto=$idade=$intoxicacao=$nascimento=$outros=$testiculos=0;
        foreach ($mortes as $mortes_){
          if($mortes_['causa_da_perda'] == 'Verminose'){ $verminose++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Clostridiose'){ $clostridiose++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Pneumonia'){ $pneumonia++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Acidente'){ $acidente++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Parto'){ $parto++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Idade'){ $idade++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Intoxicação'){ $intoxicacao++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Nascimento'){ $nascimento++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Testículos'){ $testiculos++; $qtd++;}
          if($mortes_['causa_da_perda'] == 'Outros'){ $outros++; $qtd++;}
        }
          $verminose_ = $qtd ? ($verminose*100)/$qtd : 0;
          $clostridiose_ = $qtd ? ($clostridiose*100)/$qtd : 0;
          $pneumonia_ = $qtd ? ($pneumonia*100)/$qtd : 0;
          $acidente_ = $qtd ? ($acidente*100)/$qtd : 0;
          $parto_ = $qtd ? ($parto*100)/$qtd : 0;
          $idade_ = $qtd ? ($idade*100)/$qtd : 0;
          $intoxicacao_ = $qtd ? ($intoxicacao*100)/$qtd : 0;
          $nascimento_ = $qtd ? ($nascimento*100)/$qtd : 0;
          $testiculos_ = $qtd ? ($testiculos*100)/$qtd : 0;
          $outros_ = $qtd ? ($outros*100)/$qtd : 0;
        ?>

        <div class="form-group">
          <label>Nascimento </label> <span class="badge bg-blue"><?=$nascimento?> </span> <span class="badge bg-green"><?=number_format($nascimento_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
            <label>Verminose </label>  <span class="badge bg-blue"><?=$verminose?></span> <span class="badge bg-green"><?=number_format($verminose_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Clostridiose </label>  <span class="badge bg-blue"><?=$clostridiose?></span> <span class="badge bg-green"><?=number_format($clostridiose_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Pneumonia</label> <span class="badge bg-blue"><?=$pneumonia?></span> <span class="badge bg-green"><?=number_format($pneumonia_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Acidente</label> <span class="badge bg-blue"><?=$acidente?></span> <span class="badge bg-green"><?=number_format($acidente_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Parto</label> <span class="badge bg-blue"><?=$parto?></span> <span class="badge bg-green"><?=number_format($parto_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Idade</label> <span class="badge bg-blue"><?=$idade?></span> <span class="badge bg-green"><?=number_format($idade_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Intoxicação</label> <span class="badge bg-blue"><?=$intoxicacao?></span> <span class="badge bg-green"><?=number_format($intoxicacao_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Testículos</label> <span class="badge bg-blue"><?=$testiculos?></span> <span class="badge bg-green"><?=number_format($testiculos_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Outros</label> <span class="badge bg-blue"><?=$outros?></span> <span class="badge bg-green"><?=number_format($outros_, 2, ',', '.')?>%</span>
        </div>
        <div class="form-group">
          <label>Total:</label> <span class="badge bg-red"><?=$qtd?></span>
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
              <th>Data</th>
              <th>Tipo da morte</th>
              <th>Observações</th>
            </tr>
            <?
            foreach ($mortes as $mortes_){
              $x++;
              $data_atual = $mortes_['data_de_saida'];
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
              <td onclick="abrir_animal(<?=$mortes_['id']?>)" style="cursor:pointer;" ><?=$mortes_['nome']?></td>
              <td><?=$data?></td>
              <td>
              <?
              if($mortes_['status'] == 5){ echo "Abate"; }else{
              echo $mortes_['causa_da_perda'];
              }
              ?>
              </td>
              <td><?=$mortes_['observacoes_de_saida']?></td>
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
