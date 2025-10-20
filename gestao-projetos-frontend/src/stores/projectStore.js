import { defineStore } from 'pinia';
import { projectService } from '@/services/projectService';
import { ref } from 'vue';

export const useProjectStore = defineStore('projects', () => {
  const projects = ref([]);
  const currentProject = ref(null);
  const isLoading = ref(false);
  const error = ref(null);

  async function fetchProjects() {
    isLoading.value = true;
    error.value = null;

    try {
      projects.value = await projectService.getProjects();
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao carregar projetos';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function createProject(projectData) {
    isLoading.value = true;
    error.value = null;

    try {
      const newProject = await projectService.createProject(projectData);
      projects.value.push(newProject);
      return newProject;
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao criar projeto';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function fetchProject(id) {
    isLoading.value = true;
    error.value = null;

    try {
      currentProject.value = await projectService.getProject(id);
      return currentProject.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao carregar projeto';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function updateProject(id, projectData) {
    isLoading.value = true;
    error.value = null;

    try {
      const updatedProject = await projectService.updateProject(id, projectData);

      const index = projects.value.findIndex(p => p.id === id);
      if (index !== -1) {
        projects.value[index] = updatedProject;
      }

      if (currentProject.value && currentProject.value.id === id) {
        currentProject.value = updatedProject;
      }

      return updatedProject;
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao atualizar projeto';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function deleteProject(id) {
    isLoading.value = true;
    error.value = null;

    try {
      await projectService.deleteProject(id);

      // Remove da lista
      projects.value = projects.value.filter(p => p.id !== id);

      // Limpa projeto atual se for o mesmo
      if (currentProject.value && currentProject.value.id === id) {
        currentProject.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao deletar projeto';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  function clearError() {
    error.value = null;
  }

  function clearCurrentProject() {
    currentProject.value = null;
  }

  return {
    projects,
    currentProject,
    isLoading,
    error,
    fetchProjects,
    createProject,
    fetchProject,
    updateProject,
    deleteProject,
    clearError,
    clearCurrentProject
  };
});
