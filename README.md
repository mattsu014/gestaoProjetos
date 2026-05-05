# 📊 Project Management System

A complete project management system designed to organize and track tasks, enabling efficient control of activities, teams, and progress.

The application features a Laravel backend and a Vue.js frontend, providing a modern, responsive interface integrated with a RESTful API.

## 🌎️ Languages
- [Português (Brasil) 🇧🇷](./readme-pt-br.md)
## 🏗️ Architecture

* **Backend**: Laravel 12 + MySQL/MariaDB + Sanctum Auth
* **Frontend**: Vue.js 3 + Vite + Pinia + Vue Router
* **API**: RESTful with token-based authentication
* **Database**: MySQL/MariaDB

## 🚀 Features

* ✅ User authentication (register and login)
* ✅ Full CRUD for projects
* ✅ Full CRUD for tasks (linked to projects)
* ✅ Detailed reports
* ✅ Responsive interface
* ✅ Frontend and backend validations

## 📋 Prerequisites

### For Arch Linux:

```bash
sudo pacman -S php php-fpm composer
sudo pacman -S mariadb
sudo systemctl start mariadb
sudo systemctl enable mariadb
sudo mysql_secure_installation
sudo pacman -S nodejs npm
```

### For Windows:

1. XAMPP (PHP + MySQL)
2. Composer
3. Node.js

## 🛠️ Installation and Setup

### 1. Clone the Repository

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

#### Database Setup:

Create a database named `gestaoprojetos` and configure the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestaoprojetos
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

```bash
php artisan migrate
php artisan serve
```

Backend running at: http://localhost:8000

### 3. Frontend (Vue.js)

```bash
cd gestao-projetos-frontend
npm install
npm run dev
```

Frontend running at: http://localhost:5173

## 🗃️ Database Structure

* `users`
* `projects`
* `tasks`
* `personal_access_tokens`

## 🔐 API Endpoints

### Authentication

* POST /api/register
* POST /api/login
* GET /api/user

### Projects

* GET /api/projects
* POST /api/projects
* GET /api/projects/{id}
* PUT /api/projects/{id}
* DELETE /api/projects/{id}

### Tasks

* GET /api/tasks
* POST /api/tasks
* GET /api/tasks/{id}
* PUT /api/tasks/{id}
* DELETE /api/tasks/{id}

### Reports

* GET /api/reports/general
* GET /api/reports/tasks-by-project
* GET /api/reports/pending-tasks

## 🧪 Testing

### Web Interface

1. Access the frontend
2. Create an account
3. Log in
4. Manage projects and tasks

### API (Postman/Insomnia)

```bash
POST /api/register
POST /api/login
```

Use the returned token:

```
Authorization: Bearer {token}
```

## 📱 Application Usage

* Create and manage projects
* Add tasks linked to projects
* Update task status
* View reports and metrics

## 🎯 Implemented Features

* Authentication system
* Project and task CRUD
* Analytical reports
* Responsive interface
* State management with Pinia
* Routing with Vue Router

## 📚 Technologies Used

* Laravel
* Vue.js
* MySQL
* REST API

## 👨‍💻 Author

Mateus Valentim
GitHub: https://github.com/mattsu014

## 📄 License

This project is free to use for educational and professional purposes.

---

💡 **Tip:** Make sure both backend and frontend servers are running simultaneously for the application to work properly.

