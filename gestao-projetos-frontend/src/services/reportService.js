import api from './api';

export const reportService = {
  // GET /api/reports/tasks-by-project
  async getTasksByProject() {
    const response = await api.get('/reports/tasks-by-project');
    return response.data;
  },

  // GET /api/reports/pending-tasks
  async getPendingTasks(projectId = null) {
    const params = projectId ? { project_id: projectId } : {};
    const response = await api.get('/reports/pending-tasks', { params });
    return response.data;
  },

  // GET /api/reports/general
  async getGeneralReport() {
    const response = await api.get('/reports/general');
    return response.data;
  }
};
