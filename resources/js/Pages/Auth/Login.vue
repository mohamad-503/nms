<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({ email: '', password: '' })
const error = ref('')

function submit() {
  form.post(route('login.store'), {
    onError: (e) => { error.value = e.email || 'بيانات الدخول غير صحيحة' },
    onSuccess: () => {},
  })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-50 via-white to-brand-100 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-brand-500 text-white text-2xl font-bold shadow-card mb-4">NM</div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">نظام NM</h1>
        <p class="text-gray-500 dark:text-slate-400 mt-1">نظام إدارة مزود الخدمة</p>
      </div>
      <div class="card p-8">
        <h2 class="text-lg font-semibold mb-6">تسجيل الدخول</h2>
        <form @submit.prevent="submit" class="space-y-4">
          <div><label class="label">البريد الإلكتروني</label><input v-model="form.email" type="email" required class="input" placeholder="admin@nm.iq" /></div>
          <div><label class="label">كلمة المرور</label><input v-model="form.password" type="password" required class="input" placeholder="••••••••" /></div>
          <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
          <button type="submit" class="btn-primary w-full" :disabled="form.processing"><span v-if="form.processing">جاري الدخول...</span><span v-else>دخول</span></button>
        </form>
        <div class="mt-4 text-center"><Link href="/forgot-password" class="text-sm text-brand-600 hover:underline">نسيت كلمة المرور؟</Link></div>
      </div>
    </div>
  </div>
</template>
