<template>
      <div
      v-if="visible"
      class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50"
      >
      <div class="bg-white rounded-xl p-6 shadow-xl w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ title }}</h3>
        <p class="text-gray-600 mb-6">{{ message }}</p>
        <div class="flex justify-end gap-3">
          <button
            @click="$emit('cancel')"
            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
          >
            Cancelar
          </button>
          <button
            @click="$emit('confirm')"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ConfirmModal',
    props: {
      visible: Boolean,
      title: String,
      message: String,
      confirmText: {
        type: String,
        default: 'Confirmar'
      }
    }
  }
  </script>
  