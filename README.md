# SOB — ambiente local

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
