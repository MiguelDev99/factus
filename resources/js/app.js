import './bootstrap'
import '../css/app.css'
import { createApp } from 'vue'
import App from './components/App.vue'
import router from './router'
import axios from 'axios'

// 👉 Configuración global de Axios
axios.defaults.baseURL = '/api'

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, error => {
  return Promise.reject(error)
})

// (Opcional) Interceptor de respuesta para manejar errores globales
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      window.location.href = '/login' // o router.push('/') si usás rutas
    }
    return Promise.reject(error)
  }
)

// 👉 Asignar Axios a `app.config.globalProperties` si querés usarlo como `this.$axios`
const app = createApp(App)
app.config.globalProperties.$axios = axios

app.use(router)
app.mount('#app')
