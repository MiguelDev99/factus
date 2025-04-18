<template>
    <div class="flex justify-center items-center min-h-screen bg-gray-600">
      <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800">Restablecer contraseña</h2>
        <form @submit.prevent="resetPassword" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input v-model="email" type="email" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
            <input v-model="password" type="password" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
            <input v-model="password_confirmation" type="password" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>
          <button type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
            Cambiar contraseña
          </button>
          <p v-if="error" class="mt-4 text-red-600 text-sm text-center">{{ error }}</p>
          <p v-if="success" class="mt-4 text-green-600 text-sm text-center">{{ success }}</p>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    name: 'ResetPassword',
    data() {
      return {
        email: '',
        password: '',
        password_confirmation: '',
        token: '',
        error: '',
        success: ''
      }
    },
    mounted() {
      this.token = this.$route.params.token
    },
    methods: {
      async resetPassword() {
        if (this.password !== this.password_confirmation) {
          this.error = 'Las contraseñas no coinciden'
          return
        }
  
        try {
          const res = await axios.post('http://localhost:8000/api/reset-password', {
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation,
            token: this.token
          })
  
          this.success = res.data.message
          this.error = ''
          // Opcional: redireccionar tras unos segundos
          // setTimeout(() => this.$router.push('/'), 3000)
        } catch (err) {
          this.error = err.response?.data?.message || 'Ocurrió un error al restablecer la contraseña'
          this.success = ''
        }
      }
    }
  }
  </script>
  