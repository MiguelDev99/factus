<!-- resources/js/components/Dashboard.vue -->
<template>
    <div class="dashboard">
      <h1>Bienvenido al Dashboard</h1>
      <p>Aquí puedes ver información privada del usuario.</p>
    </div>
</template>
  
<script>
  export default {
    name: 'Dashboard',
    mounted() {
      console.log('Dashboard cargado');
    }
  }
</script>
  