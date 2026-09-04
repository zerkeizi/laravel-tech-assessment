💻 Desafio Técnico – Sistema de Controle Financeiro

Desenvolver uma aplicação web utilizando:

Backend: PHP + Laravel
Banco de dados: MySQL ou PostgreSQL
Frontend: livre escolha
Importante: não utilizar IA como funcionalidade da aplicação.

🎯 Objetivo

Criar um sistema simplificado de Contas a Pagar e Contas a Receber, permitindo que uma empresa acompanhe sua posição financeira e tenha uma visão gerencial das movimentações.

Funcionalidades mínimas

1. Autenticação

- Login e logout de usuários.
- Área do sistema protegida por autenticação.

2. Cadastro de Pessoas/Empresas

- Nome/Razão Social
- CPF/CNPJ
- E-mail
- Telefone
- Tipo: Cliente / Fornecedor

3. Contas a Receber
Permitir cadastrar:

- Cliente
- Descrição
- Valor
- Data de emissão
- Data de vencimento
- Status: Pendente / Recebido / Vencido / Cancelado
- Data do recebimento

4. Contas a Pagar
Permitir cadastrar:

- Fornecedor
- Descrição
- Valor
- Data de emissão
- Data de vencimento
- Status: Pendente / Pago / Vencido / Cancelado
- Data do pagamento

5. Dashboard Gerencial

Criar uma tela inicial apresentando, no mínimo:

- Total a receber
- Total recebido
- Total vencido a receber
- Total a pagar
- Total pago
- Total vencido a pagar
- Saldo previsto
- Saldo realizado

O candidato pode acrescentar gráficos, indicadores ou informações que considere relevantes.

6. Relatórios

Criar uma tela de relatório financeiro permitindo filtros por:

- Período
- Cliente/Fornecedor
- Contas a pagar ou receber
- Status

O relatório deverá apresentar totalizadores das informações filtradas.

⭐ Diferenciais

Não são obrigatórios, mas serão considerados na avaliação:

- Dashboard com gráficos
- Paginação
- Busca e filtros
- Validação adequada dos dados
- Controle de permissões/perfis
- API REST
- Testes automatizados
- Jobs/Queues
- Events/Listeners
- Docker
- Seeders e Factories
- Repository/Service Pattern ou outra organização arquitetural justificável
- Exportação de relatório para Excel/PDF
- Auditoria/log das alterações
- Documentação da API
- Interface responsiva

📦 Entrega

Enviar:

- Link de um repositório Git
- Código-fonte completo
- README explicando como instalar e executar o projeto
- Estrutura do banco de dados/migrations
- Usuário e senha para teste
- Breve descrição das decisões técnicas adotadas

Não existe necessidade de desenvolver um sistema visualmente perfeito. Nosso principal objetivo é analisar qualidade do código, arquitetura, organização, segurança, conhecimento do Laravel, modelagem do banco de dados e capacidade de resolver problemas.

Também valorizaremos as decisões tomadas pelo desenvolvedor além dos requisitos mínimos.

O projeto será utilizado exclusivamente como avaliação técnica no processo seletivo.