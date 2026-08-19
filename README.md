# TaskFlow

## Português

TaskFlow é uma aplicação web de gerenciamento de projetos construída com Laravel e Vue. O projeto foi criado como base para uma aplicação de gestão de trabalho e como exercício técnico de arquitetura, autenticação e desenvolvimento full-stack.

Nesta primeira etapa, o projeto inclui:

- autenticação de usuários com Laravel Sanctum;
- autenticação SPA baseada em sessão e cookies seguros;
- telas Vue de login, cadastro e dashboard;
- API versionada em `/api/v1`;
- criação e listagem de projetos;
- testes automatizados com Pest;
- separação de responsabilidades seguindo uma arquitetura Clean/Hexagonal no módulo de Projects.

### Stack

- PHP 8.5
- Laravel 13
- Laravel Sanctum 4
- MySQL 8
- Vue 3
- TypeScript
- Vite
- Vue Router
- Pinia
- Axios
- Pest

### Requisitos

Antes de começar, instale:

- PHP 8.5 ou compatível com a restrição do projeto;
- Composer;
- Node.js e npm;
- MySQL 8;
- uma chave de aplicação Laravel configurada.

### Instalação

Clone o repositório e entre na pasta do projeto:

```bash
git clone <repository-url>
cd taskflow
```

Instale as dependências PHP e JavaScript:

```bash
composer install
npm install
```

Crie o arquivo de ambiente e gere a chave da aplicação:

```bash
copy .env.example .env
php artisan key:generate
```

No `.env`, configure o banco MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=root
DB_PASSWORD=
```

Execute as migrações e gere os assets:

```bash
php artisan migrate
npm run build
```

### Desenvolvimento

Em terminais separados, execute a aplicação Laravel e o Vite:

```bash
php artisan serve
npm run dev
```

A aplicação estará disponível em `http://localhost:8000`.

O comando abaixo também executa o fluxo de setup definido no Composer:

```bash
composer run setup
```

### Autenticação

O frontend usa o fluxo recomendado de primeira parte do Sanctum:

1. O Axios envia requisições com `withCredentials` habilitado.
2. O cliente solicita o cookie CSRF em `/sanctum/csrf-cookie`.
3. O login ou cadastro autentica o usuário na sessão Laravel.
4. O frontend inicializa o estado chamando `GET /api/v1/me`.
5. O Pinia mantém apenas o usuário em memória.

Tokens de autenticação não são criados nem armazenados em `localStorage` ou `sessionStorage`.

### API

| Método | Endpoint | Descrição | Autenticação |
| --- | --- | --- | --- |
| `POST` | `/api/v1/auth/register` | Cria uma conta e autentica o usuário | Não |
| `POST` | `/api/v1/auth/login` | Autentica com e-mail e senha | Não |
| `POST` | `/api/v1/auth/logout` | Invalida a sessão atual | Sanctum |
| `GET` | `/api/v1/me` | Retorna o usuário autenticado | Sanctum |
| `GET` | `/api/v1/projects` | Lista projetos | Atualmente público |
| `POST` | `/api/v1/projects` | Cria um projeto | Atualmente público |

### Arquitetura

O módulo de Projects está organizado por responsabilidade:

```text
app/
├── Application/Projects/       Casos de uso e DTOs
├── Domain/Projects/            Entidades, contratos e regras de negócio
├── Infrastructure/Persistence/ Implementações Eloquent
├── Presentation/Http/          Controllers, requests e resources da API
└── Models/                     Modelos de infraestrutura do Laravel
```

A autenticação fica na camada de Presentation, usando o modelo de usuário do Laravel e o mecanismo de sessão integrado ao Sanctum. O módulo de Projects não depende do fluxo de autenticação atual.

### Testes e qualidade

Execute a suíte completa:

```bash
composer test
```

Outros checks úteis:

```bash
vendor/bin/pint --test
npx vue-tsc --noEmit
npm run build
```

Os testes cobrem registro, validação, e-mail duplicado, login, credenciais inválidas, `/me`, logout, proteção de endpoint e o fluxo existente de Projects.

### Escopo atual

Tasks, membros de projetos, Redis, filas, notificações e funcionalidades avançadas de Projects ainda não fazem parte desta etapa.

## English

TaskFlow is a project-management web application built with Laravel and Vue. It is the foundation for a work-management product and a technical exercise focused on architecture, authentication, and full-stack development.

The current iteration includes:

- user authentication with Laravel Sanctum;
- first-party SPA authentication using sessions and secure cookies;
- Vue login, registration, and dashboard screens;
- a versioned `/api/v1` API;
- project creation and listing;
- automated Pest tests;
- Clean/Hexagonal separation of responsibilities in the Projects module.

### Stack

- PHP 8.5
- Laravel 13
- Laravel Sanctum 4
- MySQL 8
- Vue 3
- TypeScript
- Vite
- Vue Router
- Pinia
- Axios
- Pest

### Requirements

Install the following before starting:

- PHP 8.5, or a version compatible with the project constraint;
- Composer;
- Node.js and npm;
- MySQL 8;
- a configured Laravel application key.

### Installation

Clone the repository and enter the project directory:

```bash
git clone <repository-url>
cd taskflow
```

Install PHP and JavaScript dependencies:

```bash
composer install
npm install
```

Create the environment file and generate the application key:

```bash
copy .env.example .env
php artisan key:generate
```

Configure MySQL in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and build the assets:

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

The application will be available at `http://localhost:8000`.

The Composer setup script can also run the project's initial setup flow:

```bash
composer run setup
```

### Authentication

The frontend follows Sanctum's recommended first-party SPA flow:

1. Axios sends requests with `withCredentials` enabled.
2. The client requests the CSRF cookie from `/sanctum/csrf-cookie`.
3. Login or registration authenticates the user in the Laravel session.
4. The frontend initializes its state through `GET /api/v1/me`.
5. Pinia keeps the user state in memory only.

No authentication tokens are created or stored in `localStorage` or `sessionStorage`.

### API

| Method | Endpoint | Description | Authentication |
| --- | --- | --- | --- |
| `POST` | `/api/v1/auth/register` | Creates an account and authenticates the user | No |
| `POST` | `/api/v1/auth/login` | Authenticates with email and password | No |
| `POST` | `/api/v1/auth/logout` | Invalidates the current session | Sanctum |
| `GET` | `/api/v1/me` | Returns the authenticated user | Sanctum |
| `GET` | `/api/v1/projects` | Lists projects | Currently public |
| `POST` | `/api/v1/projects` | Creates a project | Currently public |

### Architecture

The Projects module is organized by responsibility:

```text
app/
├── Application/Projects/       Use cases and DTOs
├── Domain/Projects/            Entities, contracts, and business rules
├── Infrastructure/Persistence/ Eloquent implementations
├── Presentation/Http/          API controllers, requests, and resources
└── Models/                     Laravel infrastructure models
```

Authentication lives in the Presentation layer, using Laravel's user model and Sanctum's integrated session mechanism. The Projects module does not depend on the current authentication flow.

### Tests and quality

Run the complete test suite:

```bash
composer test
```

Useful additional checks:

```bash
vendor/bin/pint --test
npx vue-tsc --noEmit
npm run build
```

The tests cover registration, validation, duplicate emails, login, invalid credentials, `/me`, logout, protected endpoints, and the existing Projects flow.

### Current scope

Tasks, project members, Redis, queues, notifications, and advanced Projects features are outside the scope of this iteration.
