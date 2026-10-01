# Atualização pela Consulta de Rebanho

Acesso: **Atualizar Rebanho**, no menu principal abaixo de **Cadastrar chip**, rota `geral.php?pg=atualizar_rebanho`.

O fluxo exige duas planilhas, de animais vivos e mortos, une seus registros e valida a estrutura e exibe os dados. Consulta nome, FBB, tatuagem e data de nascimento da tabela `animais` no banco configurado para a aplicação, separando os resultados em **Animais já cadastrados**, **Possíveis animais para atualização** e **Animais não cadastrados**. A verificação só é liberada quando os dois arquivos estão selecionados e válidos; o servidor também exige ambos. O upload não altera cadastros. Na tabela de possíveis atualizações, o botão **Atualizar** aplica nome, FBB, tatuagem e nascimento da linha da planilha ao candidato escolhido. Os registros lidos e os candidatos ficam na sessão, vinculados ao banco e ao usuário; não é mantida uma cópia do arquivo. Após atualizar, a página refaz a comparação e mantém a prévia. O botão Limpar descarta a prévia da sessão.

## Formato aceito

Exportação HTML com extensão `.xls`, como `Consulta_rebanho.xls`. Não é um leitor genérico de Excel: XLS binário e XLSX são rejeitados com mensagem. A leitura usa DOM/libxml já disponíveis no container, sem novas dependências.

Limite: 5 MB por arquivo, com envio conjunto de até 10 MB (PHP configurado com post_max_size de 12 MB). Não há limite de quantidade de registros na importação; a exibição continua paginada. Cabeçalhos necessários (podem ser reordenados): FBB, Nome, Nasc., Sexo, Pai, Mãe. As colunas Avós Paternos e Avós Maternos, assim como outras colunas extras, são ignoradas e não entram nos dados guardados na sessão.

A tatuagem é sempre extraída do último conjunto de caracteres do nome, separado por espaços: `BURIA E081` → `E081`; `BURIA KK E007` → `E007`; `BURIA TE 0007` → `0007`. O nome completo é preservado. Mesmo que exista uma coluna Tat., ela não substitui a tatuagem extraída. A tatuagem derivada é usada na comparação e na atualização do cadastro. Prévias do formato anterior são invalidadas; envie a nova planilha.

FBB e tatuagem são textos, preservando letras e zeros à esquerda. FBB é normalizado internamente como FBB/FBE, sem conversão para chip. Sexo, pai e mãe aparecem na prévia para conferência; a atualização continua gravando nome, FBB, tatuagem e nascimento.

As verificações sinalizam campos de identificação vazios, data inválida/futura, sexo desconhecido, quantidade de colunas incorreta e FBB/FBE repetido no arquivo. As linhas com problemas continuam visíveis. “Conferido” indica que as verificações da linha passaram. A tabela em que o animal aparece informa se houve correspondência no banco. A classificação como já cadastrado exige nome, FBB/FBE, tatuagem e nascimento iguais em um mesmo animal do banco. Campos obrigatórios vazios e datas inválidas não confirmam cadastro. Ignora caixa e espaços excedentes, preservando acentos, letras e zeros à esquerda. Na tabela de possíveis atualizações, os valores do banco divergentes da planilha aparecem em vermelho, avaliados separadamente para cada candidato. Sem nenhuma coincidência, dados insuficientes para confirmar o cadastro aparecem em não cadastrados com aviso para revisão. Sem correspondência completa, o FBB/FBE tem prioridade na identificação. Quando ele não é encontrado, nome ou tatuagem iguais (não vazios) sugerem candidatos em possíveis atualizações, permitindo corrigir o FBB após conferência. Cada linha aparece em apenas uma tabela; coincidências completas dos quatro campos têm prioridade. A pesquisa filtra as três tabelas. Não são consultados animais de terceiros.

## Validação

```bash
docker compose exec -T app php tests/consulta_rebanho_test.php
docker compose exec -T app php tests/comparar_rebanho_test.php
```

Para validar também o arquivo de referência (opcional; não versionar dados reais de rebanho):

```bash
docker compose exec -T app php tests/consulta_rebanho_test.php Consulta_rebanho.xls
```

## Atualização individual

O endpoint `animal/_atualizar_importacao.php` aceita somente POST com token de sessão, identificador da prévia, linha da planilha e ID de um candidato exibido. Os valores vêm da sessão, não de campos editáveis enviados pelo navegador. Uma nova prévia ou recarga invalida os botões antigos. Uma linha já aplicada não pode ser reaplicada.

A gravação parametrizada ocorre em transação, conferindo se o cadastro ainda tem os valores exibidos e se o FBB não pertence a outro animal. Campos obrigatórios vazios, datas inválidas/futuras e textos acima dos limites do cadastro (nome: 80, FBB: 20, tatuagem: 15 caracteres) bloqueiam a atualização. Os valores são relidos antes do commit para confirmar a gravação sem conversões ou truncamentos. Linhas já aplicadas ficam indisponíveis para uma segunda atualização na mesma prévia. Falhas desfazem a operação. Outros campos e outros animais não são alterados.

Validação da atualização (a opção MySQL cria uma tabela temporária por conexão e não escreve nos cadastros reais):

```bash
docker compose exec -T app php tests/atualizar_rebanho_test.php --mysql
```

O teste MySQL percorre leitura do XLS, comparação, seleção do candidato, gravação e reconhecimento do cadastro atualizado. Verifica também que a prévia não grava dados e que conflitos e truncamento em modo não estrito fazem rollback. Usa uma tabela temporária com os mesmos limites dos campos do cadastro.

## Cadastro com parentesco

Na aba **Animais não cadastrados**, **Cadastrar** prepara um plano sem gravar dados e abre um modal com todos os animais que serão criados, seus campos e as referências dos pais. **Confirmar cadastro** grava o plano inteiro em uma transação.

A resolução de cada pai/mãe usa o nome completo, ignorando caixa e espaços excedentes: primeiro procura no rebanho, depois em terceiros. Se não encontrar, procura na planilha inteira e resolve os ancestrais recursivamente, cadastrando-os antes dos filhos. Quando não houver cadastro nem linha na planilha, cria um terceiro com nome, sexo correspondente ao vínculo, tatuagem extraída do nome e raça do perfil da fazenda; demais dados ficam não informados. Nomes de pais vazios ficam sem vínculo (ID 0), sem criar terceiros fictícios.

O cadastro preserva o nome completo, FBB, tatuagem, nascimento e sexo. A raça dos novos animais e terceiros vem de `admin.raca`, no perfil da fazenda, e aparece no modal. Perfil sem raça bloqueia o cadastro. Se a raça mudar após abrir o modal, será necessária nova confirmação. Os demais campos seguem os valores vazios/padrão do cadastro de rebanho existente. IDs novos são obtidos após cada inserção; `terceiro_pai` e `terceiro_mae` identificam a origem. Os indicadores de crias de reprodutores/matrizes do rebanho são recalculados na mesma transação.

Nomes ambíguos, ciclos, sexo incompatível, dados inválidos e identificações conflitantes bloqueiam o cadastro. A confirmação usa um plano guardado na sessão, vinculado à prévia; relê e bloqueia os cadastros durante a transação e exige que o plano continue igual ao exibido. Mudanças exigem nova conferência. Falhas desfazem também os parentes e terceiros recém-criados; reenvios não duplicam cadastros.

Teste com cópias temporárias do esquema real (nenhum animal real é alterado; informe o banco do rebanho):

```bash
docker compose exec -T app php tests/cadastrar_importacao_test.php --mysql siste870_buria
node tests/cadastro_previa_test.js
```

O botão **Cadastrar todos** aparece quando a aba **Animais não cadastrados** está ativa. Prepara um único plano com todos os animais dessa aba, independentemente de pesquisa/paginação, compartilhando os pais necessários. O modal apresenta todos os dados antes de confirmar. Linhas inválidas bloqueiam o lote; uma falha desfaz todos os cadastros da operação.

Tatuagens podem se repetir. Somente FBB repetido bloqueia novos cadastros e atualizações. Nomes podem se repetir. Coincidência por nome ou tatuagem sugere possíveis atualizações quando o FBB não é encontrado, sem tornar esses campos únicos.

## Planilhas de vivos e mortos

Envie **Animais vivos** e **Animais mortos** no mesmo formulário. Cada arquivo usa o mesmo cabeçalho e pode ter até 5 MB. Uma das listas pode conter somente o cabeçalho quando não houver animais dessa situação; as duas não podem estar vazias. A prévia de arquivo único foi invalidada e deve ser reenviada no novo formato.

Os registros recebem um identificador interno único na união, preservando arquivo e linha de origem. FBB presente nas duas listas bloqueia a verificação para correção, sem escolher silenciosamente uma situação. Tatuagens repetidas continuam permitidas. A resolução dos pais consulta a união inteira, independentemente da lista de origem.

A coluna **Situação** mostra Vivo (status 0) ou Morto (status 1), conforme o arquivo. Somente o cadastro persiste essa situação. Na atualização, a situação existente é preservada e diferenças nesse campo não classificam o animal como possível atualização. Os demais campos de atualização são mostrados no modal e gravados somente após confirmação. Para nomes comuns, a lista de mortos altera a situação sem preencher causa ou data da morte. Para nomes contendo `morto`, aplica-se a regra de morte no nascimento descrita abaixo.

```bash
docker compose exec -T app php tests/duas_planilhas_test.php
node tests/upload_planilhas_test.js
```

Nomes que contêm `morto` (sem diferenciar maiúsculas/minúsculas) são mantidos e substituídos por `morto1`, `morto2`, `morto3` etc. A sequência começa em 1 e percorre vivos e depois mortos. A tatuagem recebe o mesmo texto, sem espaço. Para esses nomes, a situação é Morto, a causa da perda é Nascimento e a data da morte é igual à data de nascimento, inclusive quando a linha está no arquivo de vivos. Prévias já abertas são adaptadas ao recarregar a página, invalidando a confirmação de cadastro anterior.

Nomes iguais com FBBs diferentes representam animais distintos e podem ser cadastrados, inclusive no mesmo lote ou em listas diferentes. A classificação prioriza o FBB; na ausência de correspondência por FBB, sugere candidatos por nome ou tatuagem. Esses candidatos devem ser revisados antes da correção do FBB. A resolução de pai e mãe ainda depende dos nomes informados na planilha; quando houver mais de um candidato com o mesmo nome, o vínculo fica bloqueado para revisão, sem escolher arbitrariamente um animal.

Para nomes contendo `morto`, o cadastro define a situação Morto; a atualização preserva a situação existente. A regra de causa/data da morte no nascimento continua válida, sem diferenciar caixa. Causa e data são apresentadas na confirmação. Cadastros existentes com causa/data divergentes voltam a aparecer em possíveis atualizações.

## Seleção de campos na atualização

Os modais individual e coletivo abrem com todos os campos selecionados. Cada campo pode ser desmarcado para preservar seu valor atual no banco. É obrigatório selecionar pelo menos um campo; no lote, animais sem campos marcados são ignorados. O servidor aceita apenas campos presentes na proposta guardada na sessão. Atualizações parciais continuam disponíveis para revisar as diferenças restantes após recarregar a prévia.

A coluna Situação não aparece na comparação nem nos modais de atualização. O backend também rejeita tentativas de gravar `status` por esse fluxo; a situação das planilhas é usada apenas no cadastro.

Na revisão coletiva, a lista **Campos que deseja atualizar** controla o lote inteiro, incluindo páginas ainda não visitadas. Desmarcar Nome, FBB, Tatuagem ou Nascimento preserva esse campo em todos os animais. As exceções por animal são preservadas ao alternar os campos e navegar entre páginas.

No cadastro coletivo, inconsistências de dados ignoram apenas o animal afetado e seus dependentes inválidos. A confirmação lista linha, nome e motivo dos ignorados; parentes preparados exclusivamente para uma linha rejeitada são descartados do plano. Os demais animais seguem para confirmação, e o resultado informa a quantidade ignorada. Sem animais válidos, a confirmação fica desabilitada. Falhas de infraestrutura na gravação continuam desfazendo a transação.
