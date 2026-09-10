<?php
//ini_set('display_errors', 0);
include "_config.php";
$user = DBRead('admin');
$id_user = $user[0]['id'];
$login_user = $_SESSION['login'];
date_default_timezone_set('America/Sao_Paulo');
$pg = $_GET['pg'];
//DEFINIR DARA DE EXPIRAÇÃO
$data = $_SESSION['data_expira'];
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
$data_expira2 = $data;
//FIM DATA
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Sistema Ovinos Brasil :: Software de controle Ovinos e Caprinos</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">

  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/principal.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">


<div class="row" id="palco_excluir" style=" z-index:99999999999999;  left:40%; top:5%;  position:absolute; position:fixed;"></div>

<div id="transparencia" style="display:none;"></div>

<div class="wrapper">
  <header class="main-header">
    <!-- Logo -->
    <a href="geral.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">SOB</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Sistema </b>Ovinos Brasil</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="img/logo_user/<?=$user[0]['logo']?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?=$login_user?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="img/logo_user/<?=$user[0]['logo']?>" class="img-circle" alt="User Image">

                <p>
                  <?=$user[0]['responsavel']?>
                  <small>Licença expira em: <?=$data_expira2?></small>
                </p>
              </li>
              <!-- Menu Body -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="geral.php?pg=perfil" class="btn btn-default btn-flat">Perfil</a>
                </div>
                <div class="pull-right">
                  <a href="_logout.php" class="btn btn-default btn-flat">Logout</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- search form -->
        <form action="chip/animal/_pesquisar_animal.php" method="post" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="animal" class="form-control" placeholder="Pesquisar animal..." onKeyUp="pesquisar_animal(this.value)">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
        <div id="lista_animal" style="border-style:solid; border-width:2px; height:auto; border-color: #4f8730; position:absolute; z-index:99999; background:#fff; width:500%; display:none; margin:0.5%; margin-top:2%;">
        </div>
      </form>

      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU</li>
        <? if($pg == ''){ ?> <li class="active treeview"> <? }else{ ?><li> <? } ?>
          <a href="geral.php"><i class="fa fa-home"></i> <span>Home</span></a>
        </li>

        <? if(($pg == 'cadastrar_animal') || ($pg == 'cadastrar_nascimento') || ($pg == 'lista_terceiros') || ($pg == 'lista_rebanho')){ ?> <li class="active treeview"> <? }else{ ?> <li class="treeview"><? } ?>
          <a href="#">
            <i class="fa fa-github-alt"></i>
            <span>Animais</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="geral.php?pg=cadastrar_animal"><i class="fa fa-plus"></i>Cadastrar</a></li>
            <li><a href="geral.php?pg=lista_rebanho"><i class="fa fa-github-alt"></i>Rebanho</a></li>
            <li><a href="geral.php?pg=lista_terceiros"><i class="fa fa-user"></i>Terceiros</a></li>
          </ul>
        </li>

        <? if(($pg == 'lista_monta') || ($pg == 'monta') || ($pg == 'cadastrar_monta') || ($pg == 'lista_inseminacao') || ($pg == 'inseminacao') || ($pg == 'cadastrar_inseminacao') || ($pg == 'lista_te') ||
        ($pg == 'te') || ($pg == 'cadastrar_te') || ($pg == 'lista_te')
        || ($pg == 'lista_ultrassom') || ($pg == 'embrioes') || ($pg == 'vendas_embriao') || ($pg == 'semen') || ($pg == 'vender_semen')
        || ($pg == 'relatorio_arco')){ ?> <li class="active treeview"> <? }else{ ?> <li class="treeview">
        <? } ?>
          <a href="#">
            <i class="fa fa-venus-mars"></i>
            <span>Reprodução</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="geral.php?pg=lista_monta"><i class="fa fa-venus-mars"></i> Monta natural</a></li>
            <li><a href="geral.php?pg=lista_inseminacao"><i class="fa fa-eyedropper"></i> Inseminação artificial</a></li>
            <li><a href="geral.php?pg=lista_te"><i class="fa fa-flask"></i> Trans. de embrião</a></li>
            <li><a href="geral.php?pg=semen"><i class="fa fa-mars"></i> Banco de sêmens</a></li>
            <li><a href="geral.php?pg=embrioes"><i class="fa fa-gg-circle"></i> Banco de embriões</a></li>
            <li><a href="geral.php?pg=lista_ultrassom"><i class="fa fa-qq"></i> Ultrassom</a></li>
            <li><a href="geral.php?pg=relatorio_arco"><i class="fa fa-print"></i> Relatório ARCO</a></li>
            <li><a href=""><i class="fa fa-venus-mars"></i> Acasalamento</a></li>
          </ul>
        </li>

        <? if(($pg == 'vacinas') || ($pg == 'cadastrar_vacina') || ($pg == 'vacina')){ ?> <li class="active treeview"> <? }else{ ?><li> <? } ?>
          <a href="geral.php?pg=vacinas"><i class="fa fa-eyedropper"></i> <span>Vacinas</span></a>
        </li>

        <? if(($pg == 'pesagem')){ ?> <li class="active treeview"> <? }else{ ?><li> <? } ?>
          <a href="geral.php?pg=pesagem"><i class="fa fa-sort"></i> <span>Pesagem</span></a>
        </li>

        <? if(($pg == 'relatorio_geral') || ($pg == 'relatorio_mortes') || ($pg == 'relatorio_doencas') || ($pg == 'relatorio_reprodutores') || ($pg == 'relatorio_reprodutor') || ($pg == 'relatorio_matrizes') || ($pg == 'relatorio_matriz')
        || ($pg == 'relatorio_tipificacao') || ($pg == 'relatorio_reproducao') || ($pg == 'relatorio_nascimentos')){ ?> <li class="active treeview"> <? }else{ ?> <li class="treeview"><? } ?>
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Relatórios</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="geral.php?pg=relatorio_geral"><i class="fa fa-tree"></i>Geral</a></li>
            <li><a href="geral.php?pg=relatorio_mortes"><i class="fa fa-qq"></i>Mortes</a></li>
            <li><a href="geral.php?pg=relatorio_nascimentos"><i class="fa fa-github-alt"></i>Nascimentos</a></li>
            <li><a href="geral.php?pg=relatorio_doencas"><i class="fa fa-plus-circle"></i>Doenças</a></li>
            <li><a href="geral.php?pg=relatorio_reprodutores"><i class="fa fa-mars"></i>Reprodutores</a></li>
            <li><a href="geral.php?pg=relatorio_matrizes"><i class="fa fa-venus"></i>Matrizes</a></li>
            <li><a href="geral.php?pg=relatorio_tipificacao"><i class="fa fa-star"></i>Tipificação das crias</a></li>
            <li><a href="geral.php?pg=relatorio_reproducao"><i class="fa fa-venus-mars"></i>Lotes de reprodução</a></li>
          </ul>
        </li>


<? if(($pg == 'cadastrar_exposicao') || ($pg == 'exposicao') || ($pg == 'premiacao') || ($pg == 'julgamento') || ($pg == 'relatorio_vendas_exposicao') || ($pg == 'pesquisar_exposicao')){
   ?> <li class="active treeview"> <? }else{ ?> <li class="treeview"><? } ?>
          <a href="#">
            <i class="fa fa-trophy"></i>
            <span>Exposição</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="geral.php?pg=cadastrar_exposicao"><i class="fa fa-plus"></i>Cadastrar</a></li>
            <li><a href="geral.php?pg=pesquisar_exposicao"><i class="fa fa-search"></i>Pesquisar</a></li>
          </ul>
        </li>

        <? if(($pg == 'compradores') || ($pg == 'comprador') || ($pg == 'relatorio_venda')){ ?> <li class="active treeview"> <? }else{ ?> <li class="treeview"><? } ?>
          <a href="#">
            <i class="fa fa-shopping-cart"></i>
            <span>Vendas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="geral.php?pg=vender"><i class="fa fa-shopping-cart"></i>Vender</a></li>
            <li><a href="geral.php?pg=compradores"><i class="fa fa-user"></i>Comprador</a></li>
            <li><a href="geral.php?pg=relatorio_venda"><i class="fa fa-book"></i>Relatórios</a></li>
          </ul>
        </li>


        <? if($pg == 'financeiro'){ ?> <li class="active treeview"> <? }else{ ?><li> <? } ?>
          <a href="geral.php?pg=financeiro"><i class="fa fa-money"></i> <span>Financeiro</span></a>
        </li>

        <? if($pg == 'cadastrar_chip'){ ?> <li class="active treeview"> <? }else{ ?><li> <? } ?>
          <a href="geral.php?pg=cadastrar_chip"><i class="fa fa-magic"></i> <span>Cadastrar chip</span></a>
        </li>


      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?
    include "paginacao.php";
    ?>
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.0
    </div>
    <strong>Copyright &copy; 2020</strong> Todos os direitos reservados
  </footer>

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="bower_components/raphael/raphael.min.js"></script>
<script src="bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="bower_components/moment/min/moment.min.js"></script>
<script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- Os gráficos da aplicação são inicializados abaixo. -->
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>



<?
if(($pg == 'perfil') || ($pg == 'comprador') || ($pg == 'compradores')){
?>
<!--mascara-->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $("#celular").mask("(99) 99999-9999");
    $("#telefone").mask("(99) 9999-9999");
    $("#cpf").mask("999.999.999-99");
  })
</script>
<? } ?>





<? if($pg == 'pesagem'){ ?>
  <script type="text/javascript">

  $(function () {
      "use strict";
      // LINE CHART
      var line = new Morris.Line({
        element: 'line-chart',
        resize: true,
        data: [
          {y: '<?=$data1?>', item1: <?=$pesagem1?>},
          {y: '<?=$data2?>', item1: <?=$pesagem2?>},
          {y: '<?=$data3?>', item1: <?=$pesagem3?>},
          {y: '<?=$data4?>', item1: <?=$pesagem4?>},
          {y: '<?=$data5?>', item1: <?=$pesagem5?>},
          {y: '<?=$data6?>', item1: <?=$pesagem6?>},
          {y: '<?=$data7?>', item1: <?=$pesagem7?>},
          {y: '<?=$data8?>', item1: <?=$pesagem8?>},
          {y: '<?=$data9?>', item1: <?=$pesagem9?>}
        ],
        xkey: 'y',
        ykeys: ['item1'],
        labels: ['Item 1'],
        lineColors: ['#3c8dbc'],
        hideHover: 'auto'
      });
  });
  </script>
<? } ?>


<? if($pg == 'relatorio_venda'){ ?>
  <script type="text/javascript">
  $(function () {
      "use strict";
      // LINE CHART
      var bar = new Morris.Bar({
        element: 'bar-chart3',
        resize: true,
        data: [
          {y: 'Jan', a: <?=$venda_janeiro?>},
          {y: 'Fev', a: <?=$venda_fevereiro?>},
          {y: 'Mar', a: <?=$venda_marco?>},
          {y: 'Abr', a: <?=$venda_abril?>},
          {y: 'Mai', a: <?=$venda_maio?>},
          {y: 'Jun', a: <?=$venda_junho?>},
          {y: 'Jul', a: <?=$venda_julho?>},
          {y: 'Ago', a: <?=$venda_agosto?>},
          {y: 'Set', a: <?=$venda_setembro?>},
          {y: 'Out', a: <?=$venda_outubro?>},
          {y: 'Nov', a: <?=$venda_novembro?>},
          {y: 'Dez', a: <?=$venda_dezembro?>}

        ],
        barColors: ['#00a65a'],
        xkey: 'y',
        ykeys: ['a'],
        labels: ['Vendas'],
        hideHover: 'auto'
      });
  });
  </script>
<? } ?>

<? if($pg == 'animal'){?>
<!-- fullCalendar -->
<link rel="stylesheet" href="bower_components/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
<!-- datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="bower_components/jprice.js"></script>
<script type="text/javascript">
  $(function () {
    $('#datepicker').datepicker({
          autoclose: true
        })
    $('#datepicker2').datepicker({
          autoclose: true
        })
    $('#datepicker3').datepicker({
          autoclose: true
        })
    $('#data_apartacao').datepicker({
          autoclose: true
      })
    $('#data_adulto').datepicker({
          autoclose: true
    })
    $('#data_venda').datepicker({
          autoclose: true
    })
    $('#data_saida').datepicker({
          autoclose: true
    })
    $('#data_doenca').datepicker({
          autoclose: true
    })
    $('#data_vacina').datepicker({
          autoclose: true
    })
    $('#peso_inicial').priceFormat();
    $('#peso_apartacao').priceFormat();
    $('#peso_adulto').priceFormat();
    $('#valor').priceFormat();
  })
</script>

<script type="text/javascript">
$(function () {
    "use strict";
    // Abas ocultas não têm largura disponível para o Morris.
    var animalCharts = [];
    function registerAnimalChart(options) {
      animalCharts.push({options: options, chart: null});
    }
    function renderAnimalCharts() {
      animalCharts.forEach(function (entry) {
        var element = document.getElementById(entry.options.element);
        if (!element || !$(element).is(':visible') || element.clientWidth < 150) return;
        if (entry.chart) {
          entry.chart.redraw();
        } else {
          entry.chart = new Morris.Bar(entry.options);
        }
      });
    }
    //BAR CHART
    if (document.getElementById('bar-chart')) {
    registerAnimalChart({
      element: 'bar-chart',
      resize: false,
      data: [
        {y: '1ª Avaliação', a: <?=json_encode(isset($ava1_) && is_numeric($ava1_) ? (float) $ava1_ : null)?>, b: <?=json_encode(isset($media1_) && is_numeric($media1_) ? (float) $media1_ : null)?>},
        {y: '2ª Avaliação', a: <?=json_encode(isset($ava2_) && is_numeric($ava2_) ? (float) $ava2_ : null)?>, b: <?=json_encode(isset($media2_) && is_numeric($media2_) ? (float) $media2_ : null)?>}
      ],
      barColors: ['#00a65a', '#78aac1'],
      xkey: 'y',
      ykeys: ['a', 'b'],
      labels: ['Animal', 'Rebanho'],
      hideHover: 'auto'
    });

    }
    if (document.getElementById('bar-chart2')) {
    registerAnimalChart({
      element: 'bar-chart2',
      resize: false,
      data: [
        {y: 'Média dos machos', a: <?=json_encode(isset($macho_media) && is_numeric($macho_media) ? round((float) $macho_media, 7) : null)?>, b: <?=json_encode(isset($media_macho) && is_numeric($media_macho) ? round((float) $media_macho, 7) : null)?>},
        {y: 'Média das fêmeas', a: <?=json_encode(isset($femea_media) && is_numeric($femea_media) ? round((float) $femea_media, 7) : null)?>, b: <?=json_encode(isset($media_femea) && is_numeric($media_femea) ? round((float) $media_femea, 7) : null)?>},
        {y: 'Média geral', a: <?=json_encode(isset($total) && is_numeric($total) ? round((float) $total, 7) : null)?>, b: <?=json_encode(isset($valor) && is_numeric($valor) ? round((float) $valor, 7) : null)?>},
      ],
      barColors: ['#00a65a', '#78aac1'],
      xkey: 'y',
      ykeys: ['a', 'b'],
      labels: ['Animal', 'Rebanho'],
      hideHover: 'auto'
    });
    }
    $('a[data-toggle="tab"]').on('shown.bs.tab', renderAnimalCharts);
    $(window).on('resize', renderAnimalCharts);
    renderAnimalCharts();
});
</script>
<? } ?>


<? if(($pg == 'cadastrar_animal') || ($pg == 'cadastrar_nascimento') || ($pg == 'vender_animal_exposicao')){?>
  <!-- fullCalendar -->
  <link rel="stylesheet" href="bower_components/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
  <!-- datepicker -->
  <script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
  <script src="bower_components/jprice.js"></script>
  <script>
    $(function () {
      $('#data_nascimento').datepicker({
            autoclose: true
          })
      $('#data_entrada').datepicker({
            autoclose: true
          })
      $('#valor').priceFormat();
      $('#peso').priceFormat();
    })
  </script>
<? } ?>

<? if(($pg == 'cadastrar_monta') || ($pg == 'monta') || ($pg == 'cadastrar_inseminacao') || ($pg == 'inseminacao') || ($pg == 'cadastrar_te') || ($pg == 'te') || ($pg = 'relatorio_mortes')
|| ($pg = 'cadastrar_exposicao')){?>
<!-- fullCalendar -->
<link rel="stylesheet" href="bower_components/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
<!-- datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="bower_components/jprice.js"></script>
<script>
  $(function () {
    $('#data_inicial').datepicker({
          autoclose: true
        })
    $('#data_final').datepicker({
          autoclose: true
        })
    $('#data').datepicker({
        autoclose: true
      })
  })
</script>
<? } ?>



<script>
$(function () {
  $('#valor_faturamento').priceFormat();
  $('#valor_debito').priceFormat();
  $('#data_faturamento').datepicker({
      autoclose: true
  })
  $('#data_debito').datepicker({
      autoclose: true
  })
  $('#data').datepicker({
      autoclose: true
    })
  $('#valor').priceFormat();
})

function somenteNumeros(num) {
        var er = /[^0-9.]/;
        er.lastIndex = 0;
        var campo = num;
        if (er.test(campo.value)) {
          campo.value = "";
        }
}

function pesquisar_animal(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "animal/lista_animal.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_animal").innerHTML = resposta;
document.getElementById("lista_animal").style.display = 'block';
}
}
PP.send(null);
}

function linkar_animal(id){
  window.location.href = "geral.php?pg=animal&id_animal="+id;
}

function linkar_terceiro(id){
  window.location.href = "geral.php?pg=terceiro&id_animal="+id;
}
function fechar_lista_animal(){
  document.getElementById("lista_animal").style.display = 'none';
}

function pesquisar_pai(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "animal/lista_pai.php?nome="+nome;
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

function linkar_pai(nome){
  document.getElementById("pai").value = nome;
  document.getElementById("lista_pai").style.display = 'none';
}

function pesquisar_mae(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "animal/lista_mae.php?nome="+nome;
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

function linkar_mae(nome){
  document.getElementById("mae").value = nome;
  document.getElementById("lista_mae").style.display = 'none';
}

function pesquisar_matriz(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "animal/lista_matriz.php?nome="+nome;
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

function abrir_animal(id){
  window.open('geral.php?pg=animal&id_animal='+id, '_blank');
}

function abrir_terceiro(id){
  window.open('geral.php?pg=terceiro&id_animal='+id, '_blank');
}
function pesquisar_comprador(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "animal/vender/lista_comprador.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_comprador").innerHTML = resposta;
document.getElementById("lista_comprador").style.display = 'block';
}
}
PP.send(null);
}

function fechar_lista_comprador(){
  document.getElementById("lista_comprador").style.display = 'none';
}

function linkar_comprador(nome){
  document.getElementById("comprador").value = nome;
  document.getElementById("lista_comprador").style.display = 'none';
}

function preco_conjunto(id){
    $("#preco_conjunto_"+id).priceFormat();
}

function pesquisar_animal_conjunta(nome,id){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "vendas/lista_animal_conjunto.php?nome="+nome+"&id="+id;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_animal_"+id).innerHTML = resposta;
document.getElementById("lista_animal_"+id).style.display = 'block';
}
}
PP.send(null);
}

function linkar_animal_conjunto(nome,id){
  document.getElementById("animal_"+id).value = nome;
  document.getElementById("lista_animal_"+id).style.display = 'none';
}

function fechar_lista_conjunta(id){
  document.getElementById("lista_animal_"+id).style.display = 'none';
}



function pesquisar_animal_pesagem(nome){
if(window.XMLHttpRequest) { PP = new XMLHttpRequest();} else if(window.ActiveXObject) { PP = new ActiveXObject("Microsoft.XMLHTTP"); }
// Arquivo PHP juntamente com o valor digitado no campo (método GET)
var url = "pesagem/lista_animal_peso.php?nome="+nome;
// Chamada do método open para processar a requisição
PP.open("Get", url, true);
// Quando o objeto recebe o retorno, chamamos a seguinte função;
PP.onreadystatechange = function() {
if (PP.readyState == 4) {
resposta = PP.responseText;
document.getElementById("lista_animal_peso").innerHTML = resposta;
document.getElementById("lista_animal_peso").style.display = 'block';
}
}
PP.send(null);
}

function linkar_animal_peso(id){
    window.location.href = "geral.php?pg=pesagem&id_animal="+id;
}

function fechar_lista_peso(){
  document.getElementById("lista_animal_peso").style.display = 'none';
}


setTimeout(function(){
  focus();
}, 2000);

</script>

</body>
</html>
