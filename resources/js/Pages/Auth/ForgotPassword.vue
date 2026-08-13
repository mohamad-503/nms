<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const form = useForm({ email: '' })
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-50 via-white to-brand-100 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-brand-500 text-white text-2xl font-bold shadow-card mb-4">NM</div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">استعادة كلمة المرور</h1>
      </div>
      <div class="card p-8">
        <form @submit.prevent="form.post(route('password.email'))" class="space-y-4">
          <div><label class="label">البريد الإلكتروني</label><input v-model="form.email" type="email" required class="input" /></div>
          <button type="submit" class="btn-primary w-full" :disabled="form.processing">إرسال رابط الاستعادة</button>
          <div class="text-center"><Link href="/login" class="text-sm text-brand-600 hover:underline">العودة لتسجيل الدخول</Link></div>
        </form>
      </div>
    </div>
  </div>
</template>
