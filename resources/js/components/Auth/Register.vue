<template>
  <div class="flex justify-center items-center min-h-screen bg-gray-600">
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

        <div class="mb-4 relative">
          <label class="block text-sm font-medium text-gray-700">Contraseña</label>
          <input :type="showPassword ? 'text' : 'password'" v-model="password" required
            class="mt-1 block w-full px-4 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          <span
            class="material-icons absolute right-3 top-9 cursor-pointer text-gray-500 hover:text-gray-800"
            @click="showPassword = !showPassword">
            {{ showPassword ? 'visibility_off' : 'visibility' }}
          </span>
        </div>

        <div class="mb-4 relative">
          <label class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
          <input :type="showConfirmPassword ? 'text' : 'password'" v-model="password_confirmation" required
            class="mt-1 block w-full px-4 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          <span
            class="material-icons absolute right-3 top-9 cursor-pointer text-gray-500 hover:text-gray-800"
            @click="showConfirmPassword = !showConfirmPassword">
            {{ showConfirmPassword ? 'visibility_off' : 'visibility' }}
          </span>
        </div>

        <button type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
          Registrarse
        </button>

        <p v-if="error" class="mt-4 text-red-600 text-sm">{{ error }}</p>

        <p class="mt-4 text-sm text-center text-gray-600">
          ¿Ya tienes una cuenta? <router-link to="/" class="text-blue-600 hover:underline">Iniciar sesión</router-link>
        </p>
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
      password_confirmation: '',
      showPassword: false,
      showConfirmPassword: false,
      error: ''
    }
  },
  methods: {
    async register() {
      if (this.password !== this.password_confirmation) {
        this.error = 'Las contraseñas no coinciden'
        return
      }

      try {
        const res = await axios.post('http://localhost:8000/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation
        })

        this.$router.push('/')
      } catch (err) {
        this.error = err.response?.data?.message || 'Error al registrar'
      }
    }
  }
}
</script>

<style scoped>
.material-icons {
  font-size: 20px;
}
</style>
