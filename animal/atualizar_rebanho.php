<?php
require_once __DIR__ . '/importacao/leitor_consulta_rebanho.php';
require_once __DIR__ . '/importacao/comparar_rebanho.php';
require_once __DIR__ . '/importacao/atualizar_animal.php';
require_once __DIR__ . '/importacao/paginar_previa.php';
function hPreviaRebanho($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
$prefixoMortosPrevia = mb_strtoupper(textoConsultaRebanho($user[0]['prefixo'] ?? ''), 'UTF-8');
$padraoMortosPrevia = 'prefixo_maiusculo:' . $prefixoMortosPrevia;
if (isset($_SESSION['previa_rebanho']) && ($_SESSION['previa_rebanho']['versao'] ?? 0) !== 3) {
    unset($_SESSION['previa_rebanho']);
}
if (isset($_SESSION['previa_rebanho']['registros']) && ($_SESSION['previa_rebanho']['nomes_mortos_numerados'] ?? null) !== $padraoMortosPrevia) {
    $_SESSION['previa_rebanho']['registros'] = numerarNomesMortosPrevia($_SESSION['previa_rebanho']['registros'], $prefixoMortosPrevia);
    $_SESSION['previa_rebanho']['nomes_mortos_numerados'] = $padraoMortosPrevia;
    unset($_SESSION['previa_rebanho']['cadastro_pendente'], $_SESSION['previa_rebanho']['atualizados']);
}
$registrosPrevia = array();
$gruposPrevia = array('cadastrados' => array(), 'possiveis_atualizacoes' => array(), 'nao_cadastrados' => array());
$erroPrevia = '';
$nomeArquivoPrevia = '';
$mensagemPrevia = $_SESSION['mensagem_previa_rebanho'] ?? null;
unset($_SESSION['mensagem_previa_rebanho']);
$envioPrevia = '';
if (empty($_SESSION['token_previa_rebanho'])) {
    $_SESSION['token_previa_rebanho'] = bin2hex(random_bytes(32));
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    try {
        if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            throw new RuntimeException('O envio excedeu o limite do servidor. Selecione duas planilhas de até 5 MB cada.');
        }
        $token = $_POST['token_previa'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['token_previa_rebanho'], $token)) {
            throw new RuntimeException('A sessão de envio expirou. Selecione o arquivo e tente novamente.');
        }
        if (($_POST['acao'] ?? '') === 'limpar') {
            unset($_SESSION['previa_rebanho']);
            header('Location: geral.php?pg=atualizar_rebanho', true, 303);
            exit;
        }
        $importacao = lerArquivosRebanho($_FILES, $prefixoMortosPrevia);
        $registrosLidos = $importacao['registros'];
        $gruposPrevia = compararAnimaisPrevia($registrosLidos, consultarAnimaisPrevia());
        $registrosPrevia = array_merge($gruposPrevia['cadastrados'], $gruposPrevia['possiveis_atualizacoes'], $gruposPrevia['nao_cadastrados']);
        $nomeArquivoPrevia = 'Vivos: ' . $importacao['arquivos']['vivos'] . ' | Mortos: ' . $importacao['arquivos']['mortos'];
        $envioPrevia = bin2hex(random_bytes(16));
        $_SESSION['previa_rebanho'] = array(
            'versao' => 3, 'nomes_mortos_numerados' => $padraoMortosPrevia, 'envio' => $envioPrevia, 'banco' => DB_DATABASE, 'login' => $_SESSION['login'],
            'registros' => $registrosLidos, 'possiveis' => $gruposPrevia['possiveis_atualizacoes'],
            'arquivo' => $nomeArquivoPrevia, 'atualizados' => array()
        );
    } catch (RuntimeException $erro) {
        $erroPrevia = $erro->getMessage();
    }
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && isset($_SESSION['previa_rebanho'])) {
    $previaSalva = $_SESSION['previa_rebanho'];
    if (($previaSalva['banco'] ?? '') === DB_DATABASE && ($previaSalva['login'] ?? '') === $_SESSION['login']) {
        try {
            $gruposPrevia = compararAnimaisPrevia($previaSalva['registros'], consultarAnimaisPrevia());
            $registrosPrevia = array_merge($gruposPrevia['cadastrados'], $gruposPrevia['possiveis_atualizacoes'], $gruposPrevia['nao_cadastrados']);
            $nomeArquivoPrevia = $previaSalva['arquivo'];
            $envioPrevia = bin2hex(random_bytes(16));
            $_SESSION['previa_rebanho']['envio'] = $envioPrevia;
            $_SESSION['previa_rebanho']['possiveis'] = $gruposPrevia['possiveis_atualizacoes'];
        } catch (RuntimeException $erro) { $erroPrevia = $erro->getMessage(); }
    } else { unset($_SESSION['previa_rebanho']); }
}
$lotePrevia = $envioPrevia !== '' ? atualizacoesEmLotePrevia($_SESSION['previa_rebanho']) : array();
$linhasRevisao = count(array_filter($registrosPrevia, function ($registro) { return !empty($registro['problemas']); }));
$pesquisaPrevia = is_string($_GET['pesquisa'] ?? null) ? mb_substr($_GET['pesquisa'], 0, 200) : '';
$abaInicial = ($_GET['aba'] ?? '') === 'nao_cadastrados' ? 'nao_cadastrados' : 'possiveis_atualizacoes';
if (!isset($_GET['aba']) && !$gruposPrevia['possiveis_atualizacoes']) $abaInicial = 'nao_cadastrados';
$totalNovosPrevia = count($gruposPrevia['nao_cadastrados']);
$paginacoesPrevia = array();
foreach (array('possiveis_atualizacoes', 'nao_cadastrados') as $grupoPagina) {
    $paginacoesPrevia[$grupoPagina] = paginarRegistrosPrevia($gruposPrevia[$grupoPagina], $pesquisaPrevia, $_GET['pagina'] ?? 1, $_GET['limite'] ?? 10);
    $gruposPrevia[$grupoPagina] = $paginacoesPrevia[$grupoPagina]['registros'];
}
$urlPrevia = function ($aba, $pagina = 1, $limite = null) use ($pesquisaPrevia, $paginacoesPrevia) {
    return 'geral.php?' . http_build_query(array('pg'=>'atualizar_rebanho','aba'=>$aba,'pesquisa'=>$pesquisaPrevia,'pagina'=>$pagina,'limite'=>$limite ?? $paginacoesPrevia[$aba]['limite']));
};
?>
<section class="content-header">
  <h1>Atualizar Rebanho</h1>
  <ol class="breadcrumb"><li><a href="geral.php"><i class="fa fa-home"></i> Início</a></li><li class="active">Atualizar Rebanho</li></ol>
</section>
<section class="content">
  <?php if ($mensagemPrevia): ?><div class="alert alert-<?=$mensagemPrevia['tipo'] === 'success' ? 'success' : 'danger'?>" role="status"><?=hPreviaRebanho($mensagemPrevia['texto'])?></div><?php endif; ?>
  <?php if ($erroPrevia !== ''): ?><div class="alert alert-danger" role="alert"><?=hPreviaRebanho($erroPrevia)?></div><?php endif; ?>
  <div class="box" style="border-top:0;">
    <div class="box-header with-border"><h3 class="box-title">Enviar planilhas do rebanho</h3></div>
    <div class="box-body">
      <p>Envie as planilhas de animais vivos e mortos. As duas serão unidas para conferir e cadastrar o rebanho, incluindo os vínculos de parentesco.</p>
      <form action="geral.php?pg=atualizar_rebanho" method="post" enctype="multipart/form-data">
        <input type="hidden" name="token_previa" value="<?=hPreviaRebanho($_SESSION['token_previa_rebanho'])?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
        <div class="row" style="display:flex; flex-wrap:wrap; align-items:flex-end;">
          <?php foreach (array('vivos' => 'Animais vivos', 'mortos' => 'Animais mortos') as $tipoArquivo => $rotuloArquivo): ?>
          <div class="col-sm-6"><div class="form-group">
            <label for="planilha-<?=$tipoArquivo?>"><?=$rotuloArquivo?><span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="planilha-<?=$tipoArquivo?>" name="planilha_<?=$tipoArquivo?>" accept=".xls" aria-describedby="ajuda-planilhas-rebanho" required>
          </div></div>
          <?php endforeach; ?>
          <div class="col-sm-12"><p class="help-block" id="ajuda-planilhas-rebanho">Selecione os dois arquivos para liberar a verificação. Exportações .xls com FBB, Nome, Nasc., Sexo, Pai e Mãe, até 5 MB por arquivo e sem limite de quantidade de animais. A lista de mortos define a situação Morto. Uma lista sem animais pode conter somente o cabeçalho.</p></div>
          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-default" name="acao" value="limpar" formnovalidate>Limpar</button>
            <button type="submit" id="verificar-planilhas" class="btn btn-success" disabled><i class="fa fa-upload" aria-hidden="true"></i> Verificar arquivos</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="box" style="border-top:0;">
    <div class="box-header with-border">
      <h3 class="box-title">Prévia dos animais</h3>
      <button type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#dicas-previa-rebanho" aria-label="Dicas sobre a prévia dos animais" title="Dicas">
        <i class="fa fa-question-circle" aria-hidden="true"></i> Dicas
      </button>
      <?php $abaCadastroInicial = $abaInicial === 'nao_cadastrados'; ?>
      <button type="button" id="atualizar-todos-previa" class="btn btn-primary pull-right" data-toggle="modal" data-target="#confirmar-atualizacao-todos" <?=$abaInicial === 'possiveis_atualizacoes' ? '' : 'hidden'?> <?=$lotePrevia ? '' : 'disabled'?>>Atualizar todos</button>
      <button type="button" id="cadastrar-todos-previa" class="btn btn-primary pull-right" data-cadastrar-previa="todos" <?=$abaCadastroInicial ? '' : 'hidden'?> <?=$totalNovosPrevia ? '' : 'disabled'?>>Cadastrar todos</button>
    </div>
    <div class="box-body">
      <?php if ($registrosPrevia): ?>
        <p><strong>Arquivos:</strong> <?=hPreviaRebanho($nomeArquivoPrevia)?> — <strong><?=count($registrosPrevia)?></strong> animais;
          <strong><?=count($registrosPrevia) - $linhasRevisao?></strong> sem inconsistências nas verificações desta prévia;
          <strong><?=$linhasRevisao?></strong> para revisão.
          <strong><?=count($gruposPrevia['cadastrados'])?></strong> já cadastrados sem alterações, omitidos das tabelas.</p>
        <form method="get" class="form-inline" style="margin-bottom:15px;">
          <input type="hidden" name="pg" value="atualizar_rebanho">
          <input type="hidden" name="aba" value="<?=hPreviaRebanho($abaInicial)?>">
          <input type="hidden" name="limite" value="<?=$paginacoesPrevia[$abaInicial]['limite']?>">
          <label for="pesquisa-previa-rebanho">Pesquisar na prévia</label>
          <input type="search" name="pesquisa" maxlength="200" class="form-control" id="pesquisa-previa-rebanho" value="<?=hPreviaRebanho($pesquisaPrevia)?>" placeholder="Nome, FBB/FBE ou tatuagem">
          <button class="btn btn-primary" type="submit">Pesquisar</button>
        </form>
        <p class="text-muted"><?=array_sum(array_column($paginacoesPrevia, 'total'))?> animais encontrados nas duas abas</p>
        <?php $abasPrevia = array('possiveis_atualizacoes'=>'Possíveis atualizações', 'nao_cadastrados'=>'Animais não cadastrados'); ?>
        <ul class="nav nav-tabs" role="tablist" aria-label="Grupos de animais da prévia">
          <?php foreach ($abasPrevia as $grupo => $titulo): ?>
          <li role="presentation" class="<?=$grupo === $abaInicial ? 'active' : ''?>">
            <a href="<?=hPreviaRebanho($urlPrevia($grupo))?>" id="tab-previa-<?=$grupo?>" role="tab" aria-controls="aba-previa-<?=$grupo?>" aria-expanded="<?=$grupo === $abaInicial ? 'true' : 'false'?>">
              <?=hPreviaRebanho($titulo)?> <span class="badge" id="contagem-aba-<?=$grupo?>"><?=$paginacoesPrevia[$grupo]['total']?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
        <div class="tab-content" style="padding-top:15px;">
        <?php foreach ($abasPrevia as $grupo => $titulo): ?>
        <div class="tab-pane <?=$grupo === $abaInicial ? 'active' : ''?>" id="aba-previa-<?=$grupo?>" role="tabpanel" aria-labelledby="tab-previa-<?=$grupo?>">
        <?php if ($grupo === 'possiveis_atualizacoes'): ?><p class="help-block">FBB/FBE, nome ou tatuagem encontrado no banco, com diferenças nos dados. O FBB tem prioridade; na ausência de coincidência por FBB, confira os candidatos por nome ou tatuagem para atualizar o registro correto. Os valores em vermelho no banco divergem da planilha. Revise os dados antes de atualizar.</p><?php endif; ?>
        <?php if ($grupo === 'nao_cadastrados'): ?><p class="help-block">Nenhum cadastro encontrado pelo FBB/FBE, nome ou tatuagem. Linhas com dados insuficientes ou inválidos precisam de revisão antes de confirmar que o animal não está cadastrado.</p><?php endif; ?>
        <?php if ($grupo === 'possiveis_atualizacoes'): ?>
          <?php require __DIR__ . '/importacao/tabela_possiveis_atualizacoes.php'; ?>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="tabela-previa-<?=$grupo?>" data-tabela-previa>
            <caption class="sr-only"><?=hPreviaRebanho($titulo)?></caption>
            <thead><tr><?php foreach (colunasConsultaRebanho() as $coluna): ?><th><?=hPreviaRebanho($coluna)?></th><?php endforeach; ?><th>Verificação</th><?php if ($grupo === 'nao_cadastrados'): ?><th>Ação</th><?php endif; ?></tr></thead>
            <tbody>
            <?php foreach ($gruposPrevia[$grupo] as $registro): ?>
              <tr data-registro-previa data-pesquisa="<?=hPreviaRebanho($registro['dados']['Nome'] . ' ' . $registro['dados']['FBB/FBE'] . ' ' . $registro['dados']['Tat.'])?>" class="<?=$registro['problemas'] ? 'warning' : ''?>">
                <?php foreach (colunasConsultaRebanho() as $coluna): ?><td><?=hPreviaRebanho($registro['dados'][$coluna] ?? '')?></td><?php endforeach; ?>
                <td><?php if (!empty($registro['correspondencia'])): ?><div class="text-muted"><?=hPreviaRebanho($registro['correspondencia'])?></div><?php endif; ?><?php if ($registro['problemas']): ?><span class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Revisar</span><ul><?php foreach ($registro['problemas'] as $problema): ?><li><?=hPreviaRebanho($problema)?></li><?php endforeach; ?></ul><?php else: ?><span class="text-success"><i class="fa fa-check" aria-hidden="true"></i> Conferido</span><?php endif; ?></td>
                <?php if ($grupo === 'nao_cadastrados'): ?>
                <td><button type="button" class="btn btn-success btn-sm" data-cadastrar-previa="<?=(int)$registro['linha']?>">Cadastrar</button></td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
              <tr data-sem-resultados <?=$gruposPrevia[$grupo] ? 'hidden' : ''?>><td colspan="<?=count(colunasConsultaRebanho()) + ($grupo === 'nao_cadastrados' ? 2 : 1)?>" class="text-center">Nenhum animal nesta tabela.</td></tr>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
        <?php $paginacao = $paginacoesPrevia[$grupo]; require __DIR__ . '/importacao/rodape_paginacao.php'; ?>
        </div>
        <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-center text-muted">Envie uma planilha para visualizar os animais e as verificações.</p>
      <?php endif; ?>
    </div>
  </div>
</section>


<?php require __DIR__ . '/importacao/confirmar_atualizacao_todos.php'; ?>

<div class="modal fade" id="dicas-previa-rebanho" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="titulo-dicas-previa-rebanho">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulo-dicas-previa-rebanho">Dicas sobre a prévia dos animais</h4>
      </div>
      <div class="modal-body">
        <p>Confira os dados antes de atualizar. O botão <strong>Atualizar</strong> substitui nome, FBB, tatuagem e nascimento do cadastro selecionado pelos valores da planilha, após sua confirmação.</p>
        <ul>
          <li><strong>FBB/FBE:</strong> é o registro da planilha, não o chip.</li>
          <li><strong>Tatuagem:</strong> é extraída do último trecho do nome. Exemplo: BURIA E081 → E081.</li>
          <li><strong>Avós:</strong> as colunas de avós são ignoradas.</li>
          <li><strong>Sexo, pai e mãe:</strong> são exibidos para conferência. A atualização grava nome, FBB, tatuagem e nascimento.</li>
          <li><strong>Animais já cadastrados:</strong> têm nome, FBB/FBE, tatuagem e nascimento iguais no mesmo cadastro e são omitidos das tabelas quando não há alterações.</li>
          <li><strong>Possíveis atualizações:</strong> têm FBB/FBE coincidente ou, quando o FBB não é encontrado, nome ou tatuagem iguais. Isso permite corrigir ou preencher o FBB do cadastro escolhido. Nomes e tatuagens continuam podendo se repetir.</li>
          <li><strong>Comparação:</strong> diferenças de maiúsculas/minúsculas e espaços excedentes são desconsideradas; letras e zeros dos registros são preservados.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/importacao/modal_cadastro.php'; ?>

<style>
#atualizar-todos-previa[hidden],
#cadastrar-todos-previa[hidden] {
  display: none !important;
}
</style>

<script src="animal/importacao/upload_planilhas.js?v=<?=filemtime(__DIR__ . '/importacao/upload_planilhas.js')?>"></script>
