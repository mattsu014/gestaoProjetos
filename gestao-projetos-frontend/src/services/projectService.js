import api from './api';

export const projectService = {
  // GET /api/projects
  async getProjects() {
    const response = await api.get('/projects');
    return response.data;
  },

  // POST /api/projects
  async createProject(projectData) {
    const response = await api.post('/projects', projectData);
    return response.data;
  },

  // GET /api/projects/{id}
  async getProject(id) {
    const response = await api.get(`/projects/${id}`);
    return response.data;
  },

  // PUT /api/projects/{id}
  async updateProject(id, projectData) {
    const response = await api.put(`/projects/${id}`, projectData);
    return response.data;
  },

  // DELETE /api/projects/{id}
  async deleteProject(id) {
    const response = await api.delete(`/projects/${id}`);
    return response.data;
  }
};
