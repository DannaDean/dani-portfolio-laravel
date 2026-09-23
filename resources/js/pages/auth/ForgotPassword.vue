<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
defineProps<{ status?: string }>();
const form = useForm({ email: '' });
const submit = () => form.post('/forgot-password');
</script>
<template>
    <Head title="Forgot password" />
    <AuthLayout title="Forgot password">
        <p>Enter your email address and we'll send you a reset link.</p>
        <p v-if="status" class="form-status">{{ status }}</p>
        <form @submit.prevent="submit">
            <label for="email">Email address</label>
            <input id="email" v-model="form.email" type="email" autocomplete="email" required autofocus />
            <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
            <button type="submit" :disabled="form.processing">Send reset link</button>
        </form>
        <Link href="/login">Back to log in</Link>
    </AuthLayout>
</template>
