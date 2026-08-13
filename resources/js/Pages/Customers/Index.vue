<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/ui/PageHeader.vue'
import Badge from '@/Components/ui/Badge.vue'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  customers: Object,
  plans: Array,
  areas: Array,
  cities: Array,
  filters: Object,
})

const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')
const cityFilter = ref(props.filters?.city_id || '')
const areaFilter = ref(props.filters?.area_id || '')
const planFilter = ref(props.filters?.plan_id || '')
const showFilters = ref(false)

const statusMap = {
  active: { label: 'نشط', type: 'green' },
  suspended: { label: 'موقوف', type: 'amber' },
  expired: { label: 'منتهي', type: 'red' },
  inactive: { label: 'غير نشط', type: 'gray' },
}

function applyFilters() {
  router.get(route('customers.index'), {
    search: search.value, status: statusFilter.value, city_id: cityFilter.value, area_id: areaFilter.value, plan_id: planFilter.value,
  }, { preserveState: true, preserveScroll: true })
}

let debounce
watch(search, () => {
  clearTimeout(debounce)
  debounce = setTimeout(applyFilters, 400)
})

function resetFilters() {
  search.value = ''; statusFilter.value = ''; cityFilter.value = ''; areaFilter.value = ''; planFilter.value = ''
  applyFilters()
}

const activeFiltersCount = computed(() => {
  let n = 0
  if (statusFilter.value) n++; if (cityFilter.value) n++; if (areaFilter.value) n++; if (planFilter.value) n++
  return n
})

const fmt = (n) => new Intl.NumberFormat('ar-IQ').format(n || 0)
const allCustomers = computed(() => props.customers.data || [])

function renew(c) {
  const days = prompt('تمديد الاشتراك بأيام:', '30')
  if (!days) return
  router.post(route('customers.renew', c.id), { days }, { preserveScroll: true })
}
function setStatus(c, status) {
  if (status === 'suspended') router.post(route('customers.suspend', c.id), {}, { preserveScroll: true })
  else router.post(route('customers.activate', c.id), {}, { preserveScroll: true })
}
function remove(c) {
  if (!confirm(`حذف المشترك "${c.full_name}"؟`)) return
  router.delete(route('customers.destroy', c.id))
}
</script>

<template>
  <AppLayout>
    <PageHeader title="المشتركين" subtitle="إدارة حسابات المشتركين والاشتراكات" />

    <div class="flex flex-col md:flex-row gap-3 mb-4">
      <div class="relative md:flex-1">
        <input v-model="search" class="input pr-10" placeholder="بحث بالاسم، الهاتف، PPPoE، الرقم الوطني..." />
        <svg class="w-5 h-5 absolute right-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
      </div>
      <button class="btn-secondary" @click="showFilters = !showFilters">
        تصفية
        <span v-if="activeFiltersCount" class="badge bg-brand-500 text-white">{{ activeFiltersCount }}</span>
      </button>
      <Link :href="route('customers.create')" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        مشترك جديد
      </Link>
    </div>

    <transition name="fade">
      <div v-if="showFilters" class="card p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div><label class="label">الحالة</label><select v-model="statusFilter" @change="applyFilters" class="input"><option value="">الكل</option><option v-for="(v,k) in statusMap" :key="k" :value="k">{{ v.label }}</option></select></div>
          <div><label class="label">المدينة</label><select v-model="cityFilter" @change="applyFilters" class="input"><option value="">الكل</option><option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option></select></div>
          <div><label class="label">المنطقة</label><select v-model="areaFilter" @change="applyFilters" class="input"><option value="">الكل</option><option v-for="a in areas" :key="a.id" :value="a.id">{{ a.name }}</option></select></div>
          <div><label class="label">الباقة</label><select v-model="planFilter" @change="applyFilters" class="input"><option value="">الكل</option><option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }}</option></select></div>
        </div>
        <div class="flex justify-end mt-3"><button class="btn-ghost text-sm" @click="resetFilters">إعادة تعيين</button></div>
      </div>
    </transition>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
      <div class="card p-3 text-center"><p class="text-xs text-gray-400">الإجمالي</p><p class="text-lg font-bold text-gray-800 dark:text-white">{{ customers.total }}</p></div>
      <div class="card p-3 text-center"><p class="text-xs text-gray-400">نشط</p><p class="text-lg font-bold text-green-500">{{ allCustomers.filter(c=>c.status==='active').length }}</p></div>
      <div class="card p-3 text-center"><p class="text-xs text-gray-400">موقوف</p><p class="text-lg font-bold text-amber-500">{{ allCustomers.filter(c=>c.status==='suspended').length }}</p></div>
      <div class="card p-3 text-center"><p class="text-xs text-gray-400">منتهي</p><p class="text-lg font-bold text-red-500">{{ allCustomers.filter(c=>c.status==='expired').length }}</p></div>
    </div>

    <div class="card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-500 dark:text-slate-400 text-right">
              <th class="px-4 py-3.5 font-semibold">العميل</th>
              <th class="px-4 py-3.5 font-semibold">الهاتف</th>
              <th class="px-4 py-3.5 font-semibold">الباقة</th>
              <th class="px-4 py-3.5 font-semibold">PPPoE</th>
              <th class="px-4 py-3.5 font-semibold">نهاية الاشتراك</th>
              <th class="px-4 py-3.5 font-semibold">الحالة</th>
              <th class="px-4 py-3.5 font-semibold">إجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in allCustomers" :key="c.id" class="table-row cursor-pointer" @click="router.visit(route('customers.show', c.id))">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <img v-if="c.profile_photo" :src="`/storage/${c.profile_photo}`" class="w-9 h-9 rounded-full object-cover" />
                  <div v-else class="w-9 h-9 rounded-full bg-brand-100 dark:bg-brand-500/20 text-brand-600 flex items-center justify-center text-sm font-bold">{{ (c.full_name||'?').charAt(0) }}</div>
                  <div><p class="font-medium text-gray-800 dark:text-white">{{ c.full_name }}</p><p class="text-xs text-gray-400">{{ c.city?.name || '' }}</p></div>
                </div>
              </td>
              <td class="px-4 py-3">{{ c.phone || '-' }}</td>
              <td class="px-4 py-3">{{ c.plan?.name || '-' }}</td>
              <td class="px-4 py-3 font-mono text-sm text-gray-500">{{ c.pppoe_username || '-' }}</td>
              <td class="px-4 py-3 text-gray-500">{{ c.subscription_end || '-' }}</td>
              <td class="px-4 py-3"><Badge :type="statusMap[c.status]?.type">{{ statusMap[c.status]?.label }}</Badge></td>
              <td class="px-4 py-3" @click.stop>
                <div class="flex items-center gap-1">
                  <Link :href="route('customers.edit', c.id)" class="btn-ghost p-1.5" title="تعديل"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></Link>
                  <button class="btn-ghost p-1.5" @click="renew(c)" title="تمديد"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></button>
                  <button v-if="c.status==='active'" class="btn-ghost p-1.5" @click="setStatus(c,'suspended')" title="إيقاف"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button>
                  <button v-else class="btn-ghost p-1.5 text-green-600" @click="setStatus(c,'active')" title="تفعيل"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg></button>
                  <a :href="route('customers.contract', c.id)" target="_blank" class="btn-ghost p-1.5" title="عقد"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></a>
                  <a :href="route('customers.invoice', c.id)" target="_blank" class="btn-ghost p-1.5" title="فاتورة"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" /></svg></a>
                  <button class="btn-ghost p-1.5 text-red-500" @click="remove(c)" title="حذف"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex items-center justify-between mt-4">
      <p class="text-sm text-gray-500">عرض {{ customers.from }} - {{ customers.to }} من {{ customers.total }}</p>
      <div class="flex items-center gap-1">
        <Link v-for="link in customers.links" :key="link.label" :href="link.url || '#'" :class="['btn-ghost p-2', !link.url && 'opacity-50 cursor-default', link.active && 'bg-brand-50 text-brand-700']" v-html="link.label" />
      </div>
    </div>
  </AppLayout>
</template>
