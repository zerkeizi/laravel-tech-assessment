# Sistema de Controle Financeiro

Sistema simplificado de Contas a Pagar e Contas a Receber, desenvolvido como desafio técnico. Backend em Laravel (API REST), frontend em Vue 3 (SPA, mesmo domínio, sem Inertia), banco MySQL via Laravel Sail (Docker).

## Stack

- **Backend:** Laravel 13 (PHP 8.3+), Laravel Sanctum (autenticação de sessão para SPA), MySQL 8.4
- **Frontend:** Vue 3 (Composition API) + Vue Router, Axios, montado via Vite em uma única view Blade
- **Infra:** Laravel Sail (Docker Compose)

## Como instalar e executar

Pré-requisitos: Docker instalado.

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

- **E-mail:** `test@example.com`
- **Senha:** `password`

Não há tela de cadastro pública — o sistema é de uso interno, então o único usuário é provisionado via seeder.

## Estrutura do banco de dados

Três tabelas de domínio, além das tabelas padrão do Laravel (`users`, `sessions`, `cache`, `jobs`):

**`parties`** — cadastro de pessoas/empresas (cliente ou fornecedor, conforme o contexto de uso)
| coluna | tipo | observação |
|---|---|---|
| `type` | enum(`individual`,`company`) | define se `document` é CPF (11 dígitos) ou CNPJ (14 dígitos) |
| `name` | string | nome / razão social |
| `document` | string, único | CPF ou CNPJ |
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

Uma única tabela de cadastro é usada tanto para clientes quanto para fornecedores — o papel (cliente vs. fornecedor) é implícito conforme o registro é referenciado por uma `receivable` ou por uma `payable`, e não um atributo fixo armazenado na pessoa/empresa. Isso evita duplicar cadastro quando a mesma pessoa/empresa atua nos dois papéis.

### Por que o status "vencido" nunca é gravado diretamente?

`vencido` é sempre **derivado**: um registro `pendente` é considerado vencido quando `due_date` já passou e não há data de pagamento/recebimento. Essa regra vive em `App\Models\Concerns\HasOverdueStatus` (compartilhada entre `Payable` e `Receivable`) e é usada tanto nas queries agregadas (dashboard, relatórios) quanto num accessor de instância — assim o valor nunca fica dessincronizado entre os dois usos. O `status` gravado no banco só muda para `pago`/`recebido` (via os endpoints `PATCH .../pay` e `PATCH .../receive`) ou `cancelado`.

### Por que a exclusão de uma `party` é bloqueada quando há lançamentos?

A FK usa `restrictOnDelete()`, reforçada por uma checagem no model (`Party::booted()`) que retorna um erro 409 claro antes mesmo de chegar ao banco. Um sistema financeiro não deve apagar silenciosamente nem órfãos registros de pagamento/recebimento ao remover um cadastro.

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

Listagens (`parties`, `payables`, `receivables`, `reports`) são paginadas e aceitam filtros por status, `party_id` e período de vencimento.

## Diferenciais ainda não implementados

Por decisão de escopo, os seguintes diferenciais (opcionais, conforme o enunciado) ainda não foram implementados nesta etapa: testes automatizados (Pest), Events/Listeners, Jobs/Queue (sincronização diária de status vencido) e customização do `docker-compose` com worker de fila dedicado.
