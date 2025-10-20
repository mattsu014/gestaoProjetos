import api from './api';

export const authService = {
  async login(credentials) {
    const response = await api.post('/login', credentials);

    if (response.data.token) {
      localStorage.setItem('auth_token', response.data.token);
      localStorage.setItem('user_data', JSON.stringify(response.data.user));
    }

    return response.data;
  },

  async register(userData) {
    const response = await api.post('/register', userData);

    if (response.data.token) {
      localStorage.setItem('auth_token', response.data.token);
      localStorage.setItem('user_data', JSON.stringify(response.data.user));
    }

    return response.data;
  },

  logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
  },

  getCurrentUser() {
    const user = localStorage.getItem('user_data');
    return user ? JSON.parse(user) : null;
  },

  isAuthenticated() {
    return !!localStorage.getItem('auth_token');
  }
};
