<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/ui/PageHeader.vue'
import { computed, ref } from 'vue'

const props = defineProps({
  plans: Array, areas: Array, towers: Array, cities: Array,
})

const form = useForm({
  full_name: '', phone: '', national_id: '', address: '',
  city_id: '', area_id: '', tower_id: '',
  pppoe_username: '', pppoe_password: '', plan_id: '',
  download_speed: 0, upload_speed: 0, static_ip: '', mac_address: '',
  installation_date: new Date().toISOString().slice(0, 10),
  subscription_start: new Date().toISOString().slice(0, 10),
  subscription_end: '', monthly_price: 0,
  status: 'active', notes: '', profile_photo: null,
})

const photoPreview = ref('')

function onPhoto(e) {
  const file = e.target.files?.[0]
  if (!file) return
  form.profile_photo = file
  photoPreview.value = URL.createObjectURL(file)
}

function onPlanChange() {
  const p = props.plans.find(p => p.id === Number(form.plan_id))
  if (p) { form.download_speed = p.download_speed; form.upload_speed = p.upload_speed; form.monthly_price = p.price }
}

const filteredTowers = computed(() => form.area_id ? props.towers.filter(t => t.area_id === Number(form.area_id)) : props.towers)

function submit() {
  form.post(route('customers.store'), { forceFormData: true })
}
</script>

<template>
  <AppLayout>
    <div class="flex items-center gap-2 mb-6">
      <Link :href="route('customers.index')" class="btn-ghost"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></Link>
      <PageHeader title="مشترك جديد" subtitle="إضافة مشترك جديد إلى النظام" />
    </div>

    <form @submit.prevent="submit" class="card p-6">
      <div class="flex items-center gap-4 mb-6 p-4 rounded-2xl bg-gray-50 dark:bg-slate-700/40">
        <img v-if="photoPreview" :src="photoPreview" class="w-20 h-20 rounded-2xl object-cover border-2 border-brand-200" />
        <div v-else class="w-20 h-20 rounded-2xl bg-brand-100 dark:bg-brand-500/20 text-brand-500 flex items-center justify-center"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg></div>
        <label class="btn-secondary cursor-pointer">رفع صورة<input type="file" accept="image/*" class="hidden" @change="onPhoto" /></label>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="label">الاسم الكامل *</label><input v-model="form.full_name" required class="input" /></div>
        <div><label class="label">رقم الهاتف</label><input v-model="form.phone" class="input" /></div>
        <div><label class="label">الرقم الوطني</label><input v-model="form.national_id" class="input" /></div>
        <div><label class="label">المدينة</label><select v-model="form.city_id" class="input"><option value="">—</option><option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option></select></div>
        <div><label class="label">العنوان</label><input v-model="form.address" class="input" /></div>
        <div><label class="label">المنطقة</label><select v-model="form.area_id" class="input"><option value="">—</option><option v-for="a in areas" :key="a.id" :value="a.id">{{ a.name }}</option></select></div>
        <div><label class="label">البرج</label><select v-model="form.tower_id" class="input"><option value="">—</option><option v-for="t in filteredTowers" :key="t.id" :value="t.id">{{ t.name }}</option></select></div>
        <div><label class="label">الباقة</label><select v-model="form.plan_id" @change="onPlanChange" class="input"><option value="">—</option><option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }} ({{ p.price }})</option></select></div>
        <div><label class="label">اسم مستخدم PPPoE</label><input v-model="form.pppoe_username" class="input" /></div>
        <div><label class="label">كلمة مرور PPPoE</label><input v-model="form.pppoe_password" class="input" /></div>
        <div><label class="label">سرعة التحميل (Mbps)</label><input v-model="form.download_speed" type="number" class="input" /></div>
        <div><label class="label">سرعة الرفع (Mbps)</label><input v-model="form.upload_speed" type="number" class="input" /></div>
        <div><label class="label">IP ثابت</label><input v-model="form.static_ip" class="input" /></div>
        <div><label class="label">عنوان MAC</label><input v-model="form.mac_address" class="input" /></div>
        <div><label class="label">تاريخ التركيب</label><input v-model="form.installation_date" type="date" class="input" /></div>
        <div><label class="label">بداية الاشتراك</label><input v-model="form.subscription_start" type="date" class="input" /></div>
        <div><label class="label">نهاية الاشتراك</label><input v-model="form.subscription_end" type="date" class="input" /></div>
        <div><label class="label">السعر الشهري (د.ع)</label><input v-model="form.monthly_price" type="number" class="input" /></div>
        <div><label class="label">الحالة</label><select v-model="form.status" class="input"><option value="active">نشط</option><option value="suspended">موقوف</option><option value="expired">منتهي</option><option value="inactive">غير نشط</option></select></div>
        <div class="md:col-span-2"><label class="label">ملاحظات</label><textarea v-model="form.notes" class="input" rows="2"></textarea></div>
      </div>

      <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100 dark:border-slate-700">
        <Link :href="route('customers.index')" class="btn-secondary">إلغاء</Link>
        <button type="submit" class="btn-primary" :disabled="form.processing"><span v-if="form.processing">جاري الحفظ...</span><span v-else>حفظ المشترك</span></button>
      </div>
    </form>
  </AppLayout>
</template>
