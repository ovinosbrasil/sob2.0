# SOB — ambiente local

## Publicação da conexão em produção sem Docker

O `.env` fica na raiz do sistema, ao lado de `_config.php`, com o mesmo formato
do `.envprd` local. `.envprd` é apenas referência: não é carregado pela aplicação
e está ignorado pelo Git. `APP_PORT` é ignorada pelo carregador de banco.

Os arquivos corrigidos estão em `mysqli/` e na cópia `mysqliprd/`:

- Publicar `mysqliprd/dotenv.php` como `mysqli/dotenv.php` no servidor.
- Publicar `mysqliprd/environment.php` como `mysqli/environment.php` no servidor.
- Publicar o `_config.php` da raiz atualizado, que lê `DB_PREFIX` antes de
  montar o banco da fazenda, e o `.htaccess` que bloqueia os arquivos de ambiente.
- Preservar o `.env` de produção com seus próprios dados e os demais arquivos
  de conexão/consultas já existentes no servidor.

O carregamento não depende de `putenv`, Composer ou configuração de variáveis
na hospedagem. O PHP precisa conseguir ler o `.env`. Variáveis já fornecidas
ao processo, quando existentes, continuam tendo prioridade para compatibilidade
com o Docker local. Credenciais ausentes retornam 503 sem valores sensíveis.

O login usa `DB_DATABASE`; as telas usam `DB_PREFIX` e o banco autorizado da
sessão. O caminho do `.env` é absoluto, relativo à pasta dos arquivos PHP,
independente da URL ou subpasta acessada. Nenhuma senha de MySQL foi alterada.

Validação: `php tests/environment_producao_test.php` simula a hospedagem sem
Docker e com `putenv` desabilitado, usando apenas valores sintéticos.

## Conexão com banco pelo `.env`

O PHP carrega automaticamente o `.env` na raiz do projeto, por meio de
`mysqli/dotenv.php`. Preencha `DB_HOST`, `DB_USER`, `DB_PASSWORD`,
`DB_DATABASE` (banco central de login) e `DB_PREFIX`. O arquivo está ignorado
pelo Git. As credenciais não devem ser colocadas em `environment.php`.

Valores entre aspas simples ou duplas são lidos literalmente, sem interpolação.
Use uma configuração por linha, sem comentários após o valor. Variáveis já
fornecidas pelo Docker têm prioridade; assim, seu host interno `db` permanece
funcionando. A senha local existente foi preservada.

Na produção, configure o `.env` com os dados reais antes de publicar o PHP.
Publique também o `.htaccess`, que bloqueia o download do `.env` e de cópias
como `.env.backup`. Em outro servidor web, aplique a regra equivalente.
Usuário ou senha ausentes resultam em 503, sem expor os valores.
Não houve alteração de senha do MySQL, remoção de scripts ou migração das
rotinas antigas `mysql_*` nesta etapa. Segredos no histórico Git permanecem
e devem ser tratados separadamente se ainda forem válidos.

Teste: `php tests/dotenv_test.php`.

## Proteção CSRF — cadastro e ficha de animais

Cadastros por compra, rebanho, nascimento e terceiros, edição de animal e
terceiros, exclusão de animal, registro/desfazimento de saída e venda/desfazimento
pela ficha exigem POST com `_csrf_sob`. Exclusão, desfazer saída e desfazer venda
recebem o ID no corpo do POST. URLs antigas não executam essas ações por GET.
As proteções específicas já existentes para excluir/inativar terceiros permanecem.

Os botões usam `dist/js/csrf.js`, carregado por
`includes/confirmacao_exclusao.php`, para enviar o token de sessão no corpo.
Publique esses arquivos junto com `dist/js/confirmacao-exclusao.js` e os
formulários/endpoints de animais alterados. Recarregue abas antigas. Não há SQL.
Importação, avaliações, sanidade e outros módulos têm fluxos separados e não
estão todos cobertos por esta etapa.

Validação: `php -d short_open_tag=1 tests/csrf_endpoints_test.php`,
`php -d short_open_tag=1 tests/animal_venda_parcelas_test.php`,
`php -d short_open_tag=1 tests/desfazer_venda_test.php` e
`node tests/csrf_post_front_test.js`.

## Proteção CSRF — perfil, compradores, vendas conjuntas e financeiro

Esses fluxos usam `includes/csrf.php`: token aleatório vinculado à sessão,
comparação com `hash_equals` e exigência de POST antes de consultas ou gravações
da operação. GET recebe 405; POST sem token válido recebe 403. O retorno dos
compradores via AJAX preserva o contrato JSON de erro.

Foram ajustados edição de perfil, cadastro/edição/exclusão de compradores,
cadastro de venda conjunta e exclusão de venda nas páginas de comprador e
relatório. No financeiro, cadastro, edição, confirmação, cancelamento e exclusão
exigem token; os botões de confirmação enviam formulário POST e preservam os
filtros. Buscas e carregamento de modais continuam sendo consultas.

Publique o helper e todos os formulários/endpoints alterados juntos, incluindo
`includes/modal_novo_comprador.php` e `dist/js/confirmacao-financeiro.js`.
Após a publicação, recarregue abas antigas para obter o token. Não há migração SQL.
O token não deve ser colocado na URL; formulários e AJAX enviam `_csrf_sob` no corpo.
Não foi aplicada validação global em `_config.php`: outros módulos ainda usam
proteções próprias ou precisam de revisão específica com seus formulários.

Testes: `php -d short_open_tag=1 tests/csrf_endpoints_test.php`,
`php -d short_open_tag=1 tests/compradores_alertas_test.php`,
`php -d short_open_tag=1 tests/vendas_alertas_test.php`,
`php -d short_open_tag=1 tests/financeiro_alertas_test.php`,
`php -d short_open_tag=1 tests/financeiro_layout_test.php` e
`node tests/csrf_financeiro_front_test.js`.

## Limite de tentativas de login

Antes de publicar `_logar.php` e `includes/limite_login.php`, execute
`docker/mysql/migrations/030_limite_tentativas_login.sql` no banco central
de login (`DB_DATABASE`). A tabela usa InnoDB e locks para serializar as
tentativas concorrentes por conta e IP. Se a tabela não estiver disponível,
o login é recusado; não há liberação sem o limitador.

Cinco falhas por conta ou 50 por IP dentro de 15 minutos bloqueiam novas
tentativas por 15 minutos a partir da falha que atingiu o limite. Tentativas
bloqueadas não prolongam esse prazo. Sucesso limpa as falhas da conta e
preserva as do IP. O ID da conta identifica também variações de login que
o banco considere equivalentes. Contas inexistentes também são limitadas.
Criar outra sessão ou limpar cookies não reinicia os contadores.

Somente fingerprints SHA-256 dos identificadores entram na tabela, com
contagem e horários; não são dados anonimizados. Registros sem atualização
há mais de 24 horas são removidos em lotes de até 100 a cada tentativa.
O SQL contém a limpeza diária opcional para instalações sem tráfego regular.
Eventos de sucesso, falha e bloqueio são registrados no log PHP sem senha,
login ou IP. Configure rotação e acesso restrito ao log na hospedagem.

O IP vem de `REMOTE_ADDR`, sem confiar em cabeçalhos enviados pelo cliente.
Se houver proxy/CDN, configure no servidor web a recuperação do IP real apenas
a partir de proxies confiáveis; caso contrário, todos podem compartilhar o
limite do proxy. O limite por conta também vale em redes diferentes e pode
provocar bloqueio temporário de uma conta alvo de ataque.

Testes: `php tests/limite_login_test.php` e, no container app,
`php tests/limite_login_mysql_test.php` (tabelas temporárias, sem alterar contas).

## Sessões

As rotas protegidas usam `includes/sessao.php`: expiração após 30 minutos sem
requisições autenticadas e limite absoluto de 8 horas desde o login. A atividade
não reinicia o limite absoluto. Sessões anteriores a essa atualização exigem
novo login. O logout destrói os dados da sessão e remove o cookie.

O cookie usa HttpOnly e SameSite=Lax, com Secure quando o PHP identifica HTTPS.
Em hospedagem com proxy que termina TLS, configure `SESSION_COOKIE_SECURE=1`
no processo PHP de produção. Cabeçalhos de proxy enviados pelo cliente não são
usados para decidir essa proteção. O ambiente HTTP local continua funcionando.
O caminho do cookie e o retorno ao login respeitam instalações em `/sob2.0/`.
Publique juntos o helper, `_config.php`, `_logar.php`, `_logout.php`, `chip.php`
e `animal/_cadastrar_importacao.php`. Preserve a seleção de banco da produção;
o banco fixo deste checkout é apenas uma configuração local.

Verificação: `php tests/sessao_test.php`.

## Migração das senhas de login

Antes de publicar o novo `_logar.php`, execute
`docker/mysql/migrations/029_hash_senha_login.sql` no **banco central de login**
(variável `DB_DATABASE`, padrão `siste870_sob`). A migração amplia `user.senha`
para `VARCHAR(255)` e preserva os valores atuais. Os arquivos de migração
não são executados automaticamente pelo Compose.

O login valida hashes com `password_verify`. Uma senha antiga válida é
substituída por `password_hash(PASSWORD_DEFAULT)` antes de abrir a sessão.
O código verifica o tamanho da coluna antes da conversão e confirma que o
hash foi persistido integralmente. Não é necessário enviar senhas dos usuários.
Logins duplicados são recusados e precisam ser regularizados no banco.
Senhas legadas iniciadas por `$`, contendo NUL ou com mais de 72 bytes
precisam de redefinição assistida; não são migradas automaticamente.

A compatibilidade legada é temporária. Defina uma data de encerramento,
providencie redefinição assistida das contas restantes e então configure
`AUTH_ALLOW_LEGACY_PASSWORDS=0` no ambiente do processo PHP. No Compose,
adicione essa variável em `app.environment` e recrie o serviço; apenas colocá-la
no `.env` não a repassa ao container. Com a opção desativada, somente hashes
reconhecidos são aceitos. Novos provisionamentos devem gravar hashes diretamente.
O cadastro público atual coleta contato e não define senha.

Valide em homologação: login legado correto converte uma única conta; senha
errada não altera credenciais; segundo login usa o hash; entrada contendo aspas
não altera a consulta; coluna curta recusa a migração sem substituir a senha;
opção legada desativada recusa texto e continua aceitando hashes.
Faça a validação com dados de teste e backup protegido antes da implantação.

Teste local da verificação de senhas: `php tests/autenticacao_senha_test.php`.

Requisitos: Docker Engine e Docker Compose.

```bash
cp .env.example .env
docker compose up -d --build
```

Abra http://localhost:8000. As alterações nos arquivos PHP aparecem diretamente
no container. Não é necessário instalar dependências npm ou Composer para abrir
a interface: os assets já estão no repositório.

## Banco de dados

O serviço usa MySQL 8.4 e armazena os dados no volume `mysql_database`.
O volume anterior do MariaDB é preservado, mas não é reutilizado pelo MySQL.
Se você já importou dados nele, exporte um backup SQL para importar no MySQL.

O repositório não inclui o esquema SQL nem usuários iniciais. A tela de login
abre sem o backup, mas autenticação e demais funções precisam dos bancos reais.
O banco de login padrão é `siste870_sob`; o campo `user.cookie` determina o banco
do usuário, concatenado com `DB_PREFIX` (por exemplo, `fazenda` vira
`siste870_fazenda`). Importe também esses bancos, preservando os nomes ou
ajustando o prefixo e os dados correspondentes.

Antes da primeira inicialização, você pode colocar backups `.sql` em
`docker/mysql/init/`. Eles são executados em ordem alfabética apenas quando o
volume está vazio. Backups de outros bancos devem incluir `CREATE DATABASE` e
`USE` para selecionar o destino correto.

Para importar o banco de login depois de iniciar o ambiente:

```bash
docker compose exec -T db sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < backup.sql
```

Para um backup com vários bancos e seus próprios `CREATE DATABASE`/`USE`:

```bash
docker compose exec -T db sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD"' < bancos.sql
```

Credenciais e porta podem ser ajustadas no `.env`. Alterar a senha no `.env`
não altera a senha de um banco já inicializado. O banco publica a porta 3307 apenas no localhost.
O usuário root é usado somente neste ambiente local para acessar os múltiplos bancos.

## Beekeeper Studio

Crie uma conexão MySQL com os seguintes dados:

| Campo | Valor |
| --- | --- |
| Host | `127.0.0.1` |
| Porta | `3307` (ou `DB_PORT` do `.env`) |
| Usuário | `root` |
| Senha | Valor de `DB_PASSWORD` no `.env` (padrão: `sob_local`) |
| Banco padrão | Valor de `DB_DATABASE` no `.env` (padrão: `siste870_sob`) |

Use conexão TCP direta, sem túnel SSH. Para mudar a porta, defina `DB_PORT`
no `.env` e execute `docker compose up -d`. A aplicação continua usando `db:3306`
internamente.

## Acesso pelo navegador (phpMyAdmin)

Após `docker compose up -d`, abra http://localhost:8080.
Entre com o usuário `root` e a senha de `DB_PASSWORD` no `.env`.
O servidor MySQL já está configurado como `db` internamente.

Você pode consultar tabelas, executar SQL e importar backups pela aba
**Importar** (limite de upload: 256 MB). Para arquivos maiores, use o comando
de importação pelo terminal acima. A porta pode ser alterada com `PMA_PORT`
no `.env`; o acesso fica restrito ao localhost.

## Comandos úteis

```bash
docker compose logs -f app
docker compose down
docker compose up -d
```

`down` preserva o volume do banco. `down -v` apaga todos os dados locais.

## Compatibilidade

Este ambiente é exclusivo para desenvolvimento local. PHP 7.4 foi escolhido
como ponto inicial para o código legado e já está fora de suporte; a aplicação
fica disponível apenas no localhost. As tags curtas estão habilitadas e a
extensão `mysqli` está instalada.

Login, cadastro, alteração de senha e configuração principal leem as variáveis
do ambiente. Há scripts antigos com conexão fixa e chamadas `mysql_*`, incluindo
`animal/avaliacao.php` e arquivos em `funcoes_banco/`, que precisam ser migrados
para `mysqli` antes de funcionar neste PHP. O Docker não resolve essas
incompatibilidades; a validação funcional completa depende do backup SQL.
