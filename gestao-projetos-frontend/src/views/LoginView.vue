<template>
  <div class="login-container">
    <h1>Login</h1>

    <div class="login-card">
      <h2>Acesse sua conta</h2>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label>Email:</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="form-input"
            placeholder="seu@email.com"
          >
        </div>

        <div class="form-group">
          <label>Senha:</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="form-input"
            placeholder="Sua senha"
          >
        </div>

        <button
          type="submit"
          :disabled="authStore.isLoading"
          class="login-button"
          :class="{ 'loading': authStore.isLoading }"
        >
          {{ authStore.isLoading ? 'Carregando...' : 'Entrar' }}
        </button>
      </form>

      <p v-if="authStore.error" class="error-message">
        {{ authStore.error }}
      </p>

      <div class="register-section">
        <p>Não tem conta?</p>
        <router-link to="/register" class="register-link">
          Registre-se
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  email: '',
  password: ''
});

async function handleLogin() {
  try {
    await authStore.login(form.value);
    router.push('/');
  } catch (error) {
    // O erro já é tratado na store
  }
}
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 0 auto;
  padding: 40px 20px;
}

.login-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 30px;
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.login-card h2 {
  margin-top: 0;
  color: #333;
  text-align: center;
}

.login-form {
  margin-top: 25px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
}

.form-input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.login-button {
  width: 100%;
  padding: 12px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.login-button:hover:not(:disabled) {
  background-color: #0056b3;
}

.login-button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.login-button.loading {
  background-color: #6c757d;
}

.error-message {
  margin-top: 15px;
  padding: 10px;
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  text-align: center;
}

.register-section {
  margin-top: 25px;
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.register-section p {
  margin-bottom: 10px;
  color: #666;
}

.register-link {
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
}

.register-link:hover {
  text-decoration: underline;
}

@media (max-width: 480px) {
  .login-container {
    padding: 20px 15px;
  }

  .login-card {
    padding: 20px;
  }
}
</style>
