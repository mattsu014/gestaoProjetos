# 📊 Sistema de Gestão de Projetos

Sistema completo de gestão de projetos desenvolvido para organização e acompanhamento de tarefas, permitindo o controle eficiente de atividades, equipes e progresso.

A aplicação conta com backend em Laravel e frontend em Vue.js, oferecendo uma interface moderna, responsiva e integrada a uma API RESTful.

## 🏗️ Arquitetura

* **Backend**: Laravel 12 + MySQL/MariaDB + Sanctum Auth
* **Frontend**: Vue.js 3 + Vite + Pinia + Vue Router
* **API**: RESTful com autenticação via tokens
* **Banco de Dados**: MySQL/MariaDB

## 🚀 Funcionalidades

* ✅ Autenticação de usuários (registro e login)
* ✅ CRUD completo de projetos
* ✅ CRUD completo de tarefas (vinculadas a projetos)
* ✅ Relatórios detalhados
* ✅ Interface responsiva
* ✅ Validações frontend e backend

## 📋 Pré-requisitos

### Para Arch Linux:

```bash
sudo pacman -S php php-fpm composer
sudo pacman -S mariadb
sudo systemctl start mariadb
sudo systemctl enable mariadb
sudo mysql_secure_installation
sudo pacman -S nodejs npm
```

### Para Windows:

1. XAMPP (PHP + MySQL)
2. Composer
3. Node.js

## 🛠️ Instalação e Configuração

### 1. Clonar o Repositório

```bash
git clone git@github.com:mattsu014/desafio-3e.git
cd desafio-3e
```

### 2. Backend (Laravel)

```bash
cd gestao-projetos
composer install
cp .env.example .env
php artisan key:generate
```

#### Banco de Dados:

Crie um banco chamado `gestaoprojetos` e configure o `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestaoprojetos
DB_USERNAME=root
DB_PASSWORD=sua_senha_aqui
```

```bash
php artisan migrate
php artisan serve
```

Backend: http://localhost:8000

### 3. Frontend (Vue.js)

```bash
cd gestao-projetos-frontend
npm install
npm run dev
```

Frontend: http://localhost:5173

## 🗃️ Estrutura do Banco

* `users`
* `projects`
* `tasks`
* `personal_access_tokens`

## 🔐 API Endpoints

### Autenticação

* POST /api/register
* POST /api/login
* GET /api/user

### Projetos

* GET /api/projects
* POST /api/projects
* GET /api/projects/{id}
* PUT /api/projects/{id}
* DELETE /api/projects/{id}

### Tarefas

* GET /api/tasks
* POST /api/tasks
* GET /api/tasks/{id}
* PUT /api/tasks/{id}
* DELETE /api/tasks/{id}

### Relatórios

* GET /api/reports/general
* GET /api/reports/tasks-by-project
* GET /api/reports/pending-tasks

## 🧪 Testes

### Interface Web

1. Acesse o frontend
2. Crie uma conta
3. Faça login
4. Gerencie projetos e tarefas

### API (Postman/Insomnia)

```bash
POST /api/register
POST /api/login
```

Use o token retornado:

```
Authorization: Bearer {token}
```

## 📱 Uso da Aplicação

* Criar e gerenciar projetos
* Adicionar tarefas vinculadas
* Atualizar status de tarefas
* Visualizar relatórios e métricas

## 🎯 Funcionalidades Implementadas

* Sistema de autenticação
* CRUD de projetos e tarefas
* Relatórios analíticos
* Interface responsiva
* Gerenciamento de estado com Pinia
* Navegação com Vue Router

## 📚 Tecnologias Utilizadas

* Laravel
* Vue.js
* MySQL
* REST API

## 👨‍💻 Autor

Mateus Valentim
GitHub: https://github.com/mattsu014

---

💡 **Dica:** mantenha backend e frontend rodando simultaneamente para funcionamento completo.
