<?php
function renderCadastroVacinaModal($selectId = 'vacina')
{
    $selectId = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$selectId);
    ?>
    <div class="modal fade" id="cadastro-nova-vacina" tabindex="-1" role="dialog" aria-labelledby="titulo-cadastro-nova-vacina">
      <div class="modal-dialog" role="document" style="width:440px;max-width:calc(100vw - 32px);margin:10vh auto;">
        <div class="modal-content" style="border:0;border-radius:12px;">
          <form id="form-cadastro-nova-vacina">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="titulo-cadastro-nova-vacina">Cadastrar nova vacina</h4>
            </div>
            <div class="modal-body">
              <div id="erro-cadastro-nova-vacina" class="alert alert-danger" style="display:none;"></div>
              <div class="form-group" style="margin-bottom:0;">
                <label for="nome-nova-vacina">Nome <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nome-nova-vacina" name="nome" maxlength="100" required autocomplete="off">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Cadastrar vacina</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var modal=document.getElementById('cadastro-nova-vacina');
      var form=document.getElementById('form-cadastro-nova-vacina');
      if(!modal||!form)return;
      $('#cadastro-nova-vacina').on('shown.bs.modal',function(){document.getElementById('nome-nova-vacina').focus();});
      $('#cadastro-nova-vacina').on('hidden.bs.modal',function(){form.reset();var erro=document.getElementById('erro-cadastro-nova-vacina');erro.style.display='none';erro.textContent='';});
      form.addEventListener('submit',function(evento){
        evento.preventDefault();
        var botao=form.querySelector('button[type="submit"]'),erro=document.getElementById('erro-cadastro-nova-vacina');
        botao.disabled=true;erro.style.display='none';
        var xhr=new XMLHttpRequest();xhr.open('POST','vacinas/_cadastrar_tipo_vacina.php',true);xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onreadystatechange=function(){if(xhr.readyState!==4)return;botao.disabled=false;try{var resposta=JSON.parse(xhr.responseText);}catch(e){resposta={sucesso:false,mensagem:'Não foi possível cadastrar a vacina.'};}
          if(xhr.status>=200&&xhr.status<300&&resposta.sucesso){var select=document.getElementById(<?=json_encode($selectId)?>),opcao=select.querySelector('option[value="'+resposta.id+'"]');if(!opcao){opcao=document.createElement('option');opcao.value=resposta.id;opcao.textContent=resposta.nome;select.appendChild(opcao);}select.value=String(resposta.id);select.dispatchEvent(new Event('change',{bubbles:true}));$('#cadastro-nova-vacina').modal('hide');}
          else{erro.textContent=resposta.mensagem||'Não foi possível cadastrar a vacina.';erro.style.display='block';}
        };
        xhr.send('nome='+encodeURIComponent(document.getElementById('nome-nova-vacina').value));
      });
    });
    </script>
    <?php
}
