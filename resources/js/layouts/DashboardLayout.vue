<template>
  <div class="flex h-screen">
    <!-- Menú lateral -->
    <aside class="w-64 h-full bg-gray-800 text-white p-4 flex flex-col justify-between">
      <div>
        <h2 class="text-xl font-bold mb-6">{{ texts.dashboardTitle }}</h2>
        <ul class="space-y-4">
          <li>
            <router-link to="/dashboard" exact class="block transition hover:text-yellow-400" :class="{ 'text-yellow-400 font-semibold': $route.path === '/dashboard' }">
              {{ texts.home }}
            </router-link>
          </li>
          <li>
            <router-link to="/facturacion" class="block hover:text-yellow-400 transition" active-class="text-yellow-400">
              {{ texts.billing }}
            </router-link>
          </li>
          <li>
            <router-link to="/clientes" class="block hover:text-yellow-400 transition" active-class="text-yellow-400">
              {{ texts.clients }}
            </router-link>
          </li>
          <li>
            <router-link to="/productos" class="block hover:text-yellow-400 transition" active-class="text-yellow-400">
              {{ texts.products }}
            </router-link>
          </li>
          <li>
            <router-link to="/perfil" class="block hover:text-yellow-400 transition" active-class="text-yellow-400">
              {{ texts.profile }}
            </router-link>
          </li>
        </ul>
      </div>
      <button @click="logout"
        class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-sm transition"
      >
        {{ texts.logout }}
      </button>
    </aside>

    <!-- Contenido principal -->
    <div class="flex-1 flex flex-col">
      <!-- Barra superior -->
      <header class="bg-gray-700 text-white p-4 shadow flex justify-between">
        <div class="flex justify-between items-center">
          <p>{{ user ? `${texts.welcome}, ${user.name}` : texts.welcome_guest }}</p>
        </div>
        <div class="relative">
          <router-link to="/carrito" class="material-icons">shopping_cart</router-link>
          <span
            v-if="cartCount > 0"
            class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
          >
            {{ cartCount }}
          </span>
        </div>
      </header>

      <!-- Contenido dinámico -->
      <main class="flex-1 p-6 bg-gray-100 overflow-auto">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script>
  import { texts } from '../../texts';
  import emitter from '../../utils/eventBus';

  export default {
    data() {
      return {
        texts: texts,
        cartCount: 0,
        user: null,
      };
    },
    mounted() {
      this.obtenerCantidadCarrito();

      const user = JSON.parse(localStorage.getItem('user'));
      if (user) {
        this.user = user;
      }

      emitter.on('carritoActualizado', () => {
        this.obtenerCantidadCarrito();
      });

      emitter.on('usuarioActualizado', (nuevoNombre) => {
        const user = JSON.parse(localStorage.getItem('user')) || {};
        user.name = nuevoNombre;
        localStorage.setItem('user', JSON.stringify(user));
        this.user = user; // <--- actualizamos también el estado reactivo
      });
    },
    name: 'DashboardLayout',
    methods: {
      logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.$router.push('/');
      },
      async obtenerCantidadCarrito() {
        try {
          const token = localStorage.getItem('token');
          const response = await axios.get('/cart', {
            headers: {
              Authorization: `Bearer ${token}`
            }
          });

          // Suponiendo que cada ítem tiene un campo quantity
          const carrito = response.data;
          const cantidadTotal = carrito.reduce((total, item) => total + (item.quantity || 0), 0);
          this.cartCount = cantidadTotal;

        } catch (error) {
          console.error('Error al obtener el carrito:', error);
        }
      }
    }
  }
</script>
