<template>
  <div class="px-4">
    <h2 class="text-xl font-bold text-gray-800 mb-6">{{ texts.productsTitle }}</h2>

    <div v-if="loading" class="text-gray-500 text-lg">Cargando productos...</div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 p-6 flex flex-col items-center text-center"
      >
        <img
          :src="getImageUrl(product.image)"
          alt="Imagen del producto"
          class="w-40 h-40 object-cover object-center rounded-xl mb-4 border border-gray-200"
        />

        <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ product.name }}</h3>
        <p class="text-gray-500 text-sm mb-3">{{ product.description }}</p>
        <p class="text-green-600 font-bold text-lg mb-4">
          $ {{ Number(product.price).toLocaleString('es-CO') }}
        </p>

        <div class="flex items-center justify-center gap-3 mb-4">
          <button
            @click="decreaseQuantity(product.id)"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold w-8 h-8 rounded-full transition"
          >-</button>
          <span class="text-md font-medium">{{ quantities[product.id] || 1 }}</span>
          <button
            @click="increaseQuantity(product.id)"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold w-8 h-8 rounded-full transition"
          >+</button>
        </div>

        <button
          :disabled="addingToCart[product.id]"
          @click="addToCart(product)"
          class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg transition w-full disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ addingToCart[product.id] ? 'Añadiendo...' : 'Añadir al carrito' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { texts } from '../../../texts'
import { useToast } from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'
import emitter from '../../../utils/eventBus'

export default {
  name: 'Productos',
  data() {
    return {
      products: [],
      loading: true,
      texts,
      quantities: {},
      addingToCart: {}
    }
  },
  mounted() {
    this.fetchProducts()
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await this.$axios.get('/products')
        this.products = response.data
        this.products.forEach(product => {
          this.quantities[product.id] = 1
        })
      } catch (error) {
        console.error('Error cargando productos:', error)
      } finally {
        this.loading = false
      }
    },
    getImageUrl(imageName) {
      return imageName
        ? `/images/products/${imageName}`
        : '/images/products/default.jpg'
    },
    increaseQuantity(productId) {
      this.quantities[productId] = (this.quantities[productId] || 1) + 1
    },
    decreaseQuantity(productId) {
      if ((this.quantities[productId] || 1) > 1) {
        this.quantities[productId]--
      }
    },
    async addToCart(product) {
      const toast = useToast()
      const quantity = this.quantities[product.id] || 1
      this.addingToCart[product.id] = true

      try {
        // Llama al backend para agregar el producto al carrito
        await this.$axios.post('/cart', {
          id_product: product.id,
          quantity,
          price: product.price
        })

        // 🔔 Emitir evento para actualizar el contador del carrito
        emitter.emit('carritoActualizado')

        toast.success('Producto añadido al carrito 🛒')
      } catch (error) {
        console.error('Error al añadir al carrito:', error)
        toast.error('Hubo un problema al añadir al carrito')
      } finally {
        this.addingToCart[product.id] = false
      }
    }
  }
}
</script>
