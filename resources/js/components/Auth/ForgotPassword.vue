<template>
    <div class="flex justify-center items-center min-h-screen bg-gray-600">
      <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4 text-center">Recuperar Contraseña</h2>
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
            <input v-model="email" type="email" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200" />
          </div>
          <button type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
            Enviar enlace de recuperación
          </button>
          <p v-if="message" class="mt-4 text-green-600 text-sm">{{ message }}</p>
          <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>
          <p class="text-sm text-gray-600 mt-4">
            <router-link to="/" class="text-blue-600 hover:underline">← Volver a iniciar sesión</router-link>
          </p>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    name: 'ForgotPassword',
    data() {
      return {
        email: '',
        message: '',
        error: ''
      }
    },
    methods: {
      async submit() {
        this.message = ''
        this.error = ''
        try {
          const res = await axios.post('http://localhost:8000/api/forgot-password', {
            email: this.email
          })
          this.message = res.data.message || 'Enlace de recuperación enviado a tu correo.'
        } catch (err) {
          this.error = err.response?.data?.message || 'Hubo un error al enviar el correo.'
        }
      }
    }
  }
  </script>
  