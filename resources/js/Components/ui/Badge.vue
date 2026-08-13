<script setup>
defineProps({ type: { type: String, default: 'gray' } })
const map = {
  gray: 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300',
  green: 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
  red: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
  blue: 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
  amber: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
  brand: 'bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400',
}
</script>

<template>
  <span class="badge" :class="map[type]"><slot /></span>
</template>
