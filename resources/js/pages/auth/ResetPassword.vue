<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
const props = defineProps<{ token: string; email: string }>();
const form = useForm({ token: props.token, email: props.email, password: '', password_confirmation: '' });
const submit = () => form.post('/reset-password', { onFinish: () => form.reset('password', 'password_confirmation') });
</script>
<template>
    <Head title="Reset password" />
    <AuthLayout title="Reset password">
        <form @submit.prevent="submit">
            <label for="email">Email address</label>
            <input id="email" v-model="form.email" type="email" autocomplete="email" required />
            <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
            <label for="password">New password</label>
            <input id="password" v-model="form.password" type="password" autocomplete="new-password" required autofocus />
            <p v-if="form.errors.password" class="form-error">{{ form.errors.password }}</p>
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" required />
            <p v-if="form.errors.password_confirmation" class="form-error">{{ form.errors.password_confirmation }}</p>
            <button type="submit" :disabled="form.processing">Reset password</button>
        </form>
    </AuthLayout>
</template>
