<script setup>
import Sidebar from '@/Components/Layout/Sidebar.vue'
import Topbar from '@/Components/Layout/Topbar.vue'
</script>

<template>
  <div class="min-h-screen flex bg-gray-50 dark:bg-slate-900">
    <Sidebar />
    <div class="flex-1 flex flex-col min-w-0 lg:mr-72">
      <Topbar />
      <main class="flex-1 p-4 md:p-6 overflow-x-hidden">
        <slot />
      </main>
    </div>
  </div>
</template>
