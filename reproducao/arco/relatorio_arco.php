<?
$tipo = $_POST['tipo'];
$data_inicial = $_POST['data_inicial'];

$data_inicial_ = date('d/m/Y');
if (!empty($data_inicial)) {
  $data_atual = $data_inicial;
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

$data_final = $_POST['data_final'];
$data_final_ = '';
if (!empty($data_final)) {
  $data_atual = $data_final;
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
$numeracao = $_POST['numeracao'];

?>
<section class="content-header">
  <h1>
    Pesquisar lote de nascimento
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-venus-mars"></i> Reprodução</a></li>
    <li><a href="#">Relatório arco</a></li>
  </ol>
</section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <form method="post" action="geral.php?pg=relatorio_arco">
      <div class="col-md-3">
				<div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Numeração do lote</label>
                <input type="text" class="form-control" id="numeracao" name="numeracao" value="<?=$numeracao?>">
              </div>

              <div class="form-group">
                  <label for="exampleInputPassword1">Tipo de reprodução</label>
                  <select class="form-control select" name="tipo" id="tipo">
                    <? if($tipo){ ?> <option value="<?=$tipo?>"><?=$tipo?></option><? }else{?><option value="">Selecionar</option> <? } ?>
                    <option value=""></option>
                    <option value="Monta natural">Monta natural</option>
                    <option value="Inseminação artificial">Inseminação artificial</option>
                    <option value="Trans. de embriões">Trans. de embriões</option>
                  </select>
              </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data Inicial</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_inicial" name="data_inicial" value="<?=$data_inicial?>">
                </div>
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Data Final</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_final" name="data_final" value="<?=$data_final?>">
                </div>
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Raça<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="raca" name="raca">
                <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option>
                <option></option>
                <?
                $raca = DBRead('raca', "ORDER BY nome asc");
                foreach ($raca as $raca_) { ?>
                  <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                <? } ?>
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary" style="width:100%; margin-top:4%;">Pesquisar</button>
            </div>
          </form>
        </div>
          <!-- /.box-body -->
			</div>
      <!-- /.col -->
    </div>

      <div class="col-md-9">
        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
            <?
            $raca = $_POST['raca'];
            if($tipo){
              if($tipo == 'Monta natural'){ $filtro = 'Monta Natural'; }
              if($tipo == 'Inseminação artificial'){ $filtro = 'Inseminação Artificial'; }
              if($tipo == 'Trans. de embriões'){ $filtro = 'Embrionagem'; }
            ?>
            <div class="form-group">
              <?
              if($tipo == 'Monta natural'){ ?> <a target="_blank" href="reproducao/arco/_imprimir_monta.php?numeracao=<?=$numeracao?>&raca=<?=$raca?>&data_inicial=<?=$data_inicial_?>&data_final=<?=$data_final_?>" > <? }
              if($tipo == 'Inseminação artificial'){ ?> <a target="_blank" href="reproducao/arco/_imprimir_inseminacao.php?numeracao=<?=$numeracao?>&raca=<?=$raca?>&data_inicial=<?=$data_inicial_?>&data_final=<?=$data_final_?>" > <? }
              if($tipo == 'Trans. de embriões'){ ?> <a href="reproducao/arco/_imprimir_te.php?numeracao=<?=$numeracao?>&raca=<?=$raca?>&data_inicial=<?=$data_inicial_?>&data_final=<?=$data_final_?>"> <? } ?>
               <button type="submit" class="btn btn-success" style="float:right; margin-bottom:1%;">Imprimir relatório</button> </a>
            </div>
            <table class="table table-bordered" id="tabela_padrao">
              <tr>
                <th>Fbb</th>
                <th>Nome</th>
                <th>Tat.</th>
                <th>Sexo</th>
                <th>Nascimento</th>
                <th>COD.IRREG(*)</th>
                <th>Pai</th>
                <th>Fbb</th>
                <th>Mãe</th>
                <th>Fbb</th>
              </tr>
              <?
              $animal = DBRead('animais', "WHERE data_de_nascimento >= '$data_inicial_' AND data_de_nascimento <= '$data_final_' AND tipo_reproducao = '$filtro'");
              foreach ($animal as $animais) {
              $data = $animais['data_de_nascimento'];
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

              $id_pai = $animais['pai'];
              if($animais['terceiro_pai']){
                $pai = DBRead('terceiros', "WHERE id = '$id_pai'");
              }else{
                $pai = DBRead('animais', "WHERE id = '$id_pai'");
              }

              $id_mae = $animais['mae'];
              if($animais['terceiro_mae']){
                $mae = DBRead('terceiros', "WHERE id = '$id_mae'");
              }else{
                $mae = DBRead('animais', "WHERE id = '$id_mae'");
              }
              ?>
                <tr>
                    <td><?=$animais['fbb']?></td>
                    <td onclick="abrir_animal(<?=$animais['id']?>)" style="cursor:pointer;" ><?=$animais['nome']?></td>
                    <td><?=$animais['tatuagem']?></td>
                    <td><?=$animais['sexo']?></td>
                    <td><?=$data?></td>
                    <td></td>
                    <td><?=$pai[0]['nome']?></td>
                    <td><?=$pai[0]['fbb']?></td>
                    <td><?=$mae[0]['nome']?></td>
                    <td><?=$mae[0]['fbb']?></td>
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
