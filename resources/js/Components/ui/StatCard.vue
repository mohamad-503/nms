<script setup>
defineProps({
  title: String, subtitle: String, icon: String, color: { type: String, default: 'brand' },
})
const colors = {
  brand: 'bg-brand-50 text-brand-500 dark:bg-brand-500/10',
  green: 'bg-green-50 text-green-500 dark:bg-green-500/10',
  blue: 'bg-blue-50 text-blue-500 dark:bg-blue-500/10',
  red: 'bg-red-50 text-red-500 dark:bg-red-500/10',
  amber: 'bg-amber-50 text-amber-500 dark:bg-amber-500/10',
}
const fmt = (n) => new Intl.NumberFormat('ar-IQ').format(n || 0)
</script>

<template>
  <div class="card p-5 hover:shadow-lg transition-shadow">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm text-gray-500 dark:text-slate-400">{{ title }}</p>
        <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1"><slot>{{ fmt(value) }}</slot></p>
        <p v-if="subtitle" class="text-xs text-gray-400 mt-1">{{ subtitle }}</p>
      </div>
      <div v-if="icon" class="w-12 h-12 rounded-xl flex items-center justify-center" :class="colors[color]">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="icon" /></svg>
      </div>
    </div>
  </div>
</template>
