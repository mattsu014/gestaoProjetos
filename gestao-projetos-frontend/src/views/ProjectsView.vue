<template>
  <div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Meus Projetos</h1>
        <p class="text-muted mb-0 small">
          {{ projectStore.projects.length }} projetos
        </p>
      </div>
      <button
        @click="showModal = true"
        class="btn btn-primary btn-sm"
      >
        <i class="bi bi-plus me-1"></i>Novo
      </button>
    </div>

    <div class="modal fade" :class="{ 'show d-block': showModal }" v-if="showModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingProject ? 'Editar' : 'Novo' }} Projeto</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <form @submit.prevent="saveProject">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nome</label>
                <input
                  v-model="form.title"
                  type="text"
                  class="form-control form-control-sm"
                  required
                >
              </div>

              <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea
                  v-model="form.description"
                  class="form-control form-control-sm"
                  rows="2"
                ></textarea>
              </div>

              <div class="row">
                <div class="col-6">
                  <label class="form-label">Início</label>
                  <input
                    v-model="form.start_date"
                    type="date"
                    class="form-control form-control-sm"
                    required
                  >
                </div>
                <div class="col-6">
                  <label class="form-label">Fim</label>
                  <input
                    v-model="form.end_date"
                    type="date"
                    class="form-control form-control-sm"
                  >
                </div>
              </div>

              <div class="mt-3">
                <label class="form-label">Status</label>
                <select v-model="form.status" class="form-select form-select-sm">
                  <option value="active">Ativo</option>
                  <option value="completed">Concluído</option>
                  <option value="cancelled">Cancelado</option>
                </select>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" @click="closeModal">
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary btn-sm" :disabled="projectStore.isLoading">
                <span v-if="projectStore.isLoading" class="spinner-border spinner-border-sm me-1"></span>
                {{ editingProject ? 'Salvar' : 'Criar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal-backdrop fade show" v-if="showModal"></div>

    <div v-if="projectStore.isLoading && !projectStore.projects.length" class="text-center py-5">
      <div class="spinner-border spinner-border-sm text-primary"></div>
      <p class="text-muted mt-2 small">Carregando...</p>
    </div>

    <div v-else-if="!projectStore.projects.length" class="text-center py-5">
      <p class="text-muted mb-3">Nenhum projeto criado</p>
      <button @click="showModal = true" class="btn btn-primary btn-sm">
        Criar Projeto
      </button>
    </div>

    <div v-else class="row g-3">
      <div
        v-for="project in projectStore.projects"
        :key="project.id"
        class="col-md-6 col-lg-4"
      >
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h6 class="card-title mb-0">{{ project.title }}</h6>
              <span :class="`badge bg-${getStatusVariant(project.status)}`">
                {{ getStatusText(project.status) }}
              </span>
            </div>

            <p class="card-text small text-muted mb-3">
              {{ project.description || 'Sem descrição' }}
            </p>

            <div class="small text-muted">
              <div>Início: {{ formatDate(project.start_date) }}</div>
              <div v-if="project.end_date">Fim: {{ formatDate(project.end_date) }}</div>
            </div>
          </div>

          <div class="card-footer bg-white border-0 pt-0">
            <div class="d-flex gap-2">
              <router-link
                :to="`/projects/${project.id}`"
                class="btn btn-outline-primary btn-sm flex-fill"
              >
                Abrir
              </router-link>
              <button
                @click="editProject(project)"
                class="btn btn-outline-secondary btn-sm"
              >
                Editar
              </button>
              <button
                @click="deleteProject(project.id)"
                class="btn btn-outline-danger btn-sm"
              >
                Remover
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Erro -->
    <div v-if="projectStore.error" class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
      <small>{{ projectStore.error }}</small>
      <button type="button" class="btn-close btn-close-sm" @click="projectStore.error = null"></button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useProjectStore } from '@/stores/projectStore';

const projectStore = useProjectStore();

const showModal = ref(false);
const editingProject = ref(null);

const form = reactive({
  title: '',
  description: '',
  start_date: '',
  end_date: '',
  status: 'active'
});

onMounted(() => {
  projectStore.fetchProjects();
});

function getStatusText(status) {
  const statusMap = {
    active: 'Ativo',
    completed: 'Concluído',
    cancelled: 'Cancelado'
  };
  return statusMap[status] || status;
}

function getStatusVariant(status) {
  const variantMap = {
    active: 'primary',
    completed: 'success',
    cancelled: 'danger'
  };
  return variantMap[status] || 'secondary';
}

function formatDate(dateString) {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('pt-BR');
}

function editProject(project) {
  editingProject.value = project;
  Object.assign(form, project);
  showModal.value = true;
}

async function saveProject() {
  try {
    if (editingProject.value) {
      await projectStore.updateProject(editingProject.value.id, form);
    } else {
      await projectStore.createProject(form);
    }
    closeModal();
  } catch (error) {
    console.log('Erro:', error);
  }
}

async function deleteProject(id) {
  if (confirm('Deletar este projeto?')) {
    try {
      await projectStore.deleteProject(id);
    } catch (error) {
      console.log('Erro:', error);
    }
  }
}

function closeModal() {
  showModal.value = false;
  editingProject.value = null;
  Object.assign(form, {
    title: '',
    description: '',
    start_date: '',
    end_date: '',
    status: 'active'
  });
}
</script>

<style scoped>
.card {
  border: 1px solid #dee2e6;
  transition: box-shadow 0.2s;
}

.card:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-title {
  font-weight: 600;
}

.card-text {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.btn-sm {
  font-size: 0.8rem;
  padding: 0.25rem 0.5rem;
}
</style>
