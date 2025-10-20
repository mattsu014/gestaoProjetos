<template>
  <div class="register-container">
    <h2>Criar Conta</h2>
    <form @submit.prevent="handleRegister">
      <div class="form-group">
        <label for="name">Nome completo:</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          :class="{ 'error': errors.name }"
        >
        <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          :class="{ 'error': errors.email }"
        >
        <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
      </div>

      <div class="form-group">
        <label for="password">Senha:</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          required
          minlength="6"
          :class="{ 'error': errors.password }"
        >
        <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
      </div>

      <div class="form-group">
        <label for="password_confirmation">Confirmar senha:</label>
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          required
          :class="{ 'error': errors.password_confirmation }"
        >
        <span v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation }}</span>
      </div>

      <button
        type="submit"
        :disabled="authStore.isLoading"
        class="submit-btn"
      >
        {{ authStore.isLoading ? 'Criando conta...' : 'Criar conta' }}
      </button>
    </form>

    <p v-if="authStore.error" class="error-general">{{ authStore.error }}</p>

    <p class="login-link">
      Já tem uma conta? <router-link to="/login">Faça login</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

watch(() => form.name, () => { errors.name = ''; });
watch(() => form.email, () => { errors.email = ''; });
watch(() => form.password, () => { errors.password = ''; });
watch(() => form.password_confirmation, () => { errors.password_confirmation = ''; });

function validateForm() {
  let isValid = true;

  Object.keys(errors).forEach(key => errors[key] = '');

  if (form.name.length < 2) {
    errors.name = 'Nome deve ter pelo menos 2 caracteres';
    isValid = false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(form.email)) {
    errors.email = 'Email inválido';
    isValid = false;
  }

  if (form.password.length < 6) {
    errors.password = 'Senha deve ter pelo menos 6 caracteres';
    isValid = false;
  }

  if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'As senhas não coincidem';
    isValid = false;
  }

  return isValid;
}

async function handleRegister() {
  authStore.clearError();

  if (!validateForm()) {
    return;
  }

  try {
    await authStore.register(form);
    router.push('/');
  } catch (error) {
    if (error.response?.data?.errors) {
      const apiErrors = error.response.data.errors;

      if (apiErrors.name) {
        errors.name = apiErrors.name[0];
      }
      if (apiErrors.email) {
        errors.email = apiErrors.email[0];
      }
      if (apiErrors.password) {
        errors.password = apiErrors.password[0];
      }
    }
  }
}
</script>

<style scoped>
.register-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 30px;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #555;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
  transition: border-color 0.3s;
}

input:focus {
  outline: none;
  border-color: #007bff;
}

input.error {
  border-color: #dc3545;
}

.error-message {
  display: block;
  color: #dc3545;
  font-size: 14px;
  margin-top: 5px;
}

.error-general {
  color: #dc3545;
  text-align: center;
  margin: 15px 0;
  padding: 10px;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
}

.submit-btn {
  width: 100%;
  padding: 12px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-btn:hover:not(:disabled) {
  background-color: #0056b3;
}

.submit-btn:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.login-link {
  text-align: center;
  margin-top: 20px;
}

.login-link a {
  color: #007bff;
  text-decoration: none;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>
