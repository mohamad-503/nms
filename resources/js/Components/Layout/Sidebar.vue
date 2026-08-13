<script setup>
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const nav = [
  { route: 'dashboard', label: 'لوحة التحكم', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
  { route: 'customers.index', label: 'المشتركين', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { route: 'plans.index', label: 'الباقات', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
  { route: 'invoices.index', label: 'الفواتير', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
  { route: 'expenses.index', label: 'المصروفات', icon: 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 7l-.5-.5a2 2 0 00-2.83 0L4 18.5V21h2.5L19 9.83a2 2 0 000-2.83z' },
  { route: 'inventory.index', label: 'المخزون', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
  { route: 'employees.index', label: 'الموظفين', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
  { route: 'tickets.index', label: 'تذاكر الدعم', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-.01 8.01l-.01-.01H5a4 4 0 010-8h.01z' },
  { route: 'routers.index', label: 'أجهزة MikroTik', icon: 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2' },
  { route: 'reports.index', label: 'التقارير', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
  { route: 'logs.index', label: 'السجلات', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
  { route: 'settings.index', label: 'الإعدادات', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' },
]

function isActive(routeName) {
  return page.url.startsWith(routeName.split('.')[0])
}
</script>

<template>
  <aside class="fixed top-0 right-0 z-40 h-screen w-72 bg-white dark:bg-slate-800 border-l border-gray-100 dark:border-slate-700 overflow-y-auto">
    <div class="h-16 flex items-center gap-3 px-5 border-b border-gray-100 dark:border-slate-700">
      <div class="w-10 h-10 rounded-xl bg-brand-500 text-white flex items-center justify-center font-bold">NM</div>
      <div>
        <p class="font-bold text-gray-800 dark:text-white leading-tight">NM System</p>
        <p class="text-xs text-gray-400">إدارة مزود الخدمة</p>
      </div>
    </div>
    <nav class="p-3 space-y-1">
      <Link v-for="item in nav" :key="item.route" :href="route(item.route)" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
        :class="isActive(item.route) ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700/50'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="item.icon" /></svg>
        {{ item.label }}
      </Link>
    </nav>
  </aside>
</template>
