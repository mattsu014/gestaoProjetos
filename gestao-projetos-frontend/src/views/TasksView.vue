<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Minhas Tarefas</h1>
      <button @click="showCreateModal = true" class="btn btn-primary btn-sm">
        + Nova Tarefa
      </button>
    </div>

    <div class="card mb-3">
      <div class="card-body py-3">
        <div class="row g-2">
          <div class="col-md-3">
            <select v-model="filters.status" @change="loadTasks" class="form-select form-select-sm">
              <option value="">Todos status</option>
              <option value="pending">Pendente</option>
              <option value="in_progress">Andamento</option>
              <option value="completed">Concluída</option>
            </select>
          </div>
          <div class="col-md-4">
            <select v-model="filters.project_id" @change="loadTasks" class="form-select form-select-sm">
              <option value="">Todos projetos</option>
              <option v-for="project in projects" :key="project.id" :value="project.id">
                {{ project.title }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <button @click="clearFilters" class="btn btn-outline-secondary btn-sm w-100">
              Limpar
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCreateModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nova Tarefa</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Título *</label>
              <input v-model="taskForm.title" type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Descrição</label>
              <textarea v-model="taskForm.description" rows="2" class="form-control"></textarea>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label">Projeto *</label>
                <select v-model="taskForm.project_id" class="form-select" required>
                  <option value="">Selecione...</option>
                  <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.title }}
                  </option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label">Status</label>
                <select v-model="taskForm.status" class="form-select">
                  <option value="pending">Pendente</option>
                  <option value="in_progress">Andamento</option>
                  <option value="completed">Concluída</option>
                </select>
              </div>
            </div>
            <div class="mt-3">
              <label class="form-label">Prazo</label>
              <input v-model="taskForm.due_date" type="date" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="createTask" :disabled="isCreating">
              <span v-if="isCreating" class="spinner-border spinner-border-sm me-1"></span>
              {{ isCreating ? 'Criando...' : 'Criar' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-4">
      <div class="spinner-border text-primary"></div>
      <p class="mt-2 text-muted">Carregando...</p>
    </div>

    <div v-else-if="tasks.length === 0" class="text-center py-5">
      <div class="text-muted">
        <i class="bi bi-inbox display-6"></i>
        <p class="mt-2">Nenhuma tarefa por aqui</p>
        <button @click="showCreateModal = true" class="btn btn-primary btn-sm">
          Criar primeira tarefa
        </button>
      </div>
    </div>

    <div v-else class="tasks">
      <div class="row g-2">
        <div v-for="task in tasks" :key="task.id" class="col-12">
          <div class="card task-card">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center mb-1">
                    <h6 class="mb-0 me-2">{{ task.title }}</h6>
                    <span class="badge" :class="statusClass(task.status)">
                      {{ getStatusText(task.status) }}
                    </span>
                  </div>

                  <p v-if="task.description" class="small text-muted mb-2">
                    {{ task.description }}
                  </p>

                  <div class="small text-muted">
                    <span class="me-3">
                      <i class="bi bi-folder me-1"></i>
                      {{ task.project?.title || 'Sem projeto' }}
                    </span>
                    <span v-if="task.due_date" class="me-3">
                      <i class="bi bi-calendar me-1"></i>
                      {{ formatDate(task.due_date) }}
                    </span>
                  </div>
                </div>

                <div class="d-flex gap-1 ms-3">
                  <select
                    v-model="task.status"
                    @change="updateTaskStatus(task.id, task.status)"
                    class="form-select form-select-sm"
                    style="width: 130px;"
                  >
                    <option value="pending">Pendente</option>
                    <option value="in_progress">Andamento</option>
                    <option value="completed">Concluída</option>
                  </select>

                  <button
                    @click="deleteTask(task.id)"
                    class="btn btn-outline-danger btn-sm"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="error" class="alert alert-danger alert-dismissible fade show mt-3">
      {{ error }}
      <button type="button" class="btn-close" @click="error = ''"></button>
    </div>

    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show mt-3">
      {{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = ''"></button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { taskService } from '@/services/taskService'
import { projectService } from '@/services/projectService'

const isLoading = ref(false)
const isCreating = ref(false)
const showCreateModal = ref(false)
const tasks = ref([])
const projects = ref([])
const error = ref('')
const successMessage = ref('')

const filters = ref({
  status: '',
  project_id: ''
})

const taskForm = ref({
  title: '',
  description: '',
  status: 'pending',
  due_date: '',
  project_id: ''
})

onMounted(async () => {
  await loadProjects()
  await loadTasks()
})

async function loadProjects() {
  try {
    projects.value = await projectService.getProjects()
  } catch (err) {
    console.error('Erro projetos:', err)
    showError('Erro ao carregar projetos')
  }
}

async function loadTasks() {
  isLoading.value = true
  error.value = ''

  try {
    const allTasks = await taskService.getTasks()
    let filteredTasks = allTasks

    if (filters.value.status) {
      filteredTasks = filteredTasks.filter(task => task.status === filters.value.status)
    }

    if (filters.value.project_id) {
      filteredTasks = filteredTasks.filter(task => task.project_id == filters.value.project_id)
    }

    tasks.value = filteredTasks
  } catch (err) {
    console.error('Erro tarefas:', err)
    showError('Erro ao carregar tarefas')
  } finally {
    isLoading.value = false
  }
}

async function createTask() {
  if (!taskForm.value.title || !taskForm.value.project_id) {
    showError('Preencha título e projeto')
    return
  }

  isCreating.value = true
  error.value = ''

  try {
    await taskService.createTask(taskForm.value)
    successMessage.value = 'Tarefa criada!'
    closeModal()
    resetTaskForm()
    await loadTasks()
  } catch (err) {
    console.error('Erro criar:', err)
    showError(err.response?.data?.message || 'Erro ao criar')
  } finally {
    isCreating.value = false
  }
}

async function updateTaskStatus(taskId, status) {
  try {
    await taskService.updateTask(taskId, { status })
    successMessage.value = 'Status atualizado!'
  } catch (err) {
    console.error('Erro atualizar:', err)
    showError('Erro ao atualizar')
    await loadTasks()
  }
}

async function deleteTask(taskId) {
  if (!confirm('Excluir esta tarefa?')) return

  try {
    await taskService.deleteTask(taskId)
    successMessage.value = 'Tarefa excluída!'
    await loadTasks()
  } catch (err) {
    console.error('Erro excluir:', err)
    showError('Erro ao excluir')
  }
}

function getStatusText(status) {
  const statusMap = {
    pending: 'Pendente',
    in_progress: 'Andamento',
    completed: 'Concluída'
  }
  return statusMap[status] || status
}

function statusClass(status) {
  return {
    'bg-primary': status === 'pending',
    'bg-warning': status === 'in_progress',
    'bg-success': status === 'completed'
  }
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('pt-BR')
}

function showError(message) {
  error.value = message
  setTimeout(() => error.value = '', 5000)
}

function closeModal() {
  showCreateModal.value = false
  resetTaskForm()
  error.value = ''
}

function resetTaskForm() {
  taskForm.value = {
    title: '',
    description: '',
    status: 'pending',
    due_date: '',
    project_id: ''
  }
}

function clearFilters() {
  filters.value = { status: '', project_id: '' }
  loadTasks()
}
</script>

<style scoped>
.task-card {
  transition: all 0.2s ease;
  border-left: 4px solid #6c757d;
}

.task-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.task-card .border-left-primary { border-left-color: #007bff; }
.task-card .border-left-warning { border-left-color: #ffc107; }
.task-card .border-left-success { border-left-color: #28a745; }
</style>
