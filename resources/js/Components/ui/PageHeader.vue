<script setup>
defineProps({ title: String, subtitle: String })
</script>

<template>
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ title }}</h1>
    <p v-if="subtitle" class="text-gray-500 dark:text-slate-400 mt-1">{{ subtitle }}</p>
  </div>
</template>
