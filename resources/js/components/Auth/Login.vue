<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">
      <h2 class="text-2xl font-bold text-center text-gray-800">Iniciar sesión</h2>
      <form @submit.prevent="login" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <button
          type="submit"
          :disabled="loading"
          class="w-full px-4 py-2 font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading">Entrando...</span>
          <span v-else>Entrar</span>
        </button>
        <p v-if="error" class="text-sm text-red-600 text-center">{{ error }}</p>
        <p class="text-sm text-center text-gray-600">
          ¿No tienes una cuenta? <router-link to="/register" class="text-blue-600 hover:underline">Regístrate</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';

  export default {
    name: 'Login',
    data() {
      return {
        email: '',
        password: '',
        error: '',
        loading: false
      };
    },
    mounted() {
      const token = localStorage.getItem('token')
      if (token) {
        this.$router.push('/dashboard')
      }
    },
    methods: {
      async login() {
        this.loading = true;
        this.error = '';
        try {
          const response = await axios.post('http://localhost:8000/api/login', {
            email: this.email,
            password: this.password
          });

          localStorage.setItem('token', response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));
          this.$router.push('/dashboard');
        } catch (err) {
          this.error = err.response?.data?.message || 'Error al iniciar sesión';
        } finally {
          this.loading = false;
        }
      }
    }
  };
</script>
