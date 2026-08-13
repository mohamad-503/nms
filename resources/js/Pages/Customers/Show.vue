<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/ui/PageHeader.vue'
import Badge from '@/Components/ui/Badge.vue'
import { computed } from 'vue'

const props = defineProps({ customer: Object })

const statusMap = {
  active: { label: 'نشط', type: 'green' },
  suspended: { label: 'موقوف', type: 'amber' },
  expired: { label: 'منتهي', type: 'red' },
  inactive: { label: 'غير نشط', type: 'gray' },
}

const fmt = (n) => new Intl.NumberFormat('ar-IQ').format(n || 0)

const daysLeft = computed(() => {
  if (!props.customer.subscription_end) return null
  const end = new Date(props.customer.subscription_end)
  const now = new Date(); now.setHours(0,0,0,0)
  return Math.ceil((end - now) / (1000 * 60 * 60 * 24))
})

const invStatus = { paid: { label: 'مدفوعة', type: 'green' }, unpaid: { label: 'غير مدفوعة', type: 'red' }, partial: { label: 'جزئي', type: 'amber' }, cancelled: { label: 'ملغاة', type: 'gray' } }
const ticketStatus = { open: 'amber', assigned: 'blue', in_progress: 'brand', resolved: 'green', closed: 'gray' }

function renew() {
  const days = prompt('تمديد الاشتراك بأيام:', '30')
  if (!days) return
  router.post(route('customers.renew', props.customer.id), { days }, { preserveScroll: true })
}
function remove() {
  if (!confirm(`حذف المشترك "${props.customer.full_name}"؟`)) return
  router.delete(route('customers.destroy', props.customer.id))
}
</script>

<template>
  <AppLayout>
    <div class="flex items-center gap-2 mb-6">
      <Link :href="route('customers.index')" class="btn-ghost"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></Link>
      <PageHeader title="تفاصيل المشترك" subtitle="عرض كامل لبيانات المشترك" />
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
      <Link :href="route('customers.edit', customer.id)" class="btn-secondary">تعديل</Link>
      <button class="btn-primary" @click="renew">تجديد الاشتراك</button>
      <button v-if="customer.status==='active'" class="btn-secondary text-amber-600" @click="router.post(route('customers.suspend', customer.id), {}, { preserveScroll: true })">إيقاف</button>
      <button v-else class="btn-secondary text-green-600" @click="router.post(route('customers.activate', customer.id), {}, { preserveScroll: true })">تفعيل</button>
      <a :href="route('customers.contract', customer.id)" target="_blank" class="btn-secondary">طباعة عقد</a>
      <a :href="route('customers.invoice', customer.id)" target="_blank" class="btn-secondary">طباعة فاتورة</a>
      <button class="btn-danger mr-auto" @click="remove">حذف</button>
    </div>

    <div class="card p-6 mb-6">
      <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
        <img v-if="customer.profile_photo" :src="`/storage/${customer.profile_photo}`" class="w-24 h-24 rounded-2xl object-cover border-2 border-brand-200" />
        <div v-else class="w-24 h-24 rounded-2xl bg-brand-100 dark:bg-brand-500/20 text-brand-500 flex items-center justify-center text-3xl font-bold">{{ (customer.full_name||'?').charAt(0) }}</div>
        <div class="flex-1">
          <div class="flex items-center gap-3 flex-wrap">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ customer.full_name }}</h2>
            <Badge :type="statusMap[customer.status]?.type">{{ statusMap[customer.status]?.label }}</Badge>
          </div>
          <div class="flex flex-wrap gap-x-6 gap-y-1 mt-3 text-sm text-gray-500">
            <span>{{ customer.phone || '-' }}</span>
            <span v-if="customer.national_id">الرقم الوطني: {{ customer.national_id }}</span>
            <span>{{ [customer.city?.name, customer.area?.name, customer.address].filter(Boolean).join(' · ') }}</span>
          </div>
        </div>
        <div v-if="daysLeft !== null" class="text-center px-6 py-3 rounded-2xl" :class="daysLeft < 0 ? 'bg-red-50 dark:bg-red-500/10' : daysLeft <= 7 ? 'bg-amber-50 dark:bg-amber-500/10' : 'bg-green-50 dark:bg-green-500/10'">
          <p class="text-xs text-gray-400">الأيام المتبقية</p>
          <p class="text-2xl font-bold" :class="daysLeft < 0 ? 'text-red-500' : daysLeft <= 7 ? 'text-amber-500' : 'text-green-500'">{{ daysLeft }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <div class="card p-5">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">معلومات الاتصال</h3>
        <dl class="space-y-3 text-sm">
          <div class="flex justify-between"><dt class="text-gray-400">الهاتف</dt><dd class="font-medium">{{ customer.phone || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">الرقم الوطني</dt><dd class="font-medium">{{ customer.national_id || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">العنوان</dt><dd class="font-medium text-left">{{ customer.address || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">المدينة</dt><dd class="font-medium">{{ customer.city?.name || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">المنطقة</dt><dd class="font-medium">{{ customer.area?.name || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">البرج</dt><dd class="font-medium">{{ customer.tower?.name || '-' }}</dd></div>
        </dl>
      </div>
      <div class="card p-5">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">معلومات PPPoE</h3>
        <dl class="space-y-3 text-sm">
          <div class="flex justify-between"><dt class="text-gray-400">اسم المستخدم</dt><dd class="font-mono font-medium">{{ customer.pppoe_username || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">كلمة المرور</dt><dd class="font-mono font-medium">{{ customer.pppoe_password || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">IP ثابت</dt><dd class="font-mono font-medium">{{ customer.static_ip || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">MAC</dt><dd class="font-mono text-xs font-medium">{{ customer.mac_address || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">سرعة التحميل</dt><dd class="font-medium">{{ customer.download_speed || '-' }} Mbps</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">سرعة الرفع</dt><dd class="font-medium">{{ customer.upload_speed || '-' }} Mbps</dd></div>
        </dl>
      </div>
      <div class="card p-5">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">معلومات الاشتراك</h3>
        <dl class="space-y-3 text-sm">
          <div class="flex justify-between"><dt class="text-gray-400">الباقة</dt><dd class="font-medium">{{ customer.plan?.name || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">السعر الشهري</dt><dd class="font-medium">{{ fmt(customer.monthly_price) }} د.ع</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">تاريخ التركيب</dt><dd class="font-medium">{{ customer.installation_date || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">بداية الاشتراك</dt><dd class="font-medium">{{ customer.subscription_start || '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-400">نهاية الاشتراك</dt><dd class="font-medium">{{ customer.subscription_end || '-' }}</dd></div>
        </dl>
      </div>
    </div>

    <div v-if="customer.notes" class="card p-5 mb-6">
      <h3 class="font-bold text-gray-800 dark:text-white mb-3">ملاحظات</h3>
      <p class="text-sm text-gray-600 dark:text-slate-300 whitespace-pre-wrap">{{ customer.notes }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="card p-5">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">الفواتير ({{ customer.invoices?.length || 0 }})</h3>
        <div class="space-y-2">
          <div v-for="inv in customer.invoices" :key="inv.id" class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-slate-700/50 last:border-0">
            <div><p class="font-mono text-sm">{{ inv.invoice_number }}</p><p class="text-xs text-gray-400">{{ inv.issued_date }}</p></div>
            <div class="flex items-center gap-2"><span class="font-medium text-sm">{{ fmt(inv.total) }} د.ع</span><Badge :type="invStatus[inv.status]?.type">{{ invStatus[inv.status]?.label }}</Badge></div>
          </div>
          <p v-if="!customer.invoices?.length" class="text-center text-gray-400 py-4">لا توجد فواتير</p>
        </div>
      </div>
      <div class="card p-5">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">تذاكر الدعم ({{ customer.tickets?.length || 0 }})</h3>
        <div class="space-y-2">
          <div v-for="t in customer.tickets" :key="t.id" class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-slate-700/50 last:border-0">
            <div><p class="font-medium text-sm">{{ t.subject }}</p><p class="text-xs text-gray-400">{{ new Date(t.created_at).toLocaleDateString('ar-IQ') }}</p></div>
            <Badge :type="ticketStatus[t.status]">{{ t.status }}</Badge>
          </div>
          <p v-if="!customer.tickets?.length" class="text-center text-gray-400 py-4">لا توجد تذاكر</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
