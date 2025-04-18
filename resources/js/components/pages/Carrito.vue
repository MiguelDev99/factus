<template>
  <div class="flex flex-col items-center min-h-screen bg-gray-100 px-2 mt-8">

    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-3xl">
      <h3 class="text-xl font-semibold mb-4">Productos en el Carrito</h3>

      <ul v-if="productos.length" class="space-y-6">
        <li
          v-for="(producto, index) in productos"
          :key="index"
          class="flex items-center justify-between bg-gray-50 rounded-xl p-4 shadow-sm"
        >
          <div class="flex items-center gap-4">
            <img
              :src="getImageUrl(producto.image)"
              alt="Imagen del producto"
              class="w-20 h-20 object-cover rounded-lg border border-gray-200"
            />
            <div>
              <h4 class="text-lg font-semibold text-gray-800">{{ producto.product_name }}</h4>
              <p class="text-gray-600 text-sm">Cantidad: {{ producto.quantity }}</p>
              <p class="text-gray-600 text-sm">Precio unitario: ${{ Number(producto.unit_price).toLocaleString('es-CO') }}</p>
            </div>
          </div>

          <div class="flex flex-col items-end gap-2">
            <span class="text-green-600 font-bold text-lg">
              ${{ Number(producto.unit_price * producto.quantity).toLocaleString('es-CO') }}
            </span>
            <button @click="solicitarEliminacion(producto.id)" class="text-red-500 hover:text-red-700">
              <span class="material-icons">delete</span>
            </button>
          </div>
        </li>
      </ul>

      <p v-else class="text-gray-500">Tu carrito está vacío 🛒</p>

      <div v-if="productos.length" class="mt-6 border-t pt-4 flex justify-between items-center font-bold text-lg text-gray-800">
        <span>Total:</span>
        <span>${{ Number(total).toLocaleString('es-CO') }}</span>
      </div>

      <button
        v-if="productos.length"
        class="mt-6 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition w-full text-lg font-medium"
      >
        Proceder al Pago
      </button>
    </div>

    <!-- Modal de Confirmación -->
    <ConfirmModal
      :visible="showConfirmModal"
      title="Eliminar producto"
      message="¿Estás seguro de que deseas eliminar este producto del carrito?"
      confirmText="Eliminar"
      @cancel="cancelarEliminacion"
      @confirm="confirmarEliminacion"
    />
  </div>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import ConfirmModal from '../../components/modals/ConfirmModel.vue'
import eventBus from '../../../utils/eventBus'

export default {
  name: 'Carrito',
  components: {
    ConfirmModal
  },
  data() {
    return {
      productos: [],
      showConfirmModal: false,
      productoAEliminar: null
    }
  },
  computed: {
    total() {
      return this.productos.reduce(
        (acc, producto) => acc + producto.unit_price * producto.quantity,
        0
      )
    }
  },
  mounted() {
    this.obtenerCarrito()
  },
  methods: {
    async obtenerCarrito() {
      try {
        const token = localStorage.getItem('token')
        const response = await axios.get('/cart', {
          headers: { Authorization: `Bearer ${token}` }
        })
        this.productos = response.data
      } catch (error) {
        console.error('Error al obtener el carrito:', error)
      }
    },
    solicitarEliminacion(id) {
      this.productoAEliminar = id
      this.showConfirmModal = true
    },
    cancelarEliminacion() {
      this.showConfirmModal = false
      this.productoAEliminar = null
    },
    async confirmarEliminacion() {
      const toast = useToast()
      try {
        const token = localStorage.getItem('token')
        await axios.delete(`/cart/${this.productoAEliminar}`, {
          headers: { Authorization: `Bearer ${token}` }
        })

        // Actualizar productos
        this.productos = this.productos.filter(p => p.id !== this.productoAEliminar)

        // Emitir evento global para actualizar contador
        eventBus.emit('carritoActualizado')

        toast.success('Producto eliminado del carrito 🗑️')
      } catch (error) {
        console.error('Error al eliminar el producto:', error)
        toast.error('Hubo un problema al eliminar el producto')
      } finally {
        this.cancelarEliminacion()
      }
    },
    getImageUrl(imageName) {
      return imageName ? `/images/products/${imageName}` : '/images/products/default.jpg'
    }
  }
}
</script>
