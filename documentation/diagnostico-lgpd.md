# Diagnóstico e plano de adequação à LGPD — SOB

Data: 20/10/2026.

## Conclusão e alcance

O código apresenta riscos críticos de acesso indevido, troca de senha e isolamento entre fazendas. A prioridade é corrigir esses controles, em paralelo à organização dos processos de privacidade. Uma política de privacidade, isoladamente, não resolve os problemas encontrados.

Foi feito um levantamento estático dos 363 arquivos PHP próprios (31.145 linhas), com buscas transversais e leitura dirigida dos fluxos de autenticação, configuração, banco, perfil, compradores, financeiro, relatórios, importação e manutenção. Foram examinadas também configurações locais, dependências declaradas, rotas e migrações relevantes. Isso não equivale à revisão manual de cada linha de todas as bibliotecas, nem a um teste de invasão.

O repositório contém migrações parciais, mas não o esquema completo nem os dados iniciais. Não foram acessados bancos reais, hospedagem, contratos, backups ou logs de produção. Os achados abaixo são evidências do código local; exposição e impacto em produção precisam ser confirmados. Não há evidência suficiente para afirmar que ocorreu vazamento.

Já existiam alterações locais em `_config.php` e `docker/php/local.ini` antes desta análise. O diagnóstico considera o estado atual desses arquivos, sem atribuir essas mudanças ao código publicado. Nenhum código funcional foi alterado nesta análise.

## 1. Dados e fluxos identificados

| Contexto | Dados identificados no código | Persistência ou saída |
| --- | --- | --- |
| Usuários e interessados | Nome, e-mail, celular, login, senha, último acesso, vínculo com banco e validade de acesso | `user`, `_cadastro.php`, `_logar.php` |
| Responsável pela fazenda | Nome, CPF/CNPJ, telefones, e-mail, endereço, cidade, estado, CEP, fazenda e códigos de criador | `admin`, `perfil/_alterar.php`, `perfil/perfil.php` |
| Técnico responsável | Nome e código profissional/cadastral | `admin`, perfil e relatório ARCO |
| Compradores | Nome, CPF, e-mail, celulares, cidade, estado e código de criador | `mercado`, módulo `vendas/` |
| Transações vinculadas a pessoas | Comprador, valores, parcelas, vencimentos, pagamentos e observações | `vendas`, `venda_semen`, `venda_embriao`, `controle_financeiro` |
| Documentos e compartilhamento | Dados do criador, endereço, contatos e técnico em PDF | `reproducao/arco/_relatorio_arco_pdf.php` |
| Dados operacionais | Animais, genealogia, reprodução, pesagem, chip, vacinação, exposições e importações | Módulos operacionais; avaliar vínculos com pessoas e campos livres |
| Dados técnicos | Sessões, mensagens de erro; possíveis URLs e identificadores em logs da hospedagem | PHP/servidor; conteúdo e configuração de produção não verificados |

Os dados veterinários e genéticos encontrados são de animais. Isso, por si só, não os torna dados pessoais sensíveis de pessoas. CPF é dado pessoal, mas não integra automaticamente a categoria legal de dado sensível. Os vínculos com pessoas naturais continuam exigindo avaliação de privacidade. Referência: definições do art. 5º da [LGPD](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709compilado.htm).

O nome `terceiros` no banco designa também animais de terceiros; não deve ser interpretado automaticamente como cadastro de pessoas.

## 2. Correções técnicas prioritárias

P0 = corrigir imediatamente se o fluxo estiver disponível em produção. P1 = próximo ciclo. P2 = consolidação e manutenção. A classificação é técnica, não uma conclusão de infração administrativa.

### P0 — Troca de senha permite escolher a conta pela URL

Atualização: o endpoint `perfil/_alterar2.php` foi removido a pedido do responsável, pois a funcionalidade já havia sido retirada da interface. O achado abaixo registra o estado anterior; confirmar a remoção do arquivo também na implantação em produção. As demais recomendações de autenticação continuam pendentes.

Evidência: `perfil/_alterar2.php:13–19` lê `login` e `senha` de GET e atualiza `user` após apenas verificar a existência de uma sessão. Não vincula a conta alterada ao usuário autenticado nem exige senha atual ou permissão administrativa.

Risco: um usuário autenticado pode tentar alterar a senha de outra conta; senhas na URL podem aparecer em histórico e logs.

Fazer: bloquear o fluxo atual até substituí-lo por POST, CSRF, identificação da conta pela sessão, confirmação da senha atual e hash. Uma função administrativa de redefinição deve ter permissão específica e auditoria. Revisar registros de acesso a essa rota com acesso restrito, sem copiar senhas para chamados.

Aceite: usuário A não altera B; GET não modifica registros; senha não aparece na URL nem nos logs; redefinição invalida sessões anteriores.

### P0 — Autenticação e armazenamento de senhas

Evidência: `_logar.php:17–19` interpola o login em SQL e compara a senha recebida diretamente com o valor armazenado; `perfil/_alterar2.php` grava a senha diretamente. Não foi encontrado uso de `password_hash` ou `password_verify` no PHP próprio.

Fazer: consulta preparada, `password_hash`/`password_verify`, política de redefinição e migração das credenciais antigas. Verificar todas as colunas de senha: a migração `021_equalizar_santaangelina_buria.sql:26` ainda define `admin.senha` com 12 caracteres. Usar coluna adequada ao hash, por exemplo VARCHAR(255), e eliminar cópias legadas após avaliar seu uso. Implementar limitação de tentativas e respostas que não revelem se o login existe. Considerar MFA para administradores.

A interface aponta para `_esqueceu_senha.php`, ausente nesta cópia. Implementar ou remover esse caminho; uma recuperação deve usar token aleatório, de uso único, com validade e armazenamento seguro.

Aceite: novas senhas e redefinições só persistem hashes; contas antigas têm migração definida; tentativas repetidas são limitadas; recuperação não entrega a senha anterior.

### P0 — Isolamento entre fazendas

Evidência: `_logar.php:22` carrega o banco autorizado para a sessão, mas `_config.php:10` fixa outro banco para os módulos. O README descreve bancos distintos por usuário. Há ainda scripts antigos com banco fixo em `funcoes_banco/`.

Risco: se esse estado for publicado em uma instalação com múltiplos clientes, usuários podem operar no mesmo banco independentemente de seu vínculo. Pode ser uma configuração local temporária; produção não foi verificada.

Fazer: resolver o cliente por vínculo confiável no servidor, validar contra uma lista de bancos permitidos e rejeitar vínculos inválidos. Separar conexão de autenticação da conexão de dados da fazenda. Remover seleções fixas das rotas operacionais e limitar privilégios no MySQL.

Aceite: com duas fazendas de teste, A não consulta, altera, pesquisa ou exporta B, incluindo endpoints diretos e IDs manipulados. Validar isso antes de permitir exportações de dados pessoais.

### P0 — SQL construído com entradas e atualizações sem filtro

Evidência: `mysqli/_database.php:41–43` aceita fragmentos SQL; `:58–65` interpola valores de atualização sem escape próprio. Exemplos de entradas diretas: `_logar.php:17`, `animal/_pesquisar_animal.php` e `chip/animal/_cadastrar.php`. `perfil/_alterar.php` chama `DBUpdate('admin', $dados)` sem WHERE, alterando todas as linhas da tabela.

Fazer: migrar entradas para consultas preparadas, permitir apenas identificadores de tabela/coluna internos e impedir UPDATE/DELETE sem condição nas operações comuns. Vincular alterações de perfil ao registro autorizado. Substituir apóstrofos por aspas não é uma defesa suficiente. O escape existente em `DBCreate` não protege automaticamente os outros métodos.

Aceite: entradas com aspas são armazenadas ou pesquisadas como dados; não modificam a consulta. Atualização de perfil afeta apenas o registro autorizado. Cobrir autenticação, compradores, financeiro e operações em lote.

### P0/P1 — Segredos e scripts administrativos na árvore pública

Evidência: credenciais padrão estão embutidas em `mysqli/environment.php`, `mysqli/_config.php` e scripts antigos de `funcoes_banco/`. `info.php` executa `phpinfo()`. `funcoes_banco/atualizar_tipo.php` abre conexão e processa registros sem passar pelo controle de sessão. Alguns scripts dependem de `mysql_*`, incompatível com o PHP local, o que não substitui controle de acesso.

Fazer: retirar diagnóstico e manutenção do diretório público, executar migrações por CLI autorizada e remover segredos do código. Se credenciais embutidas forem reais ou reutilizadas, rotacioná-las e revisar permissões. Tratar também o histórico Git. A aplicação deve falhar em produção quando faltar configuração obrigatória.

O Apache local já bloqueia arquivos ocultos, SQL, `error_log` e `docker/`; não há bloqueio específico de `info.php` ou de todos os scripts de manutenção. Confirmar regras equivalentes no servidor real.

### P1 — Sessões e permissões

Evidência: `_config.php` exige login e banco, mas não verifica uma permissão por ação. `_logar.php` não renova o identificador da sessão; `_logout.php` apenas remove `login`. Não foi localizada configuração explícita de cookies seguros ou expiração por inatividade no código próprio; o servidor pode ter configurações adicionais.

Fazer: sessão central com renovação após autenticação, cookies Secure em HTTPS, HttpOnly e SameSite apropriado, tempo de inatividade e limite absoluto. Destruir sessão e cookie no logout. Criar contas individuais e permissões para proprietário, gestor, financeiro, técnico, consulta e suporte, conforme a operação real. Validar permissão no backend em cada rota, inclusive PDFs, buscas e ações administrativas. Prever revogação de acesso de funcionários e suporte temporário com registro.

Aceite: acesso direto obedece às mesmas regras da tela; usuário de consulta não grava; técnico sem permissão financeira não vê compradores e pagamentos; sessão encerrada não é reutilizada.

### P1 — CSRF e alterações por GET

Evidência: há validação CSRF em partes de sêmen, embriões, receptoras, terceiros e tarefas. Em contrapartida, `perfil/_alterar.php`, os endpoints de compradores e `financeiro/_excluir.php` não apresentam a mesma validação. A exclusão financeira obtém o identificador de GET e não exige POST.

Fazer: aplicar uma proteção comum a todas as mutações, com POST e CSRF validado no servidor. GET deve ser seguro para consulta. SameSite é uma defesa complementar.

Aceite: tokens ausentes ou incorretos são recusados antes de qualquer gravação; GET não exclui, confirma pagamentos, troca senha nem altera cadastros.

### P1 — Exibição de dados, XSS e minimização

Evidência: há escape HTML nos fluxos recentes de perfil e compradores; `geral.php:105` exibe o login diretamente. `vendas/compradores.php:178` mostra CPF completo na listagem. `_buscar_compradores.php` devolve também e-mail nos resultados.

Fazer: padronizar codificação de saída conforme HTML, atributo, JavaScript e JSON. Exibir CPF mascarado nas listas e permitir revelação somente com necessidade e permissão. Reduzir campos retornados pelas buscas. Rever CPF e celular obrigatórios no perfil, justificando a necessidade por finalidade. Restringir campos livres para evitar coleta desnecessária.

Aceite: conteúdo cadastrado não executa scripts; usuários veem apenas campos necessários à função. Mascarar a interface não substitui autorização nem anonimiza o banco.

### P1 — Logs, documentos e proteção da infraestrutura

Evidência: `DBExecute` e `DBConnect` exibem mensagens de erro de banco. Há registro de mensagens de exceção em compradores/financeiro. Existe um `error_log` de aproximadamente 97 MiB na raiz e ele é rastreado pelo Git; seu conteúdo não foi lido nesta análise. `docker/php/local.ini` ativa exibição de erros, coerente com desenvolvimento, mas inadequada para produção.

Fazer: separar configuração de produção, enviar erros genéricos ao usuário e logs restritos para fora da raiz pública. Redigir dados pessoais, senhas e SQL sensível; limitar tamanho, prazo e acesso aos logs; investigar o conteúdo existente com pessoal autorizado. Criar auditoria de alterações de cadastro, acesso administrativo, exportações e decisões de exclusão com ator, cliente, ação, horário e resultado, sem copiar dados pessoais completos.

Confirmar HTTPS, TLS nas conexões quando necessário, criptografia de discos/backups, gestão separada das chaves e testes de restauração. Padronizar `Cache-Control: no-store` em respostas pessoais e avaliar cabeçalhos de segurança. Proteger downloads no servidor; para arquivos persistidos, usar armazenamento privado e acesso temporário autorizado.

O Docker está explicitamente documentado como local, com portas em localhost. O uso de root nele não prova uso de root em produção. A produção precisa de usuários com privilégios mínimos e um processo separado para migrações.

### P1/P2 — Plataforma e dependências

Evidência: Docker usa PHP 7.4, já identificado no README como fora de suporte; há chamadas `mysql_*`, AdminLTE 2, Bootstrap 3 e dependências antigas declaradas. Não foi feita correlação de versões realmente servidas com avisos de vulnerabilidade.

Fazer: migrar para uma versão de PHP com suporte na data da implantação, substituir APIs removidas e inventariar bibliotecas efetivamente utilizadas. Conferir arquivos distribuídos em `bower_components`, `plugins` e `dist`, além dos manifests. Atualizar componentes com validação funcional e acompanhar avisos dos fornecedores.

Aceite: aplicação validada em runtime suportado e dependências em uso inventariadas; riscos conhecidos corrigidos ou tratados com justificativa e prazo.

## 3. Funcionalidades e processos de privacidade

Não foram localizados no código próprio ou na documentação de negócio mecanismos explícitos de aviso de privacidade, gestão de solicitações de titulares, retenção ou anonimização. A ausência no repositório não prova ausência de processos externos.

### Atendimento ao titular

Criar um canal público e um fluxo interno com protocolo, verificação proporcional de identidade, cliente responsável, tipo de pedido, prazo, responsável e resposta. O fluxo deve permitir localizar dados, fornecer cópia segura, corrigir, avaliar bloqueio/eliminação e registrar justificativas. Implementar exportação abrangendo cadastro e vínculos financeiros, excluindo dados de outras pessoas e segredos de autenticação. Pode começar com atendimento assistido; um portal de autoatendimento não é requisito para iniciar o processo.

Para confirmação/acesso, o art. 19 prevê resposta simplificada imediata ou declaração completa em até 15 dias; não aplicar esse prazo indiscriminadamente a qualquer pedido. Referência: arts. 18–19 da [LGPD](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709compilado.htm).

### Retenção, eliminação e encerramento de cliente

`vendas/_excluir_comprador.php` bloqueia exclusão quando existem vendas ou lançamentos financeiros. A proteção de integridade é útil, mas falta um procedimento de análise de retenção e descarte.

Definir uma tabela de retenção por categoria: finalidade, evento inicial, prazo justificado, acesso durante retenção, obrigação de preservação e descarte. Contabilidade/jurídico devem validar os prazos aplicáveis. Evitar um único prazo genérico para tudo.

Implementar desativação de contas, limpeza de contatos desnecessários, preservação restrita de documentos obrigatórios e eliminação/anonimização quando cabível. Um nome substituído por “excluído” não garante anonimização se CPF, e-mail, observações ou vínculos permitem identificar a pessoa. Tratar PDFs, sessões, logs, réplicas, ambientes de teste e backups. Backups devem expirar de forma controlada e ter procedimento para reaplicar exclusões após restauração.

Definir também devolução/exportação e descarte dos dados da fazenda ao término do contrato. Eliminação não é irrestrita: observar as hipóteses de conservação do art. 16 da [LGPD](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709compilado.htm).

### Transparência, finalidades e fornecedores

Preparar inventário das operações: dado, titular, origem, finalidade, hipótese legal validada, cliente responsável, destinatários, prazo e controles. Separar cadastro/contrato, cobrança, registro genealógico, suporte e marketing. Evitar consentimento genérico para todas as operações; avaliar a hipótese adequada para cada finalidade.

Publicar aviso acessível no cadastro/login e no contato com compradores. Informar quem é responsável, dados/finalidades, compartilhamentos, conservação e canal de direitos. Registrar versão e apresentação do aviso; registrar consentimento e revogação apenas nos fluxos que dependam dessa hipótese.

Hipótese a confirmar contratualmente: as fazendas podem atuar como controladoras dos dados de seus compradores e o fornecedor do SOB como operador nesses fluxos; o fornecedor pode ser controlador de seus próprios dados comerciais de usuários. As decisões efetivas de cada operação determinam esses papéis. Formalizar instruções, confidencialidade, suporte, subcontratação, devolução, descarte e comunicação de incidentes nos contratos.

O PDF ARCO inclui dados pessoais; documentar seu destinatário e finalidade. O código mostra geração/download, não comprova transmissão automática à associação. Há um link HTTP para um serviço externo em `animal/vender.php`; confirmar o destino atual e usar canal seguro antes de orientar compartilhamentos.

Login e layout carregam Google Fonts e recursos de CDN. Avaliar informações técnicas transmitidas pelo navegador, fornecedores e eventual transferência internacional; hospedar assets localmente quando adequado. Não foi demonstrada a necessidade de um banner de cookies: primeiro inventariar os mecanismos usados e suas finalidades. Para transferências internacionais efetivas, consultar o [regulamento da ANPD](https://www.gov.br/anpd/pt-br/assuntos/assuntos-internacionais/transferencia-internacional-de-dados).

### Responsáveis, incidentes e operação

Definir responsável pelo programa, treinar quem acessa dados e avaliar a necessidade de encarregado. A dispensa para agentes de pequeno porte depende do enquadramento; permanece a necessidade de canal ao titular. Referência: [Resolução CD/ANPD nº 2/2022](https://www.gov.br/anpd/pt-br/acesso-a-informacao/institucional/atos-normativos/regulamentacoes_anpd/resolucao-cd-anpd-no-2-de-27-de-janeiro-de-2022).

Criar procedimento de detecção, contenção, preservação de evidências, avaliação e comunicação de incidentes. O prazo geral de comunicação de incidentes com risco ou dano relevante é de três dias úteis, observadas regras específicas e eventual regime diferenciado aplicável. O operador deve avisar o controlador a tempo. Referência: [orientação oficial da ANPD](https://www.gov.br/anpd/pt-br/canais_atendimento/agente-de-tratamento/comunicado-de-incidente-de-seguranca-cis).

Avaliar necessidade de relatório de impacto conforme operações e riscos, com apoio especializado; não presumir obrigatoriedade universal apenas pela existência do sistema. Usar dados sintéticos ou adequadamente desidentificados no desenvolvimento.

## 4. Ordem de execução sugerida

| Etapa | Entregas | Responsáveis sugeridos | Evidência de conclusão |
| --- | --- | --- | --- |
| Contenção imediata | Confirmar estado publicado; bloquear troca insegura de senha; verificar banco fixo; retirar manutenção/diagnóstico público; avaliar segredos reais | Desenvolvimento + infraestrutura | Rotas bloqueadas, isolamento confirmado e credenciais tratadas |
| Base técnica | Hash e recuperação de senha; consultas preparadas; autorização por cliente/ação; sessão e CSRF comuns; runtime suportado | Desenvolvimento | Testes de segurança e regressão com duas fazendas e usuários de permissões distintas |
| Governança em paralelo | Inventário, hipóteses legais, responsáveis, fornecedores, aviso, canal e plano de incidentes | Gestão + privacidade/jurídico + infraestrutura | Documentos aprovados, canal funcionando e simulação de incidente |
| Direitos e ciclo de vida | Exportação segura, correção, retenção, descarte, encerramento de contrato e auditoria | Desenvolvimento + gestão + contabilidade | Pedido de titular executado de ponta a ponta com justificativas e rastreabilidade |
| Validação operacional | Revisão da hospedagem, restauração, varredura de dependências, revisão de acesso e teste independente quando adequado | Infraestrutura + desenvolvimento + revisão especializada | Relatório de verificação e pendências com responsáveis/prazos |

Essas etapas são uma sequência de trabalho, não prazos legais nem uma estimativa fechada de desenvolvimento.

## 5. Critérios mínimos para validar a implantação

- A não acessa B, inclusive por busca, PDF, alteração de IDs e endpoints diretos.
- Usuário autenticado não redefine senha de terceiro; senha nunca trafega em URL e não fica legível no banco.
- POST sem CSRF não modifica dados; GET não modifica dados.
- Entradas de SQL e HTML são tratadas como dados; perfil não atualiza registros indiscriminadamente.
- Permissões de consulta, financeiro, técnico e administração são verificadas no servidor.
- Logout, revogação e expiração impedem reutilização da sessão.
- Cadastro, venda, parcelas, exclusão, PDFs e importação continuam funcionando em runtime suportado.
- Canal de titular entrega resposta segura e restrita à pessoa certa; retenção tem justificativa documentada.
- Logs e auditoria permitem investigar ações sem registrar senhas ou cópias excessivas de dados.
- Backups são restauráveis, protegidos e expiram conforme política; restauração respeita exclusões anteriores.

## 6. Informações que ainda precisam ser confirmadas

1. Qual versão está em produção e se o banco fixo local existe nela.
2. Esquema real de `user`, `admin`, `mercado`, transações e permissões de banco; eventuais cópias de senhas.
3. Hospedagem, localização dos dados, TLS, regras HTTP, backups, destinatários e fornecedores reais.
4. Quem acessa cada fazenda, se há contas compartilhadas e como funciona o suporte.
5. Finalidades de CPF, códigos e dados do técnico; regras contábeis e de registro aplicáveis.
6. Contratos, canal de privacidade, responsáveis e procedimentos que já existem fora do código.

O diagnóstico orienta implementação e validação. A conclusão de adequação depende também dessas evidências operacionais e das decisões do controlador.
