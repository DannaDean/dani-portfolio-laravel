<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
defineProps<{ status?: string; canResetPassword: boolean }>();
const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post('/login', { onFinish: () => form.reset('password') });
</script>
<template>
    <Head title="Log in" />
    <AuthLayout title="Login" subtitle="Ready to make progress? Let’s go!">
        <p v-if="status" class="form-status">{{ status }}</p>
        <p v-if="form.hasErrors" class="form-error login-error" role="alert">
            {{ form.errors.email || form.errors.password || 'Please check your details and try again.' }}
        </p>
        <form @submit.prevent="submit">
            <label for="email">Email address</label>
            <input id="email" v-model="form.email" type="email" autocomplete="email" required autofocus />
            <label for="password">Password</label>
            <input id="password" v-model="form.password" type="password" autocomplete="current-password" required />
            <label class="check-label"><input v-model="form.remember" type="checkbox" /> Remember me</label>
            <button type="submit" :disabled="form.processing">{{ form.processing ? 'Signing in…' : 'Login' }} <span aria-hidden="true">↗</span></button>
        </form>
        <Link v-if="canResetPassword" href="/forgot-password">Forgot your password?</Link>
    </AuthLayout>
</template>
