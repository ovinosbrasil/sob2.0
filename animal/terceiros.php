<?php
$id_animal = filter_var($_GET['id_animal'] ?? null, FILTER_VALIDATE_INT);
$registrosTerceiro = $id_animal && $id_animal > 0 ? DBRead('terceiros', "WHERE id = $id_animal") : false;
if (!$registrosTerceiro) {
    echo '<section class="content"><div class="alert alert-warning">Animal de terceiros não encontrado.</div><a class="btn btn-default" href="geral.php?pg=lista_terceiros">Voltar à lista</a></section>';
    return;
}
$dadosTerceiro = $registrosTerceiro[0];
$escaparTerceiro = function ($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); };
$filtroCriasTerceiro = "WHERE (pai = $id_animal AND terceiro_pai = 1) OR (mae = $id_animal AND terceiro_mae = 1)";
$contagem = DBRead('animais', $filtroCriasTerceiro, 'COUNT(*) AS total');
$qtd = (int)($contagem[0]['total'] ?? 0);
$porPagina = filter_var($_GET['por_pagina'] ?? 10, FILTER_VALIDATE_INT);
if (!in_array($porPagina, array(10, 20, 50, 100), true)) { $porPagina = 10; }
$qtd_pag = (int)ceil($qtd / $porPagina);
$paginaInformada = filter_var($_GET['pag'] ?? 0, FILTER_VALIDATE_INT);
$pagina = min(max(0, $paginaInformada === false ? 0 : $paginaInformada), max(0, $qtd_pag - 1));
$loop = $pagina * $porPagina;
$criasTerceiro = $qtd ? (DBRead('animais', "$filtroCriasTerceiro ORDER BY data_de_nascimento DESC, id DESC LIMIT $loop,$porPagina", 'id, nome, data_de_nascimento, sexo, tipo, status') ?: array()) : array();
$urlPagina = 'geral.php?pg=terceiro&amp;id_animal=' . $id_animal . '&amp;por_pagina=' . $porPagina;
$racasTerceiro = DBRead('raca', 'ORDER BY nome ASC', 'nome') ?: array();
$statusTerceiro = array(0 => array('Rebanho', 'text-info'), 1 => array('Morto', 'text-danger'), 2 => array('Vendido', 'text-success'), 3 => array('Empréstimo', 'text-warning'), 4 => array('Doação', 'text-warning'), 5 => array('Abate', 'text-danger'));
?>
<section class="content-header">
  <h1><?=$escaparTerceiro($dadosTerceiro['nome'])?> <small>Terceiros<?=empty($dadosTerceiro['ativo']) ? ' · Inativo' : ''?></small></h1>
  <ol class="breadcrumb">
    <li><a href="geral.php?pg=lista_terceiros"><i class="fa fa-github-alt"></i> Terceiros</a></li>
    <li class="active">Dados do animal</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box" style="border-top:0;">
        <div class="box-header with-border"><h3 class="box-title">Dados do animal</h3></div>
        <form action="animal/_alterar_terceiro.php?id_animal=<?=$id_animal?>" method="post">
          <div class="box-body">
            <div class="row">
              <div class="form-group col-sm-6 col-md-4">
                <label for="nome_animal">Nome <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nome_animal" name="nome_animal" value="<?=$escaparTerceiro($dadosTerceiro['nome'])?>" required>
              </div>
              <div class="form-group col-sm-6 col-md-4">
                <label for="fbb">FBB</label>
                <input type="text" class="form-control" id="fbb" name="fbb" value="<?=$escaparTerceiro($dadosTerceiro['fbb'] ?? '')?>">
              </div>
              <div class="form-group col-sm-6 col-md-4">
                <label for="sexo">Sexo <span class="text-danger">*</span></label>
                <select class="form-control" id="sexo" name="sexo" required>
                  <option value="">Selecione</option>
                  <?php foreach (array('Macho', 'Fêmea') as $sexoTerceiro): ?>
                  <option value="<?=$sexoTerceiro?>" <?=$dadosTerceiro['sexo'] === $sexoTerceiro ? 'selected' : ''?>><?=$sexoTerceiro?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="row">
              <?php foreach (array('pai' => 'Pai', 'mae' => 'Mãe') as $campoTerceiro => $labelTerceiro): ?>
              <div class="form-group col-sm-6 col-md-4">
                <label for="<?=$campoTerceiro?>"><?=$labelTerceiro?></label>
                <input type="text" class="form-control" id="<?=$campoTerceiro?>" name="<?=$campoTerceiro?>" value="<?=$escaparTerceiro($dadosTerceiro[$campoTerceiro] ?? '')?>">
              </div>
              <?php endforeach; ?>
              <div class="form-group col-sm-6 col-md-4">
                <label for="raca">Raça <span class="text-danger">*</span></label>
                <select class="form-control" id="raca" name="raca" required>
                  <option value="">Selecione</option>
                  <?php $nomesRacasTerceiro = array_column($racasTerceiro, 'nome');
                  if (!empty($dadosTerceiro['raca']) && !in_array($dadosTerceiro['raca'], $nomesRacasTerceiro, true)) { array_unshift($nomesRacasTerceiro, $dadosTerceiro['raca']); }
                  foreach (array_unique($nomesRacasTerceiro) as $racaTerceiro): ?>
                  <option value="<?=$escaparTerceiro($racaTerceiro)?>" <?=$dadosTerceiro['raca'] === $racaTerceiro ? 'selected' : ''?>><?=$escaparTerceiro($racaTerceiro)?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="box-footer" style="display:flex; flex-wrap:wrap; gap:8px; justify-content:flex-end;">
            <?php if (empty($dadosTerceiro['ativo'])): ?>
            <button type="button" class="btn btn-success" onclick="ativar_terceiro(<?=$id_animal?>)">Ativar animal</button>
            <?php endif; ?>
            <button type="button" class="btn btn-danger" onclick="excluir_terceiro(<?=$id_animal?>)"><i class="fa fa-trash-o" aria-hidden="true"></i> Excluir animal</button>
            <button type="submit" class="btn btn-warning">Salvar alterações</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-12" id="crias-terceiro">
      <div class="box" style="border-top:0;">
        <div class="box-header with-border"><h3 class="box-title">Lista de crias</h3></div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead><tr><th style="width:1%;">Nº</th><th>Animal</th><th>Data de nascimento</th><th>Sexo</th><th>Tipo</th><th>Status</th><th style="width:1%;"><span class="sr-only">Ações</span></th></tr></thead>
              <tbody>
                <?php if (!$criasTerceiro): ?>
                <tr><td colspan="7" class="text-center">Nenhuma cria encontrada para este animal.</td></tr>
                <?php endif; ?>
                <?php foreach ($criasTerceiro as $indiceTerceiro => $criaTerceiro):
                  $dataTerceiro = DateTimeImmutable::createFromFormat('!Y-m-d', substr((string)$criaTerceiro['data_de_nascimento'], 0, 10));
                  $nascimentoTerceiro = $dataTerceiro && $dataTerceiro->format('Y-m-d') === substr((string)$criaTerceiro['data_de_nascimento'], 0, 10) ? $dataTerceiro->format('d/m/Y') : '--';
                  $situacaoTerceiro = $statusTerceiro[(int)$criaTerceiro['status']] ?? array('Não informado', 'text-muted'); ?>
                <tr>
                  <td><?=$loop + $indiceTerceiro + 1?></td>
                  <td><a href="geral.php?pg=animal&amp;id_animal=<?=(int)$criaTerceiro['id']?>"><?=$escaparTerceiro(trim((string)$criaTerceiro['nome']) !== '' ? $criaTerceiro['nome'] : '--')?></a></td>
                  <td><?=$nascimentoTerceiro?></td>
                  <td><?=$escaparTerceiro($criaTerceiro['sexo'])?></td>
                  <td><?=$escaparTerceiro($criaTerceiro['tipo'])?></td>
                  <td><span class="<?=$situacaoTerceiro[1]?>"><?=$situacaoTerceiro[0]?></span></td>
                  <td><a class="text-primary" href="geral.php?pg=animal&amp;id_animal=<?=(int)$criaTerceiro['id']?>" title="Abrir dados da cria" aria-label="Abrir dados da cria"><i class="fa fa-search" aria-hidden="true"></i></a></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="box-footer" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:16px;">
            <form action="geral.php" method="get" style="display:flex; align-items:center; gap:8px; margin:0;">
              <input type="hidden" name="pg" value="terceiro">
              <input type="hidden" name="id_animal" value="<?=$id_animal?>">
              <label for="por-pagina" style="margin:0; font-weight:normal;">Por página</label>
              <select id="por-pagina" name="por_pagina" class="form-control input-sm" style="width:auto;" onchange="this.form.submit()">
                <?php foreach (array(10, 20, 50, 100) as $quantidade): ?>
                <option value="<?=$quantidade?>" <?=$porPagina === $quantidade ? 'selected' : ''?>><?=$quantidade?></option>
                <?php endforeach; ?>
              </select>
              <noscript><button type="submit" class="btn btn-default btn-sm">Aplicar</button></noscript>
            </form>
            <span class="text-muted">
              Exibindo <?=$qtd ? $loop + 1 : 0?> a <?=min($loop + $porPagina, $qtd)?> de <?=$qtd?> crias
            </span>
            <?php if ($qtd_pag > 1):
                $paginasVisiveis = array(0, $qtd_pag - 1);
                $inicioPaginas = max(0, min($pagina - 1, $qtd_pag - 3));
                $fimPaginas = min($qtd_pag - 1, max($pagina + 1, 2));
                for ($numero = $inicioPaginas; $numero <= $fimPaginas; $numero++) {
                    $paginasVisiveis[] = $numero;
                }
                $paginasVisiveis = array_unique($paginasVisiveis);
                sort($paginasVisiveis);
                $anterior = -1;
            ?>
            <nav aria-label="Paginação dos crias de terceiros">
              <ul class="pagination pagination-sm no-margin">
                <?php if ($pagina > 0): ?>
                <li><a href="<?=$urlPagina?>&amp;pag=<?=$pagina-1?>#crias-terceiro" aria-label="Página anterior" title="Página anterior" rel="prev">&laquo;</a></li>
                <?php else: ?>
                <li class="disabled"><span aria-label="Página anterior" aria-disabled="true">&laquo;</span></li>
                <?php endif; ?>
                <?php foreach ($paginasVisiveis as $numero): ?>
                  <?php if ($anterior >= 0 && $numero > $anterior + 1): ?>
                  <li class="disabled"><span aria-hidden="true">&hellip;</span></li>
                  <?php endif; ?>
                  <?php if ($numero === $pagina): ?>
                  <li class="active"><span aria-current="page" aria-label="Página <?=$numero+1?>"><?=$numero+1?></span></li>
                  <?php else: ?>
                  <li><a href="<?=$urlPagina?>&amp;pag=<?=$numero?>#crias-terceiro" aria-label="Página <?=$numero+1?>"><?=$numero+1?></a></li>
                  <?php endif; ?>
                <?php $anterior = $numero; endforeach; ?>
                <?php if ($pagina < $qtd_pag - 1): ?>
                <li><a href="<?=$urlPagina?>&amp;pag=<?=$pagina+1?>#crias-terceiro" aria-label="Próxima página" title="Próxima página" rel="next">&raquo;</a></li>
                <?php else: ?>
                <li class="disabled"><span aria-label="Próxima página" aria-disabled="true">&raquo;</span></li>
                <?php endif; ?>
              </ul>
            </nav>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
