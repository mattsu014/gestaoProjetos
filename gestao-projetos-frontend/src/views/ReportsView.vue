<template>
  <div class="reports">
    <div class="header">
      <h1>Relatórios</h1>
      <button @click="refreshData" class="refresh-btn" :disabled="loading">
        ↻ Atualizar
      </button>
    </div>

    <div class="reports-content">
      <!-- Card de Resumo Geral -->
      <div class="report-card summary">
        <h2>Visão Geral</h2>
        <div v-if="!reports.general" class="loading-placeholder">
          <div class="ph"></div>
          <div class="ph"></div>
        </div>
        <div v-else class="numbers">
          <div class="number-item">
            <span class="big">{{ reports.general.total_projects || 0 }}</span>
            <small>Projetos no total</small>
          </div>
          <div class="number-item highlight">
            <span class="big">{{ reports.general.active_projects || 0 }}</span>
            <small>Ativos</small>
          </div>
          <div class="number-item">
            <span class="big">{{ reports.general.total_tasks || 0 }}</span>
            <small>Tarefas</small>
          </div>
          <div class="number-item done">
            <span class="big">{{ reports.general.completed_tasks || 0 }}</span>
            <small>Concluídas</small>
          </div>
        </div>
      </div>

      <!-- Tarefas por Projeto -->
      <div class="report-card projects">
        <h2>Andamento por Projeto</h2>

        <div v-if="!reports.tasksByProject" class="loading-bars">
          <div v-for="i in 3" :key="i" class="loading-bar"></div>
        </div>

        <template v-else>
          <div v-if="reports.tasksByProject.length === 0" class="no-data">
            <span>📁</span>
            <p>Nenhum projeto com tarefas ainda</p>
          </div>

          <div v-else class="project-list">
            <div
              v-for="project in reports.tasksByProject"
              :key="project.id"
              class="project-item"
              @click="goToProject(project.id)"
            >
              <div class="project-main">
                <h4 class="project-title">{{ project.title }}</h4>
                <span class="project-badge" :class="project.status">
                  {{ projectStatus(project.status) }}
                </span>
              </div>

              <div class="project-stats">
                <div class="progress">
                  <div
                    class="progress-bar"
                    :style="{ width: getProgress(project) + '%' }"
                  ></div>
                </div>
                <div class="task-numbers">
                  <span>{{ project.completed_tasks || 0 }}/{{ project.total_tasks || 0 }} concluídas</span>
                  <strong>{{ getProgress(project) }}%</strong>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- Tarefas Pendentes -->
      <div class="report-card pending">
        <div class="card-header">
          <h2>Tarefas em Aberto</h2>
          <select
            v-model="selectedProject"
            @change="filterPendingTasks"
            class="project-filter"
          >
            <option value="">Todos os projetos</option>
            <option
              v-for="p in availableProjects"
              :key="p.id"
              :value="p.id"
            >
              {{ p.title }}
            </option>
          </select>
        </div>

        <div v-if="loadingPending" class="loading-text">Buscando tarefas...</div>

        <div v-else-if="!reports.pendingTasks" class="loading-bars">
          <div v-for="i in 2" :key="i" class="loading-bar short"></div>
        </div>

        <template v-else>
          <div v-if="reports.pendingTasks.length === 0" class="no-data success">
            <span>🎉</span>
            <p>Tudo em dia! Não há tarefas pendentes</p>
          </div>

          <div v-else class="task-list">
            <div
              v-for="task in reports.pendingTasks"
              :key="task.id"
              class="task-item"
              :class="{ overdue: isOverdue(task.due_date) }"
            >
              <div class="task-main">
                <h4 class="task-title">{{ task.title }}</h4>
                <p class="task-project" v-if="task.project">
                  {{ task.project.title }}
                </p>
                <div class="task-dates">
                  <span v-if="task.due_date" class="due-date">
                    📅 {{ formatDate(task.due_date) }}
                    <span v-if="isOverdue(task.due_date)" class="urgent">- URGENTE</span>
                  </span>
                </div>
              </div>
              <div class="task-actions">
                <span class="status" :class="task.status">
                  {{ taskStatus(task.status) }}
                </span>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { reportService } from '@/services/reportService'
import { projectService } from '@/services/projectService'

const router = useRouter()

// Estado mais "humano" - menos estruturado, mais direto
const reports = ref({})
const availableProjects = ref([])
const selectedProject = ref('')
const loading = ref(false)
const loadingPending = ref(false)

onMounted(() => {
  loadInitialData()
})

async function loadInitialData() {
  loading.value = true
  try {
    // Carrega dados básicos primeiro
    const [generalData, projectsData] = await Promise.all([
      reportService.getGeneralReport(),
      projectService.getProjects()
    ])

    reports.value.general = generalData
    availableProjects.value = projectsData

    // Depois carrega o resto
    await loadAdditionalReports()

  } catch (err) {
    console.error('Deu erro ao carregar os dados:', err)
  } finally {
    loading.value = false
  }
}

async function loadAdditionalReports() {
  try {
    const [tasksByProject, pendingTasks] = await Promise.all([
      reportService.getTasksByProject(),
      reportService.getPendingTasks()
    ])

    reports.value.tasksByProject = tasksByProject
    reports.value.pendingTasks = pendingTasks
  } catch (err) {
    console.error('Erro nos relatórios detalhados:', err)
  }
}

async function refreshData() {
  loading.value = true
  try {
    await loadAdditionalReports()
  } finally {
    loading.value = false
  }
}

async function filterPendingTasks() {
  loadingPending.value = true
  try {
    reports.value.pendingTasks = await reportService.getPendingTasks(
      selectedProject.value || undefined
    )
  } catch (err) {
    console.error('Erro ao filtrar:', err)
  } finally {
    loadingPending.value = false
  }
}

function getProgress(project) {
  const total = project.total_tasks || 0
  const completed = project.completed_tasks || 0
  return total > 0 ? Math.round((completed / total) * 100) : 0
}

function projectStatus(status) {
  const statusMap = {
    active: 'Ativo',
    completed: 'Finalizado',
    cancelled: 'Cancelado',
    planning: 'Planejamento'
  }
  return statusMap[status] || status
}

function taskStatus(status) {
  const statusMap = {
    pending: 'Pendente',
    in_progress: 'Fazendo',
    completed: 'Pronto'
  }
  return statusMap[status] || status
}

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('pt-BR')
}

function isOverdue(dueDate) {
  if (!dueDate) return false
  return new Date(dueDate) < new Date()
}

function goToProject(projectId) {
  router.push(`/projects/${projectId}`)
}
</script>

<style scoped>
.reports {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: between;
  align-items: center;
  margin-bottom: 30px;
  gap: 20px;
}

.header h1 {
  margin: 0;
  color: #2c3e50;
}

.refresh-btn {
  padding: 8px 16px;
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.refresh-btn:hover:not(:disabled) {
  background: #e9ecef;
}

.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.reports-content {
  display: grid;
  gap: 24px;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
}

.report-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border: 1px solid #eaeaea;
}

.report-card h2 {
  margin: 0 0 20px 0;
  color: #2c3e50;
  font-size: 1.2em;
}

/* Summary Card */
.numbers {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.number-item {
  text-align: center;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid #6c757d;
}

.number-item.highlight {
  background: #e7f1ff;
  border-left-color: #007bff;
}

.number-item.done {
  background: #e8f5e8;
  border-left-color: #28a745;
}

.big {
  font-size: 2em;
  font-weight: bold;
  display: block;
  color: #2c3e50;
}

.number-item.highlight .big {
  color: #007bff;
}

.number-item.done .big {
  color: #28a745;
}

/* Projects Card */
.project-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.project-item {
  padding: 16px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.project-item:hover {
  border-color: #007bff;
  background: #f8f9fa;
}

.project-main {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 12px;
}

.project-title {
  margin: 0;
  font-size: 1em;
  color: #2c3e50;
}

.project-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8em;
  font-weight: 500;
}

.project-badge.active {
  background: #d4edda;
  color: #155724;
}

.project-badge.completed {
  background: #d1ecf1;
  color: #0c5460;
}

.project-badge.cancelled {
  background: #f8d7da;
  color: #721c24;
}

.progress {
  height: 6px;
  background: #e9ecef;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-bar {
  height: 100%;
  background: #28a745;
  transition: width 0.3s ease;
}

.task-numbers {
  display: flex;
  justify-content: space-between;
  font-size: 0.9em;
  color: #6c757d;
}

/* Pending Tasks Card */
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  gap: 16px;
}

.project-filter {
  padding: 6px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: white;
}

.task-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.task-item {
  display: flex;
  justify-content: space-between;
  align-items: start;
  padding: 16px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  background: #f8f9fa;
}

.task-item.overdue {
  border-color: #dc3545;
  background: #fff5f5;
}

.task-main {
  flex: 1;
}

.task-title {
  margin: 0 0 8px 0;
  color: #2c3e50;
  font-size: 1em;
}

.task-project {
  margin: 0 0 8px 0;
  color: #6c757d;
  font-size: 0.9em;
}

.task-dates {
  font-size: 0.85em;
  color: #6c757d;
}

.due-date .urgent {
  color: #dc3545;
  font-weight: bold;
}

.status {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8em;
  font-weight: 500;
}

.status.pending {
  background: #fff3cd;
  color: #856404;
}

.status.in_progress {
  background: #cce7ff;
  color: #004085;
}

.status.completed {
  background: #d4edda;
  color: #155724;
}

/* Estados de loading e vazio */
.loading-placeholder .ph {
  height: 60px;
  background: #f8f9fa;
  border-radius: 8px;
  margin-bottom: 10px;
}

.loading-bars {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.loading-bar {
  height: 40px;
  background: #f8f9fa;
  border-radius: 6px;
}

.loading-bar.short {
  height: 60px;
}

.loading-text {
  text-align: center;
  color: #6c757d;
  padding: 20px;
}

.no-data {
  text-align: center;
  padding: 40px 20px;
  color: #6c757d;
}

.no-data span {
  font-size: 2em;
  display: block;
  margin-bottom: 12px;
}

.no-data.success {
  color: #28a745;
}

@media (max-width: 768px) {
  .reports-content {
    grid-template-columns: 1fr;
  }

  .header {
    flex-direction: column;
    align-items: stretch;
  }

  .card-header {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
