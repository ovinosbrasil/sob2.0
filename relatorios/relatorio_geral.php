
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

//CONTAGEM LOTES
$monta = DBRead('monta');
if($monta[0]['id'] == 0){
  $monta = 0;
}else{
$monta = count($monta);
}

$inseminacao = DBRead('inseminacao');
if($inseminacao[0]['id'] == 0){
  $inseminacao = 0;
}else{
$inseminacao = count($inseminacao);
}

$transplante = DBRead('transplante');
if($transplante[0]['id'] == 0){
  $transplante = 0;
}else{
$transplante = count($transplante);
}
//FIM CONTAGEM LOTES


//CONTAGEM NASCIMENTO
$monta_ = DBRead('animais', "WHERE entrada = '0' AND tipo_reproducao = 'Monta Natural'");
if($monta_[0]['id'] == 0){
  $monta_ = 0;
}else{
$monta_ = count($monta_);
}

$inseminacao_ = DBRead('animais', "WHERE entrada = '0' AND tipo_reproducao = 'Inseminação Artificial'");
if($inseminacao_[0]['id'] == 0){
  $inseminacao_ = 0;
}else{
$inseminacao_ = count($inseminacao_);
}

$transplante_ = DBRead('animais', "WHERE entrada = '0' AND tipo_reproducao = 'Embrionagem'");
if($transplante_[0]['id'] == 0){
  $transplante_ = 0;
}else{
$transplante_ = count($transplante_);
}
//FIM CONTAGEM  NASCIMENTO
?>

<!-- Content Header (Page header) -->


<!-- Main content -->
<section class="content">
  <!-- Small boxes (Stat box) -->
  <div class="row">
    <section class="content-header">
      <h1>
        Geral da fazenda
      </h1>
    </section>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?=$total?></h3>

          <p>Animais registrados</p>
        </div>
        <div class="icon">
          <i class="ion ion-android-add-circle"></i>
        </div>
        <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?=$vivos?></h3>

          <p>Animais vivos</p>
        </div>
        <div class="icon">
          <i class="ion ion-social-octocat"></i>
        </div>
        <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?=$vendidos?></h3>

          <p>Animais vendidos</p>
        </div>
        <div class="icon">
          <i class="ion ion-cash"></i>
        </div>
        <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?=$mortos?></h3>

          <p>Animais mortos</p>
        </div>
        <div class="icon">
          <i class="ion ion-android-cancel"></i>
        </div>
        <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
</div>


<div class="row">
    <section class="content-header">
      <h1>Lotes de reprodução
      </h1>
    </section>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?=$monta?></h3>

          <p>Monta natural</p>
        </div>
        <div class="icon">
          <i class="fa fa-venus-mars"></i>
        </div>
        <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?=$inseminacao?></h3>

          <p>Inseminação artificial</p>
        </div>
        <div class="icon">
          <i class="fa fa-eyedropper"></i>
        </div>
        <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
        <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?=$transplante?></h3>

          <p>Transplante de embriões</p>
        </div>
        <div class="icon">
          <i class="ion ion-erlenmeyer-flask"></i>
        </div>
        <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
</div>

<div class="row">
  <section class="content-header">
    <h1>Nascimentos
    </h1>
  </section>
  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-green">
      <div class="inner">
        <h3><?=$monta_?></h3>

        <p>Monta natural</p>
      </div>
      <div class="icon">
        <i class="fa fa-venus-mars"></i>
      </div>
      <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->

  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-green">
      <div class="inner">
        <h3><?=$inseminacao_?></h3>

        <p>Inseminação artificial</p>
      </div>
      <div class="icon">
        <i class="fa fa-eyedropper"></i>
      </div>
      <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->

  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-green">
      <div class="inner">
        <h3><?=$transplante_?></h3>

        <p>Transplante de embriões</p>
      </div>
      <div class="icon">
        <i class="ion ion-erlenmeyer-flask"></i>
      </div>
      <a href="#" class="small-box-footer">Lista de animais <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->

</div>
</section>
<!-- /.content -->
