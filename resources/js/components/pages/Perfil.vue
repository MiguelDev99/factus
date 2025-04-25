<template>
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow-md">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Perfil de Usuario</h1>
      <form @submit.prevent="actualizarPerfil" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="(campo, key) in camposVisibles"
          :key="key"
          class="flex flex-col"
        >
          <label :for="key" class="mb-1 text-sm font-medium text-gray-700 capitalize">
            {{ key.replaceAll('_', ' ') }}
          </label>
          <input
            v-model="campos[key]"
            :id="key"
            type="text"
            class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="col-span-full text-right text-sm text-gray-500 mt-2">
          Última actualización: <span class="font-medium">{{ ultimaActualizacion }}</span>
        </div>

        <div class="col-span-full flex justify-end mt-6">
          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors"
          >
            Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed } from 'vue'
  import axios from 'axios'
  import { useToast } from 'vue-toast-notification'
  import emitter from '../../../utils/eventBus'
  
  const toast = useToast()
  
  const campos = ref({
    name: '',
    email: '',
    identification: '',
    dv: '',
    company: '',
    trade_name: '',
    address: '',
    phone: '',
    legal_organization_id: '',
    tribute_id: '',
    identification_document_id: '',
    municipality_id: '',
  })
  
  onMounted(async () => {
    try {
      const token = localStorage.getItem('token')
      const response = await axios.get('/user', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
      console.log('Datos del perfil:', response.data)
      campos.value = { ...campos.value, ...response.data }
    } catch (error) {
      console.error('Error al obtener datos del perfil:', error)
      toast.error('No se pudo cargar el perfil')
    }
  })
  
  const actualizarPerfil = async () => {
    try {
      const token = localStorage.getItem('token')
      await axios.put('/user', campos.value, {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
      campos.value.updated_at = new Date().toISOString()
      localStorage.setItem('user', JSON.stringify({ ...campos.value }))
      emitter.emit('usuarioActualizado', campos.value.name)
      toast.success('Perfil actualizado correctamente')
    } catch (error) {
      console.error(error)
      toast.error('Ocurrió un error al actualizar el perfil')
    }
  }
  const camposVisibles = computed(() => {
    const ocultar = ['id', 'created_at', 'updated_at', 'email_verified_at']
    return Object.fromEntries(
      Object.entries(campos.value).filter(([key]) => !ocultar.includes(key))
    )
  })
  const ultimaActualizacion = computed(() => {
    if (!campos.value.updated_at) return ''
    const fecha = new Date(campos.value.updated_at)
    const opciones = {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }
    return fecha.toLocaleString('es-CO', opciones)
  })
</script>
  