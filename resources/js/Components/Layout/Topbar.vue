<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth?.user

const roleLabels = { super_admin: 'مدير عام', manager: 'مدير', accountant: 'محاسب', technician: 'فني', employee: 'موظف' }

const logoutForm = useForm({})

function toggleTheme() {
  document.documentElement.classList.toggle('dark')
  localStorage.setItem('nm-theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')
}

if (typeof window !== 'undefined' && localStorage.getItem('nm-theme') === 'dark') {
  document.documentElement.classList.add('dark')
}
</script>

<template>
  <header class="h-16 bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between px-4 md:px-6 sticky top-0 z-20">
    <p class="text-sm text-gray-400 hidden md:block">مرحباً بك في نظام NM</p>
    <div class="flex items-center gap-3">
      <button @click="toggleTheme" class="btn-ghost p-2" title="تبديل المظهر">
        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
      </button>
      <div class="flex items-center gap-3 pr-3 border-r border-gray-100 dark:border-slate-700">
        <div class="text-left hidden sm:block">
          <p class="text-sm font-medium text-gray-800 dark:text-white leading-tight">{{ user?.name }}</p>
          <p class="text-xs text-gray-400">{{ roleLabels[user?.role] }}</p>
        </div>
        <div class="w-9 h-9 rounded-full bg-brand-100 dark:bg-brand-500/20 text-brand-700 dark:text-brand-400 flex items-center justify-center font-semibold">{{ user?.name?.charAt(0) }}</div>
        <button @click="logoutForm.post(route('logout'))" class="btn-ghost p-2" title="تسجيل الخروج">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
        </button>
      </div>
    </div>
  </header>
</template>
