<template>
  <div class="dashboard container mt-4">
    <!-- Cabeçalho -->
    <div class="welcome-card mb-5 p-4 rounded-3 bg-light border-start border-primary border-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h2 fw-bold text-dark mb-2">Olá, {{ userName }}! 👋</h1>
          <p class="text-muted mb-0">
            Aqui está o resumo do seu dia.
            <span v-if="hasActiveProjects" class="text-success">Bom trabalho!</span>
            <span v-else>Vamos começar?</span>
          </p>
        </div>
        <div
          class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
          :title="authStore.user?.name"
        >
          {{ userInitials }}
        </div>
      </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="mb-5">
      <h3 class="h5 mb-3 text-secondary">
        <i class="bi bi-speedometer2 me-2"></i>Como estão as coisas?
      </h3>

      <div class="row g-3">
        <!-- Projetos Ativos -->
        <div class="col-lg-3 col-md-6">
          <div class="card stats-card project-card h-100"
               :class="{ 'loading': !reports.general }"
               @click="goToProjects">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-kanban text-primary fs-4 me-2"></i>
                <span class="fw-semibold text-dark">Projetos Ativos</span>
              </div>
              <div class="number display-6 fw-bold text-dark">
                {{ reports.general?.active_projects || '--' }}
              </div>
              <small class="text-muted">de {{ reports.general?.total_projects || 0 }} no total</small>
            </div>
          </div>
        </div>

        <!-- Tarefas Pendentes -->
        <div class="col-lg-3 col-md-6">
          <div class="card stats-card task-card h-100"
               :class="{ 'loading': !reports.general, 'urgent': hasPendingTasks }">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-list-check text-warning fs-4 me-2"></i>
                <span class="fw-semibold text-dark">Pendentes</span>
              </div>
              <div class="number display-6 fw-bold text-dark">
                {{ reports.general?.pending_tasks || '--' }}
              </div>
              <small v-if="hasPendingTasks" class="text-warning">
                <i class="bi bi-exclamation-circle me-1"></i>Precisa de atenção
              </small>
              <small v-else class="text-muted">Tudo em dia!</small>
            </div>
          </div>
        </div>

        <!-- Tarefas Concluídas -->
        <div class="col-lg-3 col-md-6">
          <div class="card stats-card done-card h-100"
               :class="{ 'loading': !reports.general }">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-check2-circle text-success fs-4 me-2"></i>
                <span class="fw-semibold text-dark">Concluídas</span>
              </div>
              <div class="number display-6 fw-bold text-dark">
                {{ reports.general?.completed_tasks || '--' }}
              </div>
              <small class="text-muted">
                <span v-if="reports.general?.completed_tasks > 0">🎉 Bom trabalho!</span>
                <span v-else>Vamos lá!</span>
              </small>
            </div>
          </div>
        </div>

        <!-- Ação Rápida -->
        <div class="col-lg-3 col-md-6">
          <div class="card quick-action-card h-100" @click="quickAction">
            <div class="card-body text-center d-flex flex-column justify-content-center">
              <i class="bi bi-plus-circle display-6 text-primary mb-2"></i>
              <span class="fw-semibold">Adicionar</span>
              <small class="text-muted">Projeto ou tarefa</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Menu Rápido -->
    <div class="mb-4">
      <h3 class="h5 mb-3 text-secondary">Atalhos</h3>
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-outline-primary btn-sm" @click="goToProjects">
          <i class="bi bi-folder me-1"></i>Meus Projetos
        </button>
        <button class="btn btn-outline-success btn-sm" @click="goToTasks">
          <i class="bi bi-check2-square me-1"></i>Minhas Tarefas
        </button>
        <button class="btn btn-outline-info btn-sm" @click="goToReports">
          <i class="bi bi-graph-up me-1"></i>Relatórios
        </button>
        <button class="btn btn-outline-secondary btn-sm" @click="refreshData" :disabled="isLoading">
          <i class="bi bi-arrow-clockwise me-1" :class="{ 'spin': isLoading }"></i>
          {{ isLoading ? 'Atualizando...' : 'Atualizar' }}
        </button>
      </div>
    </div>

    <!-- Estados -->
    <div v-if="isLoading && !reports.general" class="text-center py-5">
      <div class="spinner-border text-primary mb-3" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
      <p class="text-muted">Carregando seus dados...</p>
    </div>

    <div v-if="error" class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <strong>Ops!</strong> {{ error }}
      <button type="button" class="btn-close" @click="clearError"></button>
    </div>

    <!-- Timestamp -->
    <div v-if="reports.general" class="text-muted small mt-4">
      <i class="bi bi-clock me-1"></i>
      Atualizado às {{ lastUpdate }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { reportService } from '@/services/reportService';

const router = useRouter();
const authStore = useAuthStore();

const reports = ref({ general: null });
const isLoading = ref(false);
const error = ref('');
const lastUpdate = ref('');

const userName = computed(() => authStore.user?.name?.split(' ')[0] || 'Colega');
const userInitials = computed(() => {
  const name = authStore.user?.name;
  if (!name) return '?';

  return name
    .split(' ')
    .slice(0, 2)
    .map(n => n[0])
    .join('')
    .toUpperCase();
});

const hasActiveProjects = computed(() => (reports.value.general?.active_projects || 0) > 0);
const hasPendingTasks = computed(() => (reports.value.general?.pending_tasks || 0) > 5);

onMounted(() => {
  loadData();
});

async function loadData() {
  isLoading.value = true;
  error.value = '';

  try {
    const data = await reportService.getGeneralReport();
    reports.value.general = data;
    lastUpdate.value = new Date().toLocaleTimeString('pt-BR', {
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (err) {
    console.error('Deu pau ao carregar os dados:', err);
    error.value = err.message || 'Não consegui carregar os dados. Tenta de novo?';
  } finally {
    isLoading.value = false;
  }
}

function refreshData() {
  if (!isLoading.value) {
    loadData();
  }
}

function clearError() {
  error.value = '';
}

function goToProjects() {
  router.push('/projects');
}

function goToTasks() {
  router.push('/tasks');
}

function goToReports() {
  router.push('/reports');
}

function quickAction() {
  if (!hasActiveProjects.value) {
    goToProjects();
  } else {
    goToTasks();
  }
}
</script>

<style scoped>
.dashboard {
  max-width: 1200px;
}

.welcome-card {
  background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
}

.avatar {
  width: 70px;
  height: 70px;
  font-size: 1.4rem;
  box-shadow: 0 3px 10px rgba(0, 123, 255, 0.2);
}

.stats-card {
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid #e9ecef;
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-color: #007bff;
}

.stats-card.urgent {
  border-left: 4px solid #ffc107;
  background: #fffbf0;
}

.quick-action-card {
  cursor: pointer;
  border: 2px dashed #dee2e6;
  transition: all 0.2s ease;
  background: #fafafa;
}

.quick-action-card:hover {
  border-color: #007bff;
  background: #f0f8ff;
  transform: scale(1.02);
}

.stats-card.loading .number {
  color: #e9ecef;
  background: #e9ecef;
  border-radius: 4px;
  min-height: 60px;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .welcome-card .d-flex {
    flex-direction: column;
    text-align: center;
  }

  .avatar {
    margin-top: 1rem;
    width: 60px;
    height: 60px;
    font-size: 1.2rem;
  }

  .display-6 {
    font-size: 2rem;
  }
}

@media (max-width: 576px) {
  .d-flex.flex-wrap {
    justify-content: center;
  }

  .btn-sm {
    font-size: 0.8rem;
  }
}
</style>
