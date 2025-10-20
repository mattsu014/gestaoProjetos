<template>
  <div class="project-details-container">
    <div v-if="projectStore.isLoading" class="loading">Carregando...</div>

    <div v-else-if="projectStore.currentProject" class="project-details">
      <div class="project-header">
        <div>
          <h1>{{ projectStore.currentProject.title }}</h1>
          <p class="project-description">{{ projectStore.currentProject.description }}</p>
          <div class="project-meta">
            <span><strong>Status:</strong> {{ getStatusText(projectStore.currentProject.status) }}</span>
            <span><strong>Início:</strong> {{ formatDate(projectStore.currentProject.start_date) }}</span>
            <span v-if="projectStore.currentProject.end_date">
              <strong>Término:</strong> {{ formatDate(projectStore.currentProject.end_date) }}
            </span>
          </div>
        </div>
        <div class="header-actions">
          <router-link to="/projects" class="btn-secondary">← Voltar</router-link>
        </div>
      </div>

      <div class="tasks-section">
        <div class="tasks-header">
          <h2>Tarefas</h2>
          <button @click="showTaskModal = true" class="btn-primary">
            Nova Tarefa
          </button>
        </div>

        <div v-if="showTaskModal" class="modal-overlay">
          <div class="modal">
            <h3>Nova Tarefa</h3>
            <form @submit.prevent="createTask">
              <div>
                <label>Título:</label>
                <input v-model="taskForm.title" type="text" required>
              </div>
              <div>
                <label>Descrição:</label>
                <textarea v-model="taskForm.description" rows="3"></textarea>
              </div>
              <div>
                <label>Status:</label>
                <select v-model="taskForm.status">
                  <option value="pending">Pendente</option>
                  <option value="in_progress">Em Andamento</option>
                  <option value="completed">Concluída</option>
                </select>
              </div>
              <div>
                <label>Prazo:</label>
                <input v-model="taskForm.due_date" type="date">
              </div>
              <div class="modal-actions">
                <button type="button" @click="showTaskModal = false">Cancelar</button>
                <button type="submit" :disabled="isLoading">
                  {{ isLoading ? 'Salvando...' : 'Salvar' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <div v-if="tasks.length === 0" class="empty-state">
          Nenhuma tarefa encontrada para este projeto.
        </div>

        <div v-else class="tasks-list">
          <div
            v-for="task in tasks"
            :key="task.id"
            class="task-card"
            :class="`task-${task.status}`"
          >
            <div class="task-main">
              <h4>{{ task.title }}</h4>
              <p class="task-description">{{ task.description }}</p>
              <div class="task-meta">
                <span class="task-status">{{ getTaskStatusText(task.status) }}</span>
                <span v-if="task.due_date" class="task-due">
                  Prazo: {{ formatDate(task.due_date) }}
                </span>
              </div>
            </div>
            <div class="task-actions">
              <button
                @click="updateTaskStatus(task.id, 'completed')"
                v-if="task.status !== 'completed'"
                class="btn-success"
              >
                Concluir
              </button>
              <button @click="deleteTask(task.id)" class="btn-danger">
                Excluir
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p v-if="projectStore.error" class="error">{{ projectStore.error }}</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useProjectStore } from '@/stores/projectStore';
import { taskService } from '@/services/taskService';

const route = useRoute();
const router = useRouter();
const projectStore = useProjectStore();

const showTaskModal = ref(false);
const isLoading = ref(false);
const tasks = ref([]);

const taskForm = ref({
  title: '',
  description: '',
  status: 'pending',
  due_date: '',
  project_id: route.params.id
});

const projectId = computed(() => route.params.id);

onMounted(async () => {
  await loadProjectAndTasks();
});

async function loadProjectAndTasks() {
  try {
    await projectStore.fetchProject(projectId.value);
    await loadTasks();
  } catch (error) {
    console.error('Erro ao carregar projeto:', error);
  }
}

async function loadTasks() {
  try {
    const allTasks = await taskService.getTasks();
    tasks.value = allTasks.filter(task => task.project_id == projectId.value);
  } catch (error) {
    console.error('Erro ao carregar tarefas:', error);
  }
}

function getStatusText(status) {
  const statusMap = {
    active: 'Ativo',
    completed: 'Concluído',
    cancelled: 'Cancelado'
  };
  return statusMap[status] || status;
}

function getTaskStatusText(status) {
  const statusMap = {
    pending: 'Pendente',
    in_progress: 'Em Andamento',
    completed: 'Concluída'
  };
  return statusMap[status] || status;
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('pt-BR');
}

async function createTask() {
  isLoading.value = true;
  try {
    await taskService.createTask(taskForm.value);
    showTaskModal.value = false;
    taskForm.value = {
      title: '',
      description: '',
      status: 'pending',
      due_date: '',
      project_id: projectId.value
    };
    await loadTasks();
  } catch (error) {
    console.error('Erro ao criar tarefa:', error);
  } finally {
    isLoading.value = false;
  }
}

async function updateTaskStatus(taskId, status) {
  try {
    await taskService.updateTask(taskId, { status });
    await loadTasks();
  } catch (error) {
    console.error('Erro ao atualizar tarefa:', error);
  }
}

async function deleteTask(taskId) {
  if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
    try {
      await taskService.deleteTask(taskId);
      await loadTasks();
    } catch (error) {
      console.error('Erro ao excluir tarefa:', error);
    }
  }
}
</script>

<style scoped>
.project-details-container {
  max-width: 1000px;
  margin: 0 auto;
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #ddd;
}

.project-meta {
  display: flex;
  gap: 20px;
  margin-top: 10px;
  color: #666;
}

.tasks-section {
  margin-top: 30px;
}

.tasks-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.tasks-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.task-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: white;
}

.task-card.task-completed {
  opacity: 0.7;
  background-color: #f8f9fa;
}

.task-main {
  flex: 1;
}

.task-main h4 {
  margin: 0 0 5px 0;
  color: #333;
}

.task-description {
  color: #666;
  margin: 0 0 10px 0;
}

.task-meta {
  display: flex;
  gap: 15px;
  font-size: 14px;
}

.task-status {
  padding: 2px 8px;
  border-radius: 4px;
  background: #e9ecef;
  color: #495057;
}

.task-actions {
  display: flex;
  gap: 10px;
}

.btn-success {
  background: #28a745;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  text-decoration: none;
  display: inline-block;
}

</style>
