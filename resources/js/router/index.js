import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import Dashboard from '../components/Dashboard.vue';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import Facturacion from '../components/pages/Facturacion.vue';
import Clientes from '../components/pages/Clientes.vue';
import Productos from '../components/pages/Productos.vue';
import Carrito from '../components/pages/Carrito.vue';

const routes = [
  { 
    path: '/', 
    name: 'Login', 
    component: Login 
  },
  { 
    path: '/register', 
    name: 'Register',
    component: Register 
  },
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: Dashboard
      },
      {
        path: '/facturacion',
        name: 'Facturacion',
        component: Facturacion
      },
      {
        path: '/clientes',
        name: 'Clientes',
        component: Clientes
      },
      
      {
        path: '/productos',
        name: 'Productos',
        component: Productos
      },
      {
        path: '/carrito',
        name: 'Carrito',
        component: Carrito
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const isProtected = to.matched.some(record => record.meta.requiresAuth);

  if (isProtected && !token) {
    next('/');
  } else {
    next();
  }
});

export default router;
