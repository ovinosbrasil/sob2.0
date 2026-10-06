<?
function addDayIntoDate($date,$days) {
     $thisyear = substr ( $date, 0, 4 );
     $thismonth = substr ( $date, 4, 2 );
     $thisday =  substr ( $date, 6, 2 );
     $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday + $days, $thisyear );
     return strftime("%Y%m%d", $nextdate);
}

$tipo = $_GET['tipo'] ?? ''; ?>


<script type="text/javascript">
function atualizar(x){
  window.location.href = "geral.php?pg=cadastrar_animal&tipo="+x;
}

function cadastrar_monta(x,id_lote,tipo){
  data = document.getElementById("data_nascimento").value;
  window.location.href = "geral.php?pg=cadastrar_nascimento&x="+x+"&id_lote="+id_lote+"&tipo="+tipo+"&data_de_nascimento="+data;
}

</script>

<section class="content-header">
  <h1>
    Cadastrar animal
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-plus"></i> Cadastrar</a></li>
  </ol>
</section>

<section class="content">
  <div class="box" style="border-top:0;" aria-labelledby="titulo-dicas-cadastro">
    <div class="box-header with-border">
      <h3 class="box-title" id="titulo-dicas-cadastro"><i class="fa fa-lightbulb-o text-muted" aria-hidden="true"></i> Dicas</h3>
    </div>
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6 col-md-3">
          <h4 style="font-size:14px; margin-top:5px;"><strong>Rebanho</strong></h4>
          <p class="text-muted">Utilize para cadastrar animais que já existem na fazenda.</p>
        </div>
        <div class="col-sm-6 col-md-3">
          <h4 style="font-size:14px; margin-top:5px;"><strong>Compra</strong></h4>
          <p class="text-muted">Ao comprar um animal, utilize este tipo para gerar o registro financeiro da compra.</p>
        </div>
        <div class="col-sm-6 col-md-3">
          <h4 style="font-size:14px; margin-top:5px;"><strong>Nascimento</strong></h4>
          <p class="text-muted">Utilize para registrar nascimentos. Os pais precisam estar vinculados a um lote de reprodução: monta natural, inseminação artificial ou transplante de embriões. Pesquise pelo nome da mãe e data de nascimento da cria.</p>
        </div>
        <div class="col-sm-6 col-md-3">
          <h4 style="font-size:14px; margin-top:5px;"><strong>Terceiros</strong></h4>
          <p class="text-muted">Utilize para cadastrar animais que não pertencem ao seu rebanho.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <!-- left column -->
    <div class="col-md-12">
      <!-- general form elements -->
      <div class="box" style="border-top:0;">
        <!-- form start -->
          <div class="box-body">

          <div class="row"><div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label for="exampleInputEmail1">Tipo de cadastro<span style="color:#F00;">*</span></label>
              <select class="form-control select" id="tipo_cadastro" name="tipo_cadastro" onchange="atualizar(this.value)">
                <? if($tipo == 1){ ?> <option value="1">Compra</option> <? } ?>
                <? if($tipo == 3){ ?> <option value="3">Nascimento</option> <? } ?>
                <? if($tipo == 2){ ?> <option value="2">Rebanho</option> <? } ?>
                <? if($tipo == 4){ ?> <option value="4">Terceiros</option> <? } ?>
                <? if(!$tipo){ ?><option value="">Selecionar</option><? } ?>
                <option value=""></option>
                <option value="1">Compra</option>
                <option value="2">Rebanho</option>
                <option value="3">Nascimento</option>
                <option value="4">Terceiros</option>
              </select>
            </div>
         </div></div>

<? if($tipo == 1){ ?>
<!-- FIM COMPRA -->
  <form role="form" action="animal/cadastrar/_compra.php" method="post" onsubmit="return ativar_compra()" style="clear:both;">
    <div class="row" style="display:flex; flex-wrap:wrap;">
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="nome_animal">Nome<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="nome_animal" name="nome_animal">
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="tatuagem">Tatuagem<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="tatuagem" name="tatuagem" >
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="fbb">FBB</label>
                 <input type="text" class="form-control" id="fbb" name="fbb" value="">
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="sexo">Sexo<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="sexo" name="sexo" onchange="lista_colaborador(this.value)">
                 <option value="">Selecionar</option>
                   <option></option>
                   <option value="Macho">Macho</option>
                   <option value="Fêmea">Fêmea</option>
                 </select>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="raca">Raça<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="raca" name="raca">
                   <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option>
                   <option></option>
                   <?
                   $raca = DBRead('raca', "ORDER BY nome asc");
                   foreach (($raca ?: []) as $raca_) { ?>
                     <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                   <? } ?>
                 </select>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group" >
                 <label for="data_nascimento">Data de Nascimento<span style="color:#F00;">*</span></label>
                 <div class="input-group date">
                   <div class="input-group-addon">
                     <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control pull-right" id="data_nascimento" name="data_de_nascimento" >
                 </div>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="data_entrada">Data de entrada no rebanho<span style="color:#F00;">*</span></label>
                 <div class="input-group date">
                   <div class="input-group-addon">
                     <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control pull-right" id="data_entrada" name="data_de_entrada">
                 </div>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <?php renderBuscaAnimais(array(
                     'id' => 'pai',
                     'name' => 'pai',
                     'label' => 'Pai',
                     'tipo' => 'machos',
                     'required' => true,
                     'limite_origem' => 5,
                     'novo_url' => 'geral.php?pg=cadastrar_animal&tipo=2'
                 )); ?>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <?php renderBuscaAnimais(array(
                     'id' => 'mae',
                     'name' => 'mae',
                     'label' => 'Mãe',
                     'tipo' => 'femeas',
                     'required' => true,
                     'limite_origem' => 5,
                     'novo_url' => 'geral.php?pg=cadastrar_animal&tipo=2'
                 )); ?>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="valor">Preço de compra<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="valor" name="valor">
                 </div>
      </div>
      <div class="col-sm-6 col-md-8">
<div class="form-group">
                 <label for="observacoes">Observações</label>
                 <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="5" style="height:105px; width:100%;"></textarea>
               </div>
      </div>
    </div>
    <div class="text-right" style="margin-bottom:10px;">
      <button type="submit" class="btn btn-success">Cadastrar animal</button>
    </div>
  </form>
<? } ?>
<!-- FIM COMPRA -->


<? if($tipo == 2){ ?>
<!-- REBANHO -->
  <form role="form" action="animal/cadastrar/_rebanho.php" method="post" onsubmit="return ativar_rebanho()" style="clear:both;">
    <div class="row" style="display:flex; flex-wrap:wrap;">
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="nome_animal">Nome<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="nome_animal" name="nome_animal" value="<?=$user[0]['prefixo']?>">
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="tatuagem">Tatuagem<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="tatuagem" name="tatuagem" >
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="fbb">FBB</label>
                 <input type="text" class="form-control" id="fbb" name="fbb" value="">
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="sexo">Sexo<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="sexo" name="sexo" onchange="lista_colaborador(this.value)">
                 <option value="">Selecionar</option>
                   <option></option>
                   <option value="Macho">Macho</option>
                   <option value="Fêmea">Fêmea</option>
                 </select>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="raca">Raça<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="raca" name="raca">
                   <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option>
                   <option></option>
                   <?
                   $raca = DBRead('raca', "ORDER BY nome asc");
                   foreach (($raca ?: []) as $raca_) { ?>
                     <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                   <? } ?>
                 </select>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group" >
                 <label for="data_nascimento">Data de Nascimento<span style="color:#F00;">*</span></label>
                 <div class="input-group date">
                   <div class="input-group-addon">
                     <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control pull-right" id="data_nascimento" name="data_de_nascimento" >
                 </div>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <label for="data_entrada">Data de entrada no rebanho</label>
                 <div class="input-group date">
                   <div class="input-group-addon">
                     <i class="fa fa-calendar"></i>
                   </div>
                   <input type="text" class="form-control pull-right" id="data_entrada" name="data_de_entrada">
                 </div>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <?php renderBuscaAnimais(array(
                     'id' => 'pai',
                     'name' => 'pai',
                     'label' => 'Pai',
                     'tipo' => 'machos',
                     'required' => true,
                     'limite_origem' => 5,
                     'novo_url' => 'geral.php?pg=cadastrar_animal&tipo=2'
                 )); ?>
               </div>
      </div>
      <div class="col-sm-6 col-md-4">
<div class="form-group">
                 <?php renderBuscaAnimais(array(
                     'id' => 'mae',
                     'name' => 'mae',
                     'label' => 'Mãe',
                     'tipo' => 'femeas',
                     'required' => true,
                     'limite_origem' => 5,
                     'novo_url' => 'geral.php?pg=cadastrar_animal&tipo=2'
                 )); ?>
               </div>
      </div>
      <div class="col-sm-12">
<div class="form-group">
                 <label for="observacoes">Observações</label>
                 <textarea  class="form-control" name="observacoes" id="observacoes" cols="45" rows="5" style="height:105px; width:100%;"></textarea>
               </div>
      </div>
    </div>
    <div class="text-right" style="margin-bottom:10px;">
      <button type="submit" class="btn btn-success">Cadastrar animal</button>
    </div>
  </form>
<? } ?>
<!-- FIM REBANHO -->

<? if($tipo == 3){
$nome_mae = trim($_POST['mae'] ?? '');
$dataNascimentoInformada = trim($_POST['data_de_nascimento'] ?? '');
$dataNascimentoValidada = DateTime::createFromFormat('!d/m/Y', $dataNascimentoInformada);
$dataNascimentoValida = $dataNascimentoValidada && $dataNascimentoValidada->format('d/m/Y') === $dataNascimentoInformada;

?>
<!--NASCIMENTO -->
  <form role="form" action="geral.php?pg=cadastrar_animal&tipo=3" method="post">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">

          <div class="col-sm-4">
            <!-- general form elements -->
              <div class="form-group">
                <?php renderBuscaAnimais(array(
                    'id' => 'mae',
                    'name' => 'mae',
                    'label' => 'Selecionar matriz:',
                    'tipo' => 'femeas',
                    'value' => $nome_mae,
                    'limite_origem' => 5
                )); ?>
              </div>
          </div>

          <div class="col-sm-4">
            <!-- general form elements -->
              <div class="form-group" >
                <label for="data_nascimento">Data de Nascimento</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="data_nascimento" name="data_de_nascimento" value="<?=htmlspecialchars($dataNascimentoInformada, ENT_QUOTES, 'UTF-8')?>">
                </div>
              </div>
          </div>



          <div class="col-sm-4">
            <!-- general form elements -->
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Buscar lote</button>
                <a href="geral.php?pg=cadastrar_animal&amp;tipo=3" class="btn btn-default">Limpar</a>
              </div>
          </div>
        </div>
</form>
    </div>
  </div>
  <div class="box" style="border-top:0;">
    <div class="box-body">
      <h3 class="box-title" style="font-size:16px; margin:0 0 15px;">Últimos nascimentos cadastrados</h3>
      <div id="lista-ultimos-nascimentos" aria-live="polite">
        <?php
        require_once __DIR__ . '/_lista_nascimentos.php';
        renderListaNascimentos(consultarUltimosNascimentos($_GET));
        ?>
      </div>
      <p id="erro-lista-nascimentos" class="text-danger" role="alert"></p>
      <script src="animal/cadastrar/lista_nascimentos.js"></script>
          <? if($nome_mae && !$dataNascimentoValida){ ?>
            <p role="alert">Informe uma data de nascimento válida no formato dia/mês/ano para consultar os lotes da matriz.</p>
          <? }elseif ($nome_mae){ ?>
          <div class="modal fade" id="modal-lotes-reproducao" tabindex="-1" role="dialog" aria-labelledby="titulo-lotes-reproducao">
            <div class="modal-dialog modal-lg" role="document" style="width:95%; max-width:1200px;">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="titulo-lotes-reproducao">Lotes de reprodução da matriz</h4>
                </div>
                <div class="modal-body">
                  <? ob_start(); ?>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead><tr>
              <th>Tipo</th>
              <th>Lote</th>
              <th>Macho</th>
              <th>Data</th>
              <th>Previsão de parto</th>
              <th>Receptora</th>
              <th>Cadastrar</th>
            </tr></thead>
            <tbody>
            <?
            $mae = DBRead('animais', "WHERE nome = '$nome_mae'");
            if($mae[0]['id'] <= 0){
                $mae = DBRead('terceiros', "WHERE nome = '$nome_mae'");
            }
            $id_mae = $mae[0]['id'];


            $monta = DBRead('monta_controle', "WHERE id_animal = '$id_mae'");
            foreach (($monta ?: []) as $monta_){
              $id_monta_controle = $monta_['id'];
              $id_monta = $monta_['id_monta'];
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
              $data_inicio = $data;

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
              $data_fim = $data;


              $data = explode("/", $data_inicio);
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

              $data = explode("/", $data_fim);
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

              $data_parto_ = $dataNascimentoValidada->format('Y-m-d');

              $data_atual = $data_previsao1;
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
              $data_previsao1_ = $data;

              $data_atual = $data_previsao2;
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
              $data_previsao2_ = $data;

              if($data_parto_ >= '1990-01-01'){
                if(($data_parto_ >= $data_previsao1_) && ($data_parto_ <= $data_previsao2_)){
            ?>
              <? if($monta_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
              <? if(!$monta_['status_nascimento']){ ?> <tr> <? } ?>
              <td>Monta natural</td>
              <td>Lote <?=$lote[0]['codigo']?></td>
              <? if($lote[0]['terceiro']){ ?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
              <? if(!$lote[0]['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
              <td>Inicial: <?=$data_inicio?> Final: <?=$data_fim?></td>
              <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
              <td></td>
              <td>
                  <? if($monta_['status_nascimento']){ ?>
                <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_monta_controle?>, '1')" style="color:green;">
                <? }else{ ?> <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_monta_controle?>, '1')"> <? } ?>
                  <? if($monta_['status_nascimento']){?> <option value="" style="color:green;">Cadastrado</option> <? }else{ ?>
                 <option value="">Selecionar</option><? } ?>
                 <option value=""></option>
                 <option value="1">Parto simples</option>
                 <option value="2">Parto duplo</option>
                 <option value="3">Parto triplo</option>
               </select>
             </td>
            </tr>
          <? }
        }else{ ?>

          <? if($monta_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$monta_['status_nascimento']){ ?> <tr> <? } ?>
          <td>Monta natural</td>
          <td>Lote <?=$lote[0]['codigo']?></td>
          <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td>
          <td>Inicial: <?=$data_inicio?> Final: <?=$data_fim?></td>
          <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
          <td></td>
          <td>
            <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_monta_controle?>, '1')">
             <option value="">Selecionar</option>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select>
         </td>
        </tr>
      <? } }
          //INSEMINAÇÃO
          $inseminacao = DBRead('inseminacao_controle', "WHERE id_femea = '$id_mae'");
          foreach (($inseminacao ?: []) as $inseminacao_) {
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

            $data_parto_ = $dataNascimentoValidada->format('Y-m-d');

            $data_atual = $data_previsao1;
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
            $data_previsao1_ = $data;

            $data_atual = $data_previsao2;
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
            $data_previsao2_ = $data;

            if($data_parto_ >= '1990-01-01'){
              if(($data_parto_ >= $data_previsao1_) && ($data_parto_ <= $data_previsao2_)){

          ?>
          <? if($inseminacao_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$inseminacao_['status_nascimento']){ ?> <tr> <? } ?>
            <td>Inseminação artificial</td>
            <td>Lote <?=$lote[0]['codigo']?></td>
            <? if($lote[0]['terceiro']){?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <? if(!$lote[0]['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <td><?=$data_inseminacao?></td>
            <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
            <td></td>
            <td>
              <? if($inseminacao_['status_nascimento']){ ?>
            <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_inseminacao_controle?>, '2')" style="color:green;">
            <? }else{ ?> <select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$id_inseminacao_controle?>, '2')"> <? } ?>
              <? if($inseminacao_['status_nascimento']){?> <option value="" style="color:green;">Cadastrado</option> <? }else{ ?>
             <option value="">Selecionar</option><? } ?>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select>
         </td>
          </tr>
        <? } }else{ ?>

          <? if($inseminacao_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$inseminacao_['status_nascimento']){ ?> <tr> <? } ?>
            <td>Inseminação artificial</td>
            <td>Lote <?=$lote[0]['codigo']?></td>
            <? if($lote[0]['terceiro']){?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <? if(!$lote[0]['terceiro']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <td><?=$data_inseminacao?></td>
            <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
            <td></td>
            <td>
              <select class="form-control select" id="tipo_parto" name="tipo_parto"  onchange="cadastrar_monta(this.value, <?=$id_inseminacao_controle?>, '2')">
             <option value="">Selecionar</option>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select>
         </td>
       </tr> <? } }

          //TE
          $te = DBRead('transplante', "WHERE id_mae = '$id_mae'");
          foreach (($te ?: []) as $te_){
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


            $data = explode("/", $data_te);
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

            $data = explode("/", $data_te);
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

            $data_parto_ = $dataNascimentoValidada->format('Y-m-d');

            $data_atual = $data_previsao1;
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
            $data_previsao1_ = $data;

            $data_atual = $data_previsao2;
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
            $data_previsao2_ = $data;

            if($data_parto_ >= '1990-01-01'){
              if(($data_parto_ >= $data_previsao1_) && ($data_parto_ <= $data_previsao2_)){

            $lote = DBRead('transplante_controle', "WHERE id_lote = '$id_te'");
            foreach (($lote ?: []) as $lote_){
          ?>
          <? if($lote_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$lote_['status_nascimento']){ ?> <tr> <? } ?>
            <td>T.E.</td>
            <td>Lote <?=$te_['codigo']?></td>
            <? if($te_['terceiro_pai']){ ?> <td onclick="abrir_terceiro(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <? if(!$te_['terceiro_pai']){ ?> <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td> <? } ?>
            <td><?=$data_te?></td>
            <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
            <td><?=$lote_['receptora']?></td>
            <td><select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$lote_['id']?>, '3')">
             <option value="">Selecionar</option>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select></td>
          </tr>
        <? } } }else{
          $lote = DBRead('transplante_controle', "WHERE id_lote = '$id_te'");
          foreach (($lote ?: []) as $lote_){

          ?>
          <? if($lote_['status_nascimento']){ ?> <tr style="color:green; text-align:center;"> <? } ?>
          <? if(!$lote_['status_nascimento']){ ?> <tr> <? } ?>
            <td>T.E.</td>
            <td>Lote <?=$te_['codigo']?></td>
            <td onclick="abrir_animal(<?=$macho[0]['id']?>)" style="cursor:pointer;"><?=$macho[0]['nome']?></td>
            <td><?=$data_te?></td>
            <td><?=$data_previsao1?> até <?=$data_previsao2?></td>
            <td><?=$lote_['receptora']?></td>
            <td><select class="form-control select" id="tipo_parto" name="tipo_parto" onchange="cadastrar_monta(this.value, <?=$lote_['id']?>, '3')">
             <option value="">Selecionar</option>
             <option value=""></option>
             <option value="1">Parto simples</option>
             <option value="2">Parto duplo</option>
             <option value="3">Parto triplo</option>
           </select></td>
          </tr>
        <? } } } ?>
            </tbody>
          </table>
                  </div>
                  <?
                  $tabelaLotes = ob_get_clean();
                  $possuiLotes = substr_count($tabelaLotes, "<tr") > 1;
                  if ($possuiLotes) {
                    echo $tabelaLotes;
                  } else {
                  ?>
                  <div class="alert alert-warning" role="alert" style="margin-bottom:0;">
                    <h4 style="margin-top:0;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Nenhum lote encontrado</h4>
                    A fêmea <strong><?=htmlspecialchars($nome_mae, ENT_QUOTES, "UTF-8")?></strong> não foi cadastrada em nenhum lote com previsão de nascimento para a data <strong><?=htmlspecialchars($dataNascimentoInformada, ENT_QUOTES, "UTF-8")?></strong>.
                  </div>
                  <? } ?>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button></div>
              </div>
            </div>
          </div>
          <script>
          document.addEventListener("DOMContentLoaded", function () {
              jQuery("#modal-lotes-reproducao").modal("show");
          });
          </script>
<? } } ?>
<!-- FIM NASCIMENTO -->

<? if($tipo == 4){ ?>
<!-- TERCEIRO -->
  <form role="form" action="animal/cadastrar/_terceiros.php" method="post" onsubmit="return ativar_terceiros()" style="clear:both;">
    <div class="row">
      <div class="col-sm-6 col-md-4">
        <div class="form-group">
                 <label for="nome_animal">Nome<span style="color:#F00;">*</span></label>
                 <input type="text" class="form-control" id="nome_animal" name="nome_animal">
                       </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="form-group">
                 <label for="fbb">FBB</label>
                 <input type="text" class="form-control" id="fbb" name="fbb" value="">
                       </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="form-group">
                 <label for="sexo">Sexo<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="sexo" name="sexo" onchange="lista_colaborador(this.value)">
                 <option value="">Selecionar</option>
                   <option></option>
                   <option value="Macho">Macho</option>
                   <option value="Fêmea">Fêmea</option>
                 </select>
                       </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="form-group">
                 <?php renderBuscaAnimais(array(
                     'id' => 'pai',
                     'name' => 'pai',
                     'label' => 'Pai',
                     'tipo' => 'machos',
                     'limite_origem' => 5
                 )); ?>
                         </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="form-group">
                 <?php renderBuscaAnimais(array(
                     'id' => 'mae',
                     'name' => 'mae',
                     'label' => 'Mãe',
                     'tipo' => 'femeas',
                     'limite_origem' => 5
                 )); ?>
                        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="form-group">
                 <label for="raca">Raça<span style="color:#F00;">*</span></label>
                 <select class="form-control select" id="raca" name="raca">
                   <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option>
                   <option></option>
                   <?
                   $raca = DBRead('raca', "ORDER BY nome asc");
                   foreach (($raca ?: []) as $raca_) { ?>
                     <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
                   <? } ?>
                 </select>
                       </div>
      </div>
    </div>
    <div class="text-right" style="margin-bottom:10px;">
      <button type="submit" class="btn btn-success">Cadastrar animal</button>
    </div>
  </form>
<? } ?>
<!-- FIM TERCEIRO -->

  </div>
</div>
</div>

</div>
</section>
