<?php
$tarefaFlash = $_SESSION['tarefa_flash'] ?? null;
unset($_SESSION['tarefa_flash']);
$tarefaValores = $tarefaFlash['valores'] ?? array();
if (empty($_SESSION['tarefa_exclusao_csrf'])) {
    $_SESSION['tarefa_exclusao_csrf'] = bin2hex(random_bytes(32));
}
?>
<link rel="stylesheet" href="dist/css/alertas.css?v=<?=filemtime(__DIR__ . '/dist/css/alertas.css')?>">
<script src="dist/js/alertas.js?v=<?=filemtime(__DIR__ . '/dist/js/alertas.js')?>"></script>
<script src="alerta/cadastro_tarefa.js?v=<?=filemtime(__DIR__ . '/alerta/cadastro_tarefa.js')?>"></script>
<?php if ($tarefaFlash): ?>
<script>
SobAlertas.mostrar(<?=json_encode(array('tipo' => $tarefaFlash['tipo'], 'mensagem' => $tarefaFlash['mensagem']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>);
</script>
<?php endif; ?>
<script type="text/javascript">
function excluir_alerta(botao){
  confirmarExclusao({
    titulo: 'Excluir tarefa?',
    nome: botao.getAttribute('data-titulo'),
    descricao: 'Confirme se deseja excluir esta tarefa do calendário de manejo. Esta ação não pode ser desfeita.',
    aoConfirmar: function () {
      var formulario = document.getElementById('excluir-tarefa-form');
      formulario.elements.id_alerta.value = botao.getAttribute('data-id');
      formulario.submit();
    }
  });
}

</script>


<?
//CONTAGEM ANIMAIS
$total = DBRead('animais');
if($total[0]['id'] == 0){
  $total = 0;
}else{
$total = count($total);
}

$vivos = DBRead('animais', "WHERE status = 0");
if($vivos[0]['id'] == 0){
  $vivos = 0;
}else{
$vivos = count($vivos);
}

$mortos = DBRead('animais', "WHERE status = 1");
if($mortos[0]['id'] == 0){
  $mortos = 0;
}else{
$mortos = count($mortos);
}

$vendidos = DBRead('animais', "WHERE status = 2");
if($vendidos[0]['id'] == 0){
  $vendidos = 0;
}else{
$vendidos = count($vendidos);
}
//FIM CONTAMGE ANIMAIS
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Geral
    <small>Dados da fazenda</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
    <li class="active">Página inicial</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?=$total?></h3>

          <p>Animais registrados</p>
        </div>
        <div class="icon">
          <i class="ion ion-social-octocat"></i>
        </div>
        <a href="geral.php?pg=lista_rebanho" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3><?=$vivos?></h3>

          <p>Animais vivos</p>
        </div>
        <div class="icon">
          <i class="ion ion-social-octocat"></i>
        </div>
        <a href="geral.php?pg=lista_rebanho&filtro=Rebanho" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?=$vendidos?></h3>

          <p>Animais vendidos</p>
        </div>
        <div class="icon">
          <i class="ion ion-social-octocat"></i>
        </div>
        <a href="geral.php?pg=relatorio_venda" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
          <h3><?=$mortos?></h3>

          <p>Animais mortos</p>
        </div>
        <div class="icon">
          <i class="ion ion-social-octocat"></i>
        </div>
        <a href="geral.php?pg=relatorio_mortes" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>
    <!-- ./col -->

  <div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <i class="fa fa-warning"></i>
          <h3 class="box-title">Calendário de manejo</h3>
        </div>

      <form method="post" action="alerta/_cadastrar.php" onsubmit="return validarTarefa()" novalidate>
        <!-- /.box-header -->
        <div class="box-body">

        <div class="col-md-3">
          <div class="form-group">
              <label for="titulo">Título<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="titulo" name="titulo" required value="<?=htmlspecialchars($tarefaValores['titulo'] ?? '', ENT_QUOTES, 'UTF-8')?>">
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
              <label for="data">Data<span style="color:#F00;">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="data" name="data" required value="<?=htmlspecialchars($tarefaValores['data'] ?? '', ENT_QUOTES, 'UTF-8')?>">
              </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
              <label for="prioridade">Prioridade</label>
              <select class="form-control select" id="prioridade" name="prioridade">
              <option value="x">Selecionar</option>
                <option></option>
                <option value="1" <?=($tarefaValores['prioridade'] ?? '') === '1' ? 'selected' : ''?>>Sim</option>
                <option value="0" <?=($tarefaValores['prioridade'] ?? '') === '0' ? 'selected' : ''?>>Não</option>
              </select>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <button type="submit" class="btn btn-success" style="margin-top:2%;">Cadastrar tarefa</button>
          </div>
      </div>
      </div>
    </form>
        <?
        $alerta = DBRead('alerta', "ORDER BY data asc") ?: [];
        foreach ($alerta as $alerta_) {
          $id_alerta = $alerta_['id'];
          $data_atual = $alerta_['data'];
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

        <? if($alerta_['status']){ ?>
            <div class="alert alert-danger alert-dismissible" style="width:98%; margin-left:1%;"> <? }else{ ?> <div class="alert alert-success alert-dismissible" style="width:98%; margin-left:1%;"> <? } ?>
            <button type="button" class="close" aria-label="Excluir tarefa" data-id="<?=(int)$id_alerta?>" data-titulo="<?=htmlspecialchars($alerta_['titulo'], ENT_QUOTES, 'UTF-8')?>" onclick="excluir_alerta(this)">&times;</button>
            <h4><i class="icon fa fa-warning"></i> Data: <?=$data?> </h4>
            <?=htmlspecialchars($alerta_['titulo'], ENT_QUOTES, 'UTF-8')?>
            </div>
        <? } ?>
        <br/>

    </div>
</div>
</section>
<!-- /.content -->

<form id="excluir-tarefa-form" action="alerta/_excluir_alerta.php" method="post" hidden>
  <input type="hidden" name="id_alerta" value="">
  <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['tarefa_exclusao_csrf'], ENT_QUOTES, 'UTF-8')?>">
</form>
