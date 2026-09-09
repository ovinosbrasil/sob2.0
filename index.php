
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
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- Principal -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/principal.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page" style="width:100%; background:url(img/bg.jpg) no-repeat center top fixed; -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;">
    <div class="login-box" style="display:none;" id="bloco_esqueceu">
      <div class="login-logo">
        <img src="img/logo.png" width="350">
      </div>
      <!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Esqueceu a senha? Preencha corretamente o campo abaixo.</p>

        <form action="_esqueceu_senha.php" method="post" onsubmit="return validar2()">

          <div class="form-group has-feedback">
            <input type="e-mail" class="form-control" placeholder="E-mail" name="email" id="email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
                <label>
                  <a href="#" onclick="voltar()">Área de login</a><br>
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Enviar</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <!-- /.social-auth-links -->
      </div>
      <!-- /.login-box-body -->
    </div>

    <div class="login-box" style="display:none;" id="bloco_cadastro">
      <div class="login-logo">
        <img src="img/logo.png" width="350">
      </div>
      <!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Preencha corretamente os campos abaixo.</p>

        <form action="_cadastro.php" method="post" onsubmit="return validar3()">
          <div class="form-group has-feedback">
            <input type="input" class="form-control" placeholder="Nome completo" name="nome" id="nome">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="e-mail" class="form-control" placeholder="E-mail" name="email" id="email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="input" class="form-control" placeholder="WhatsApp" name="telefone" id="telefone">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
                <label>
                  <a href="#" onclick="voltar()">Área de login</a><br>
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-success btn-block btn-flat">Cadastrar</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <!-- /.social-auth-links -->
      </div>
      <!-- /.login-box-body -->
    </div>

<div class="login-box" id="bloco">
  <div class="login-logo">
    <img src="img/logo.png" width="350">
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Preencha os campos corretamente</p>

    <form action="_logar.php" method="post" onsubmit="return validar()">
      <div class="form-group has-feedback">
        <input type="input" class="form-control" placeholder="Login" name="login" id="login">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Senha" name="senha" id="senha">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">

      <div class="col-xs-8">
        <button type=button class="btn btn-primary btn-block btn-flat" onclick="cadastro()" style="margin-top:-0%;">Ainda não sou cadastrado</button>
      </div>

        <div class="col-xs-4">
          <button type="submit" class="btn btn-success btn-block btn-flat">Logar</button>
        </div>

        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label style="margin-top:0%;">
              <a href="#" onclick="esqueceu()">Esqueceu a senha?</a><br>
            </label>
          </div>
        </div>
      </div>
    </form>
    <!-- /.social-auth-links -->
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });

  function validar(){
    saida = 0;
  	if(!document.getElementById("login").value){
      document.getElementById("login").style.border = "1px solid red";
      saida = 1;
    }else{document.getElementById("login").style.border = "1px solid green";}

    if(!document.getElementById("senha").value){
      document.getElementById("senha").style.border = "1px solid red";
      saida = 1;
    }else{document.getElementById("senha").style.border = "1px solid green";}


    if(saida){ return false; }else{ return true; }
  }

  function validar2(){
    saida = 0;
  	if(!document.getElementById("email").value){
      document.getElementById("email").style.border = "1px solid red";
      saida = 1;
    }else{document.getElementById("email").style.border = "1px solid green";}

    if(saida){ return false; }else{ return true; }
  }

  function esqueceu(){
    document.getElementById('bloco').style.display = 'none';
    document.getElementById('bloco_esqueceu').style.display = 'block';
    document.getElementById('bloco_cadastro').style.display = 'none';
  }

  function cadastro(){
    document.getElementById('bloco').style.display = 'none';
    document.getElementById('bloco_esqueceu').style.display = 'none';
    document.getElementById('bloco_cadastro').style.display = 'block';
  }

  function voltar(){
    document.getElementById('bloco').style.display = 'block';
    document.getElementById('bloco_esqueceu').style.display = 'none';
    document.getElementById('bloco_cadastro').style.display = 'none';
  }
</script>
</body>
</html>
