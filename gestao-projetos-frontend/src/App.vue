<template>
  <div id="app">
    <nav v-if="authStore.isAuthenticated" class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
      <div class="container">
        <router-link to="/" class="navbar-brand d-flex align-items-center fw-bold text-decoration-none">
          <span class="gradient-text">Gestão de Projetos</span>
        </router-link>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link to="/" class="nav-link d-flex align-items-center">
                <span>Dashboard</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/projects" class="nav-link d-flex align-items-center">
                <span>Projetos</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/tasks" class="nav-link d-flex align-items-center">
                <span>Tarefas</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/reports" class="nav-link d-flex align-items-center">
                <span>Relatórios</span>
              </router-link>
            </li>
          </ul>

          <div class="navbar-nav align-items-center">
            <div class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle d-flex align-items-center"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                  U
                </div>
                <span class="d-none d-md-inline">Usuário</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <button @click="handleLogout" class="dropdown-item d-flex align-items-center text-danger">
                    <span>Sair</span>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <main :class="{ 'with-nav': authStore.isAuthenticated }">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

function handleLogout() {
  authStore.logout();
  router.push('/login');
}
</script>

<style scoped>

.gradient-text {
  background: linear-gradient(135deg, #007bff, #0056b3);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.navbar-brand {
  font-size: 1.25rem;
}

.nav-link {
  color: #666 !important;
  font-weight: 500;
  padding: 0.5rem 1rem !important;
  border-radius: 0.375rem;
  margin: 0 0.125rem;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: #007bff !important;
  background-color: #f8f9fa;
  transform: translateY(-1px);
}

.nav-link.router-link-active {
  color: #fff !important;
  background-color: #007bff;
  box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
}

.user-avatar {
  width: 32px;
  height: 32px;
  font-size: 0.875rem;
  font-weight: 600;
}

.dropdown-item {
  cursor: pointer;
}

.dropdown-item:active {
  background-color: #e9ecef;
}

@media (max-width: 991.98px) {
  .navbar-collapse {
    margin-top: 1rem;
  }

  .nav-link {
    margin: 0.125rem 0;
  }

  .dropdown-menu {
    border: none;
    box-shadow: none;
  }
}

.nav-link:focus-visible,
.navbar-toggler:focus-visible,
.dropdown-toggle:focus-visible {
  outline: 2px solid #007bff;
  outline-offset: 2px;
}

.navbar {
  transition: all 0.3s ease;
}

.nav-link {
  transition: all 0.3s ease;
}

main {
  min-height: calc(100vh - 76px);
  background-color: #f8f9fa;
}

main.with-nav {
  padding: 0;
}
</style>
