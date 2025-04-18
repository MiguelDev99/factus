<template>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
      <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-semibold mb-6 text-center">Crear cuenta</h2>
        <form @submit.prevent="register">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <input v-model="name" type="text" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Correo</label>
            <input v-model="email" type="email" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input v-model="password" type="password" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>
          <button type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
            Registrarse
          </button>
          <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
        </form>
      </div>
    </div>
</template>
  
<script>
  import axios from 'axios'
  
  export default {
    name: 'Register',
    data() {
      return {
        name: '',
        email: '',
        password: '',
        error: ''
      }
    },
    methods: {
      async register() {
        try {
          const res = await axios.post('http://localhost:8000/api/register', {
            name: this.name,
            email: this.email,
            password: this.password
          });
  
          // Opcional: guardar el token si el backend lo envía
          // localStorage.setItem('token', res.data.token);
  
          this.$router.push('/');
        } catch (err) {
          this.error = err.response?.data?.message || 'Error al registrar';
        }
      }
    }
  }
</script>
  