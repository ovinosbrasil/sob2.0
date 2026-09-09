<?
include "../_config.php";
$id_financeiro = $_GET['id_financeiro'];
$financeiro = DBRead('controle_financeiro', "WHERE id = '$id_financeiro'");
$tipo = $financeiro[0]['tipo'];
$data = $financeiro[0]['data'];
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

if($financeiro[0]['id_animal'] > 0){
  $id_animal = $financeiro[0]['id_animal'];
  $venda = DBRead('vendas', "WHERE id_animal = '$id_animal'");
  $forma = $venda[0]['forma_de_pagamento'];
}else{
  $forma = $financeiro[0]['forma_de_pagamento'];
}
?>
  <!-- left column -->
  <div class="col-md-9">
    <!-- general form elements -->
    <div class="box box-warning">
      <div class="box-header with-border">
        <h3 class="box-title">Alterar Registro Financeiro</h3>
      </div>
      <!-- /.box-header -->
              <!-- left column -->
                  <!-- /.box-header -->
                  <!-- form start -->
                  <form method="post" action="financeiro/_alterar.php?id_financeiro=<?=$id_financeiro?>&tipo=<?=$tipo?>">
                  <div class="box-body">
                    <div class="col-md-4 ">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Descrição<span style="color:#F00;">*</span></label>
                        <input type="text" class="form-control" id="descricao_faturamento" name="descricao_alterar" value="<?=$financeiro[0]['titulo']?>">
                      </div>

                      <div class="form-group">
                          <label for="exampleInputPassword1">Forma de pagamento<span style="color:#F00;">*</span></label>
                          <select class="form-control select" name="forma_alterar" id="forma_faturamento">
                            <option value="<?=$forma?>"><?=$forma?></option>
                            <option value=""></option>
                            <option value="Boleto">Boleto</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão">Cartão</option>
                            <option value="Transação bancária">Transação bancária</option>
                          </select>
                      </div>
                    </div>

                    <div class="col-md-4 " style="margin-left:-12px;">
                      <div class="form-group">
                          <label for="exampleInputPassword1">Data<span style="color:#F00;">*</span></label>
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="datepicker5" name="data_alterar" value="<?=$data?>">
                          </div>
                      </div>

                      <div class="form-group">
                        <label for="exampleInputPassword1">Categoria<span style="color:#F00;">*</span></label>
                        <select class="form-control select" name="categoria_alterar" id="categoria_debito">
                          <option value="<?=$financeiro[0]['categoria']?>"><?=$financeiro[0]['categoria']?></option>
                          <option value=""></option>
                          <option value="Custos fixos (Água, luz, telefone)">Custos fixos (Água, luz, telefone)</option>
                          <option value="Infraestrutura">Infraestrutura</option>
                          <option value="Pagamento Profissional">Pagamento Profissional</option>
                          <option value="Encargos de funcionários">Encargos de funcionários</option>
                          <option value="Contabilidade">Contabilidade</option>
                          <option value="Marketing">Marketing</option>
                          <option value="Outros">Outros</option>
                        </select>
                      </div>

                  </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="exampleInputPassword1">Valor<span style="color:#F00;">*</span></label>
                    <input type="text" id="valor_alterar_faturamento" name="valor_alterar" class="form-control" value="<?=$financeiro[0]['valor']?>">
                  </div>


                </div>
                <button type="submit" class="btn btn-success" style="margin-left:1.5%;">Alterar registro</button>
              </form>
              <button type="button" class="btn btn-danger" style="float:right; margin-right:2.5%;" onclick="fechar_alterar()">Fechar</button>
            </div>
          </div>
    <!-- /.col -->
</div>
  <!-- /.row -->
