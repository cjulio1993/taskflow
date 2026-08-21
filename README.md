# TaskFlow

[Português](#português) · [English](#english)

---

## Português

TaskFlow é uma aplicação local de gerenciamento de projetos e tarefas, construída como projeto de portfólio para demonstrar competências de desenvolvimento **Senior PHP Laravel & Vue**.

O foco é apresentar uma solução pequena, explicável em uma entrevista técnica, com autenticação SPA segura, API REST versionada, autorização por projeto e uma arquitetura Clean/Hexagonal pragmática.

### Funcionalidades atuais

- Autenticação SPA de primeira parte com Laravel Sanctum, sessão e cookies HTTP.
- Cadastro, login, logout e consulta do usuário autenticado.
- Projetos com proprietário e membros.
- Controle de acesso: apenas membros podem visualizar projetos, membros e tarefas; apenas o proprietário gerencia membros.
- Quadro de tarefas por projeto com os estados `todo`, `in_progress` e `done`.
- Criação, edição, atribuição e remoção de tarefas.
- Atribuição permitida apenas a membros do mesmo projeto.
- Interface Vue responsiva: dashboard, projetos, quadro de tarefas, modal de tarefas e gestão de membros.

### Stack

- PHP 8.5 usado no desenvolvimento local (a restrição do Composer é `^8.3`)
- Laravel 13
- Laravel Sanctum 4
- MySQL 8
- Vue 3 + Composition API + TypeScript
- Vite + Tailwind CSS
- Vue Router, Pinia e Axios
- Pest e Laravel Pint

### Arquitetura

Os módulos de **Projects** e **Tasks** seguem uma separação pragmática por responsabilidade:

```text
app/
├── Application/
│   ├── Projects/              DTOs e casos de uso de projetos e membros
│   └── Tasks/                 DTOs e casos de uso de tarefas
├── Domain/
│   ├── Projects/              Entidades, enums, exceções e portas
│   └── Tasks/                 Entidade Task, enums, exceções e porta
├── Infrastructure/
│   └── Persistence/Eloquent/  Modelos e repositórios Eloquent
└── Presentation/Http/Api/V1/  Controllers, requests, resources e policies
```

- O **Domain** não depende de Laravel, HTTP ou Eloquent.
- Os casos de uso da **Application** dependem de interfaces de repositório.
- A **Infrastructure** implementa essas interfaces com Eloquent.
- A **Presentation** contém validação HTTP, resources, rotas e policies.
- O `AppServiceProvider` é o ponto de composição que vincula portas às implementações.

Não há repositório genérico: cada porta existe apenas quando representa uma necessidade real do domínio.

### Requisitos

- PHP 8.5 recomendado
- Composer
- Node.js e npm
- MySQL 8 em execução localmente

Não é usado Docker, Redis, filas ou WebSockets neste estágio.

### Instalação local

Clone o repositório e instale as dependências:

```bash
git clone <repository-url>
cd taskflow
composer install
npm install
```

Crie o arquivo de ambiente e gere a chave da aplicação. No Windows/PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Configure no `.env` o seu MySQL local. Não versione o arquivo `.env` nem exponha credenciais:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=<seu-usuario-local>
```

Defina `DB_PASSWORD` apenas no seu `.env` local. Em seguida:

```bash
php artisan migrate
npm run build
```

### Desenvolvimento

Execute Laravel e Vite em terminais separados:

```bash
php artisan serve
npm run dev
```

Abra `http://localhost:8000`.

Para o Sanctum SPA local funcionar, mantenha os hosts/portas locais usados pelo Laravel e Vite em `SANCTUM_STATEFUL_DOMAINS` no `.env`.

### Segurança de autenticação

O frontend usa o fluxo de SPA de primeira parte do Sanctum:

1. Axios envia requisições com `withCredentials` e suporte a XSRF.
2. Antes de login ou cadastro, o cliente solicita `/sanctum/csrf-cookie`.
3. O Laravel autentica o usuário na sessão e regenera a sessão após login/cadastro.
4. O Pinia mantém somente o usuário autenticado em memória, obtido por `GET /api/v1/me`.
5. Logout invalida a sessão e regenera o token CSRF.

Tokens não são armazenados em `localStorage` ou `sessionStorage`.

### API REST (`/api/v1`)

| Método | Endpoint | Descrição | Acesso |
| --- | --- | --- | --- |
| `POST` | `/auth/register` | Cria e autentica usuário | Público |
| `POST` | `/auth/login` | Autentica usuário | Público |
| `POST` | `/auth/logout` | Encerra a sessão | Autenticado |
| `GET` | `/me` | Retorna o usuário atual | Autenticado |
| `GET` | `/projects` | Lista projetos do usuário | Autenticado |
| `POST` | `/projects` | Cria projeto e registra o criador como owner | Autenticado |
| `GET` | `/projects/{project}` | Exibe projeto | Membro do projeto |
| `GET` | `/projects/{project}/members` | Lista membros | Membro do projeto |
| `POST` | `/projects/{project}/members` | Adiciona usuário existente por e-mail | Owner |
| `DELETE` | `/projects/{project}/members/{user}` | Remove membro não-owner | Owner |
| `GET` | `/projects/{project}/tasks` | Lista tarefas | Membro do projeto |
| `POST` | `/projects/{project}/tasks` | Cria tarefa | Membro do projeto |
| `GET` | `/projects/{project}/tasks/{task}` | Exibe tarefa | Membro do projeto |
| `PUT` / `PATCH` | `/projects/{project}/tasks/{task}` | Atualiza tarefa | Membro do projeto |
| `DELETE` | `/projects/{project}/tasks/{task}` | Remove tarefa | Membro do projeto |

O escopo de rotas aninhadas impede acessar uma tarefa de um projeto por meio da URL de outro projeto.

### Interface Vue

Rotas autenticadas:

- `/dashboard`
- `/projects`
- `/projects/:id`

Os módulos de comunicação com a API ficam centralizados em:

```text
resources/js/api/
├── auth-api.ts
├── projects-api.ts
├── members-api.ts
└── tasks-api.ts
```

O estado de autenticação fica no Pinia. O estado de projetos, membros e tarefas é local às páginas, pois não exige compartilhamento global neste escopo.

### Qualidade e testes

```bash
php artisan test
vendor/bin/pint --test
npm exec vue-tsc --noEmit
npm run build
```

Última verificação registrada:

- **45 testes Pest aprovados**
- **128 assertions**
- Laravel Pint aprovado
- TypeScript/Vue aprovado
- Build do Vite aprovado

### Fora do escopo atual

- Redis, filas e notificações
- WebSockets e recursos em tempo real
- Drag and drop
- Comentários e activity log
- Convites por e-mail
- Testes de componentes Vue

---

## English

TaskFlow is a local project and task-management application built as a portfolio project for a **Senior PHP Laravel & Vue Developer** role.

It is intentionally small enough to explain in a technical interview while demonstrating secure SPA authentication, a versioned REST API, project-based authorization, and pragmatic Clean/Hexagonal Architecture.

### Current features

- First-party SPA authentication with Laravel Sanctum, sessions, and HTTP cookies.
- Registration, login, logout, and current-user retrieval.
- Projects with an owner and members.
- Access control: only members can view projects, members, and tasks; only owners manage members.
- Per-project task board with `todo`, `in_progress`, and `done` states.
- Task creation, editing, assignment, and deletion.
- Tasks can only be assigned to members of the same project.
- Responsive Vue UI: dashboard, project list, task board, task modal, and member management.

### Stack

- PHP 8.5 used in local development (the Composer constraint is `^8.3`)
- Laravel 13
- Laravel Sanctum 4
- MySQL 8
- Vue 3 + Composition API + TypeScript
- Vite + Tailwind CSS
- Vue Router, Pinia, and Axios
- Pest and Laravel Pint

### Architecture

The **Projects** and **Tasks** modules use a pragmatic separation of responsibilities:

```text
app/
├── Application/
│   ├── Projects/              Project/member DTOs and use cases
│   └── Tasks/                 Task DTOs and use cases
├── Domain/
│   ├── Projects/              Entities, enums, exceptions, and ports
│   └── Tasks/                 Task entity, enums, exceptions, and port
├── Infrastructure/
│   └── Persistence/Eloquent/  Eloquent models and repositories
└── Presentation/Http/Api/V1/  Controllers, requests, resources, and policies
```

- The **Domain** has no Laravel, HTTP, or Eloquent dependency.
- **Application** use cases depend on repository interfaces.
- **Infrastructure** implements those interfaces using Eloquent.
- **Presentation** owns HTTP validation, resources, routing, and policies.
- `AppServiceProvider` is the composition root that binds ports to implementations.

There is no generic repository; each port exists only where it has a concrete domain purpose.

### Requirements

- PHP 8.5 recommended
- Composer
- Node.js and npm
- Locally running MySQL 8

Docker, Redis, queues, and WebSockets are not used at this stage.

### Local installation

Clone the repository and install dependencies:

```bash
git clone <repository-url>
cd taskflow
composer install
npm install
```

Create the environment file and generate the application key. On Windows/PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Configure your local MySQL connection in `.env`. Never commit `.env` or expose credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=<your-local-user>
```

Set `DB_PASSWORD` only in your local `.env`, then run:

```bash
php artisan migrate
npm run build
```

### Development

Run Laravel and Vite in separate terminals:

```bash
php artisan serve
npm run dev
```

Open `http://localhost:8000`.

For local Sanctum SPA authentication, keep the Laravel and Vite local hosts/ports in `SANCTUM_STATEFUL_DOMAINS` in `.env`.

### Authentication security

The frontend follows Sanctum's first-party SPA flow:

1. Axios sends requests with `withCredentials` and XSRF support.
2. Before login or registration, the client requests `/sanctum/csrf-cookie`.
3. Laravel authenticates the user in the session and regenerates the session after login/registration.
4. Pinia keeps only the authenticated user in memory, loaded through `GET /api/v1/me`.
5. Logout invalidates the session and regenerates the CSRF token.

Authentication tokens are not stored in `localStorage` or `sessionStorage`.

### REST API (`/api/v1`)

| Method | Endpoint | Description | Access |
| --- | --- | --- | --- |
| `POST` | `/auth/register` | Creates and authenticates a user | Public |
| `POST` | `/auth/login` | Authenticates a user | Public |
| `POST` | `/auth/logout` | Ends the current session | Authenticated |
| `GET` | `/me` | Returns the current user | Authenticated |
| `GET` | `/projects` | Lists the user's projects | Authenticated |
| `POST` | `/projects` | Creates a project and records the creator as owner | Authenticated |
| `GET` | `/projects/{project}` | Shows a project | Project member |
| `GET` | `/projects/{project}/members` | Lists members | Project member |
| `POST` | `/projects/{project}/members` | Adds an existing user by email | Owner |
| `DELETE` | `/projects/{project}/members/{user}` | Removes a non-owner member | Owner |
| `GET` | `/projects/{project}/tasks` | Lists tasks | Project member |
| `POST` | `/projects/{project}/tasks` | Creates a task | Project member |
| `GET` | `/projects/{project}/tasks/{task}` | Shows a task | Project member |
| `PUT` / `PATCH` | `/projects/{project}/tasks/{task}` | Updates a task | Project member |
| `DELETE` | `/projects/{project}/tasks/{task}` | Deletes a task | Project member |

Scoped nested route binding prevents a task from one project being accessed through another project's URL.

### Vue UI

Authenticated routes:

- `/dashboard`
- `/projects`
- `/projects/:id`

API communication is centralized in:

```text
resources/js/api/
├── auth-api.ts
├── projects-api.ts
├── members-api.ts
└── tasks-api.ts
```

Authentication state lives in Pinia. Project, member, and task state remains page-local because it does not need global sharing in this scope.

### Quality and tests

```bash
php artisan test
vendor/bin/pint --test
npm exec vue-tsc --noEmit
npm run build
```

Last recorded verification:

- **45 Pest tests passing**
- **128 assertions**
- Laravel Pint passing
- Vue/TypeScript check passing
- Vite build passing

### Current exclusions

- Redis, queues, and notifications
- WebSockets and real-time features
- Drag and drop
- Comments and activity logs
- Email invitations
- Vue component tests
