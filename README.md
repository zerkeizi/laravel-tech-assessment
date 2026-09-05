# Sistema de Controle Financeiro

Sistema simplificado de Contas a Pagar e Contas a Receber, desenvolvido como desafio técnico. Backend em Laravel (API REST), frontend em Vue 3 (SPA), banco MySQL via Laravel Sail (Docker).

## Stack

- **Backend:** Laravel 13 (PHP 8.3+), Laravel Sanctum (autenticação de sessão para SPA), MySQL 8.4
- **Frontend:** Vue 3 (Composition API) + Vue Router, Axios, montado via Vite em uma única view Blade
- **Infra:** Laravel Sail (Docker Compose)

## Requisitos, como instalar e executar

Pré-requisitos: 
- Docker 29.6.2
- PHP 8.3
- Composer 2.10.3

Executando:
```bash
composer install
cp .env.example .env
php artisan key:generate

./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed

npm install
npm run build
```

A aplicação fica disponível em `http://localhost` (porta 80, padrão do `compose.yaml` do Sail — ajuste `APP_PORT` no `.env` se a porta estiver ocupada).

Para desenvolvimento com hot-reload do frontend, use `npm run dev` em paralelo (Vite serve os assets na porta configurada em `VITE_PORT`, padrão 5173).

## Usuário de teste

O seeder (`database/seeders/DatabaseSeeder.php`) cria um usuário de teste:

- **E-mail:** `admin@example.com`, `manager@example.com`
- **Senha:** `password`

## Estrutura do banco de dados

além das tabelas padrão do Laravel (`users`, `sessions`, `cache`, `jobs`), há três tabelas que representam as entidades principais:

**`parties`** — cadastro de pessoas/empresas (alimentam cliente ou fornecedor, conforme o contexto de uso)
| coluna | tipo | observação |
|---|---|---|
| `type` | enum(`individual`,`company`) | define se `document` é CPF (11 dígitos) ou CNPJ (14 dígitos) |
| `name` | string | nome / razão social |
| `document` | string, unique | CPF ou CNPJ |
| `email` | string, nullable | |
| `phone` | string, nullable | |

**`payables`** — contas a pagar
| coluna | tipo | observação |
|---|---|---|
| `party_id` | FK → parties, `restrict on delete` | |
| `description` | string | |
| `amount` | decimal(10,2) | |
| `issue_date` / `due_date` | date | |
| `payment_date` | date, nullable | preenchida ao registrar o pagamento |
| `status` | enum(`pendente`,`pago`,`vencido`,`cancelado`) | default `pendente` |

**`receivables`** — contas a receber (mesma estrutura de `payables`, com `receipt_date` no lugar de `payment_date` e status `recebido` no lugar de `pago`)

### Por que `parties` em vez de `clients`/`suppliers` separados?

Uma única tabela de cadastro é usada tanto para clientes quanto para fornecedores — o papel (cliente vs. fornecedor) é implícito conforme o registro é referenciado por uma `receivable` ou por uma `payable`, e não um atributo fixo armazenado na pessoa/empresa. Essa abordagem evita cadastros duplicado de pessoa/empresa caso sejam usadas tanto em contas a pagar quanto a receber.

### Por que a exclusão de uma `party` é bloqueada quando há lançamentos?

A FK usa `restrictOnDelete()`, reforçada por uma checagem no model (`Party::booted()`) que retorna um erro 409 claro antes mesmo de chegar ao banco. Um sistema financeiro não deve apagar silenciosamente nem órfãos registros de pagamento/recebimento ao remover um cadastro.

## Auditoria

Toda alteração (`UPDATE`) nas tabelas `parties`, `payables`, `receivables` e `users` é registrada automaticamente em tabelas de auditoria — uma trigger a nivel de banco de dados é disparada para qualquer escrita na tabela, seja via API ou por uma query dentro do banco de dados, isso garante a integridade e coerencia de um histórico de auditoria.

| tabela de log | coluna que referencia a origem | observação |
|---|---|---|
| `parties_log` | `party_id` | |
| `payables_log` | `payable_id` | |
| `receivables_log` | `receivable_id` | |
| `users_log` | `subject_id` | não usa `user_id` para se referir a origem pra não repetir nome da coluna" |

Cada tabela de _log tem `id`, `user_id` (quem fez a alteração), `data` (snapshot em JSON apenas de dados alterados) e timestamps. 


### Job

O status `vencido` as tabelas payables e receivables é alterado pela rotina diária `App\Jobs\setOverdueRecords`. Ela foi programada pra rodar uma vez ao dia, buscando registros que contemplem a condição (`due_date` < now() && `status` = "pending"), ou seja, data de vencimento anterior a hoje e com status pendente.

<!-- Com o tempo, essa tabela pode ficar muito grande diminuindo a performance do Job. Nesse caso, uma lógica de busca por limite de registros a cada rotina poderia ser aplicado. -->

## Autenticação

Sessão via Laravel Sanctum (SPA same-origin), não tokens Bearer: o frontend busca o cookie CSRF (`GET /sanctum/csrf-cookie`), autentica em `POST /login`, e as rotas `/api/*` ficam protegidas por `auth:sanctum`. Não há CORS a configurar, pois frontend e backend são servidos do mesmo domínio.

## Rotas principais da API

```
POST   /login
POST   /logout

GET    /api/user
GET|POST /api/parties            GET|PUT|DELETE /api/parties/{party}
GET|POST /api/payables           GET|PUT|DELETE /api/payables/{payable}
PATCH  /api/payables/{payable}/pay
GET|POST /api/receivables        GET|PUT|DELETE /api/receivables/{receivable}
PATCH  /api/receivables/{receivable}/receive
GET    /api/dashboard
GET    /api/reports              ?type=payable|receivable|both&party_id=&status=&date_from=&date_to=
```

Listagens (`parties`, `payables`, `receivables`, `reports`) são paginadas e aceitam filtros por `status`, `party_id` e `due_date` (período de vencimento).

## Diferenciais não implementados

- Repository/Service Pattern ou outra organização arquitetural justificável
- Interface responsiva
- Events/Listeners
- Dashboard com gráficos
- Controle de permissões/perfis
- Testes automatizados