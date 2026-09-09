<?

//mysql_close($db);

// $db = mysql_connect("localhost", "siste870_sob","sob123");
// echo $login_user;
// $dados = mysql_select_db("siste870_sob",$db);
// $senha = mysql_query("SELECT * FROM user WHERE login = '$login_user'");
// $senha = mysql_fetch_array($senha);
// echo $senha;
// mysql_close($db);

define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'siste870_sob');
define('DB_PASSWORD', 'sob123');
define('DB_DATABASE', 'siste870_user');
define('DB_CHARSET', 'latin1');

// require '../mysqli/_conexao.php';
// require '../mysqli/_database.php';

// $senha = DBRead('user', "WHERE login = '$login_user'");
// echo "asdasdasdsda",$senha;
?>



<script type="text/javascript">
function ativar(){
  saida = 0;
	if(!document.getElementById("nome").value){
    document.getElementById("nome").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("nome").style.border = "1px solid green";}

  if(!document.getElementById("cpf").value){
    document.getElementById("cpf").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("cpf").style.border = "1px solid green";}

  if(!document.getElementById("celular").value){
    document.getElementById("celular").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("celular").style.border = "1px solid green";}

  if(!document.getElementById("senha").value){
    document.getElementById("senha").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("senha").style.border = "1px solid green";}

  if(!document.getElementById("fazenda").value){
    document.getElementById("fazenda").style.border = "1px solid red";
    saida = 1;
  }else{document.getElementById("fazenda").style.border = "1px solid green";}

  if(saida){ return false; }else{ return true; }
}
</script>

<section class="content-header">
  <h1>
    Dados da gerais
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Perfil</a></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <!-- left column -->
    <div class="col-md-6">
      <!-- general form elements -->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Dados pessoais</h3>
        </div>
        <!-- /.box-header -->

        <!-- form start -->
        <form role="form" action="perfil/_alterar.php?id=<?=$id_user?>" method="post" onsubmit="return ativar()">
          <div class="box-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Nome completo<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="nome" name="nome" value="<?=$user[0]['responsavel']?>">
            </div>

            <div class="form-group">
              <label for="exampleInputEmail1">CPF/CNPJ<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" id="cpf" name="cpf" value="<?=$user[0]['cpf']?>">
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">E-mail</label>
              <input type="text" class="form-control" name="email" value="<?=$user[0]['email']?>">
            </div>


            <div class="form-group">
              <label for="exampleInputPassword1">Celular (Whatsapp)<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" name="celular" id="celular" value="<?=$user[0]['celular']?>">
            </div>

            <div class="form-group">
              <label for="exampleInputPassword1">Telefone</label>
              <input type="text" class="form-control" name="telefone" id="telefone" value="<?=$user[0]['telefone']?>">
            </div>

            <div class="col-md-6" style="margin-left:-12px;">
            <div class="form-group">
              <label for="exampleInputPassword1">Login<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" name="login" id="login" value="<?=$user[0]['login']?>" readonly="true">
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
              <label for="exampleInputPassword1">Senha<span style="color:#F00;">*</span></label>
              <input type="text" class="form-control" name="senha" id="senha" value="<?=$senha['senha']?>">
            </div>
            </div>
          </div>
      </div>
      <!-- /.box -->
    </div>

    <div class="col-md-6">
      <!-- general form elements -->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Dados da Fazenda</h3>
        </div>
        <div class="box-body">
        <!-- /.box-header -->

        <div class="col-md-4" style="margin-left:-12px;">
          <div class="form-group">
            <label for="exampleInputPassword1">Nome da fazenda<span style="color:#F00;">*</span></label>
            <input type="text" class="form-control" name="fazenda" id="fazenda" value="<?=$user[0]['fazenda']?>">
          </div>
        </div>

        <div class="col-md-4" style="margin-left:-12px;">
          <div class="form-group">
            <label for="exampleInputPassword1">Prefixo</label>
            <input type="text" class="form-control" name="prefixo" id="prefixo" value="<?=$user[0]['prefixo']?>">
          </div>
        </div>

        <div class="col-md-4" style="margin-left:-12px;">
          <div class="form-group">
            <label for="exampleInputPassword1">Raça padrão</label>
            <select class="form-control select" id="raca" name="raca">
              <?if($user[0]['raca'] != ''){?> <option value="<?=$user[0]['raca']?>"><?=$user[0]['raca']?></option> <? }else{?><option value="">Selecionar raca</option> <? } ?>
              <option></option>
              <?
              $raca = DBRead('raca', "ORDER BY nome asc");
              foreach ($raca as $raca_) { ?>
                <option value="<?=$raca_['nome']?>"><?=$raca_['nome']?></option>
              <? } ?>
            </select>
          </div>
        </div>

        <div class="col-md-4" style="margin-left:-12px;">
          <div class="form-group">
            <label for="exampleInputPassword1">Cod. do rebanho:</label>
            <input type="text" class="form-control" name="cod_rebanho" id="cod_rebanho" value="<?=$user[0]['cod_rebanho']?>">
          </div>
        </div>
        
        <div class="col-md-4" style="margin-left:-12px;">
          <div class="form-group">
            <label for="exampleInputPassword1">Cod. do responsável:</label>
            <input type="text" class="form-control" name="cod" id="cod" value="<?=$user[0]['cod']?>">
          </div>
        </div>

        <div class="col-md-4" style="margin-left:-12px;">
        <div class="form-group">
          <label for="exampleInputPassword1">Técnico responsável:</label>
          <input type="text" class="form-control" name="tecnico" id="tecnico" value="<?=$user[0]['tecnico']?>">
        </div>
        </div>

        <div class="col-md-4" style="margin-left:-12px;">
        <div class="form-group">
          <label for="exampleInputPassword1">Cód. do Técnico:</label>
          <input type="text" class="form-control" name="cod_tecnico" id="cod_tecnico" value="<?=$user[0]['cod_tecnico']?>">
        </div>
        </div>

        <div class="col-md-6" style="margin-left:-12px;">
        <div class="form-group">
          <label for="exampleInputPassword1">Endereço</label>
          <input type="text" class="form-control" name="end" id="end" value="<?=$user[0]['end']?>">
        </div>
        </div>

        <div class="col-md-2" style="margin-left:-12px;">
        <div class="form-group">
          <label for="exampleInputPassword1">Número</label>
          <input type="text" class="form-control" name="num" id="num" value="<?=$user[0]['num']?>">
        </div>
        </div>

        <div class="col-md-4" style="margin-left:-12px;">
        <div class="form-group">
          <label for="exampleInputPassword1">Cidade</label>
          <input type="text" class="form-control" name="cidade" id="cidade" value="<?=$user[0]['cidade']?>">
        </div>
        </div>

        <div class="col-md-3" style="margin-left:-12px;">
        <div class="form-group">
          <label for="exampleInputPassword1">Estado</label>
          <select class="form-control select" id="estado" name="estado">
          <option value="<?=$user[0]['estado']?>"><?=$user[0]['estado']?></option>
            <option></option>
            <?
            $estado = DBRead('estado', "ORDER BY estado asc");
            foreach ($estado as $estado_) {
            ?>
              <option value="<?=$estado_['sigla']?>"><?=$estado_['estado']?></option>
            <? } ?>
          </select>
        </div>
        </div>

        <div class="col-md-3" style="margin-left:-12px;">
        <div class="form-group">
          <label for="exampleInputPassword1">CEP</label>
          <input type="text" class="form-control" name="cep" id="cep" value="<?=$user[0]['cep']?>">
        </div>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-warning" style="width:100%">Alterar Perfil</button>
        </div>
        </div>
    </div>
 </div>
        <!-- form start -->

</form>
</section>
