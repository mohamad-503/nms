<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/ui/PageHeader.vue'
import StatCard from '@/Components/ui/StatCard.vue'
import Badge from '@/Components/ui/Badge.vue'

const props = defineProps({
  stats: Object,
  recentLogs: Array,
  revenueData: Object,
  customerStatusData: Object,
})

const fmt = (n) => new Intl.NumberFormat('ar-IQ').format(n || 0)
</script>

<template>
  <AppLayout>
    <PageHeader title="لوحة التحكم" subtitle="نظرة عامة على أداء النظام" />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <StatCard title="إجمالي المشتركين" :value="fmt(stats.total)" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" color="brand" />
      <StatCard title="المشتركين النشطين" :value="fmt(stats.active)" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" color="green" />
      <StatCard title="اشتراكات منتهية" :value="fmt(stats.expired)" icon="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" color="amber" />
      <StatCard title="إيرادات اليوم" :value="fmt(stats.todayIncome)" subtitle="د.ع" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" color="green" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <StatCard title="إيرادات الشهر" :value="fmt(stats.monthIncome)" subtitle="د.ع" icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" color="brand" />
      <StatCard title="مصروفات الشهر" :value="fmt(stats.expenses)" subtitle="د.ع" icon="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 7l-.5-.5a2 2 0 00-2.83 0L4 18.5V21h2.5L19 9.83a2 2 0 000-2.83z" color="red" />
      <StatCard title="صافي الربح" :value="fmt(stats.profit)" subtitle="د.ع" icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" color="green" />
      <StatCard title="تذاكر مفتوحة" :value="fmt(stats.openTickets)" icon="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-.01 8.01l-.01-.01H5a4 4 0 010-8h.01z" color="blue" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <StatCard title="تنبيهات المخزون" :value="fmt(stats.lowStock)" icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" color="amber" />
      <StatCard title="موقوفون" :value="fmt(stats.suspended)" icon="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" color="red" />
      <StatCard title="حالة النظام" value="سليم" subtitle="جميع الأنظمة تعمل" icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" color="green" />
    </div>

    <div class="card p-5">
      <h3 class="font-bold text-gray-800 dark:text-white mb-4">أحدث الأنشطة</h3>
      <div class="space-y-3">
        <div v-for="log in recentLogs" :key="log.id" class="flex items-center gap-3 py-2 border-b border-gray-50 dark:border-slate-700/50 last:border-0">
          <div class="w-9 h-9 rounded-full bg-brand-50 dark:bg-brand-500/10 text-brand-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-700 dark:text-slate-200 truncate"><span class="font-medium">{{ log.user?.name || 'النظام' }}</span> {{ log.description || log.action }}</p>
            <p class="text-xs text-gray-400">{{ new Date(log.created_at).toLocaleString('ar-IQ') }} · {{ log.module }}</p>
          </div>
          <Badge type="gray">{{ log.action }}</Badge>
        </div>
        <p v-if="!recentLogs.length" class="text-center text-gray-400 py-6">لا توجد أنشطة حديثة</p>
      </div>
    </div>
  </AppLayout>
</template>
