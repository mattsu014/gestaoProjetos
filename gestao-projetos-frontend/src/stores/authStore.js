import { defineStore } from 'pinia';
import { authService } from '@/services/authService';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(authService.getCurrentUser());
  const isLoading = ref(false);
  const error = ref(null);

  const isAuthenticated = computed(() => !!user.value);

  async function login(credentials) {
    isLoading.value = true;
    error.value = null;

    try {
      const data = await authService.login(credentials);
      user.value = data.user;
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro no login';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  async function register(userData) {
    isLoading.value = true;
    error.value = null;

    try {
      const data = await authService.register(userData);
      user.value = data.user;
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro no registro';
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  function logout() {
    authService.logout();
    user.value = null;
  }

  function clearError() {
    error.value = null;
  }

  return {
    user,
    isLoading,
    error,
    isAuthenticated,
    login,
    register,
    logout,
    clearError
  };
});
