# 📊 Sistema de Gestão de Projetos - 3E Soluções

Sistema completo de gestão de projetos desenvolvido como desafio técnico para a vaga de estágio em desenvolvimento web. A aplicação inclui backend em Laravel 12 com API RESTful e frontend em Vue.js 3 com interface moderna e responsiva.

## 🏗️ Arquitetura

- **Backend**: Laravel 12 + MySQL/MariaDB + Sanctum Auth
- **Frontend**: Vue.js 3 + Vite + Pinia + Vue Router
- **API**: RESTful com autenticação via tokens
- **Banco de Dados**: MySQL/MariaDB

## 🚀 Funcionalidades

- ✅ Autenticação de usuários (registro e login)
- ✅ CRUD completo de projetos
- ✅ CRUD completo de tarefas (vinculadas a projetos)
- ✅ Relatórios detalhados
- ✅ Interface responsiva
- ✅ Validações frontend e backend

## 📋 Pré-requisitos

### Para Arch Linux:
```bash
# Instalar PHP
sudo pacman -S php php-fpm composer

# Instalar MariaDB
sudo pacman -S mariadb
sudo systemctl start mariadb
sudo systemctl enable mariadb
sudo mysql_secure_installation

# Instalar Node.js
sudo pacman -S nodejs npm
```

### Para Windows:
1. **XAMPP** (PHP + MySQL): https://www.apachefriends.org/
2. **Composer**: https://getcomposer.org/
3. **Node.js**: https://nodejs.org/

## 🛠️ Instalação e Configuração

### 1. Clonar o Repositório
```bash
git clone git@github.com:mattsu014/desafio-3e.git
cd desafio-3e
```

### 2. Configurar o Backend (Laravel)

```bash
# Navegar para a pasta do backend
cd gestao-projetos

# Instalar dependências PHP
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate
```

#### Configurar Banco de Dados:

**No Arch Linux (MariaDB):**
```bash
# Acessar o MariaDB
sudo mysql -u root -p

# Criar banco de dados
CREATE DATABASE gestaoprojetos;
EXIT;
```

**No Windows (XAMPP):**
1. Abrir phpMyAdmin (http://localhost/phpmyadmin)
2. Criar banco de dados chamado `gestaoprojetos`

#### Editar arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestaoprojetos
DB_USERNAME=root
DB_PASSWORD=sua_senha_aqui
```

#### Executar Migrações:
```bash
php artisan migrate
```

#### Iniciar Servidor Backend:
```bash
php artisan serve
```
**Backend rodando em:** http://localhost:8000

### 3. Configurar o Frontend (Vue.js)

**Em um novo terminal:**
```bash
# Navegar para a pasta do frontend
cd gestao-projetos-frontend

# Instalar dependências JavaScript
npm install

# Iniciar servidor de desenvolvimento
npm run dev
```

**Frontend rodando em:** http://localhost:5173

## 🗃️ Estrutura do Banco de Dados

### Tabelas Criadas:
- `users` - Usuários do sistema
- `projects` - Projetos (relacionados a usuários)
- `tasks` - Tarefas (relacionadas a projetos)
- `personal_access_tokens` - Tokens de autenticação

## 🔐 API Endpoints

### Autenticação:
- `POST /api/register` - Registrar usuário
- `POST /api/login` - Login de usuário
- `GET /api/user` - Dados do usuário autenticado

### Projetos:
- `GET /api/projects` - Listar projetos
- `POST /api/projects` - Criar projeto
- `GET /api/projects/{id}` - Ver projeto específico
- `PUT /api/projects/{id}` - Atualizar projeto
- `DELETE /api/projects/{id}` - Deletar projeto

### Tarefas:
- `GET /api/tasks` - Listar tarefas
- `POST /api/tasks` - Criar tarefa
- `GET /api/tasks/{id}` - Ver tarefa específica
- `PUT /api/tasks/{id}` - Atualizar tarefa
- `DELETE /api/tasks/{id}` - Deletar tarefa

### Relatórios:
- `GET /api/reports/general` - Relatório geral do sistema
- `GET /api/reports/tasks-by-project` - Tarefas agrupadas por projeto
- `GET /api/reports/pending-tasks` - Tarefas pendentes

## 🧪 Testando a Aplicação

### 1. Via Interface Web:
1. Acesse http://localhost:5173
2. Registre um novo usuário
3. Faça login
4. Explore as funcionalidades de projetos, tarefas e relatórios

### 2. Via Postman/Insomnia:

#### Registrar Usuário:
```bash
POST http://localhost:8000/api/register
{
    "name": "Seu Nome",
    "email": "seu@email.com",
    "password": "senha123",
    "password_confirmation": "senha123"
}
```

#### Login:
```bash
POST http://localhost:8000/api/login
{
    "email": "seu@email.com",
    "password": "senha123"
}
```

**Guarde o token retornado** e use no header das requisições:
```
Authorization: Bearer {seu_token}
Content-Type: application/json
```

## 📱 Como Usar a Aplicação

1. **Registro/Login**: Crie uma conta ou faça login
2. **Projetos**: 
   - Clique em "Projetos" no menu
   - Adicione novos projetos com o botão "+"
   - Edite ou exclua projetos existentes
3. **Tarefas**:
   - Acesse "Tarefas" no menu
   - Crie tarefas vinculadas a projetos
   - Altere o status das tarefas
4. **Relatórios**:
   - Veja estatísticas gerais em "Relatórios"
   - Analise tarefas por projeto
   - Verifique tarefas pendentes


## 🎯 Funcionalidades Implementadas

- [x] Sistema de autenticação completo
- [x] CRUD de projetos com validações
- [x] CRUD de tarefas vinculadas a projetos
- [x] Múltiplos relatórios
- [x] Interface responsiva
- [x] Validações frontend e backend
- [x] Feedback visual para usuário
- [x] Gestão de estado com Pinia
- [x] Navegação com Vue Router

## # 📚 Recursos de Aprendizado
- **[Laravel 12 CRUD with React Starter Kit](https://www.youtube.com/watch?v=VdYfBzOFPUQ&t=97s)** - Aprendi a criar operações básicas (Create, Read, Update, Delete)
- **[How To CRUD (Create, Read, Update, and Delete) With Laravel ](https://kinsta.com/blog/laravel-crud/)** - Complementação do meu aprendizado
- **[Vue.js Beginner Course | Build & Deploy a Modern Fitness App](https://www.youtube.com/watch?v=JAgeFLJYrUY)** - Ajudou a transportar os meus conhecimentos de React para Vue

## 🎯 Conhecimentos Adquiridos
- Desenvolver API RESTful com Laravel
- Criar interface moderna com Vue.js
- Implementar autenticação entre sistemas

*Projeto desenvolvido como desafio técnico para vaga de estágio*

## 👨‍💻 Desenvolvido por

**Mateus Valentim**  
GitHub: [mattsu014](https://github.com/mattsu014)

## 📄 Licença

Este projeto foi desenvolvido como parte do processo seletivo para a vaga de estágio em desenvolvimento web na 3E Soluções.

---

**💡 Dica:** Certifique-se de que ambos os servidores (backend na porta 8000 e frontend na porta 5173) estejam rodando simultaneamente para o correto funcionamento da aplicação.