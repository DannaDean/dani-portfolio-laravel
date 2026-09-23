<script setup lang="ts">
defineProps<{ title: string; subtitle?: string }>();
</script>
<template>
    <main class="auth-shell">
        <a class="auth-back" href="/">← Back to portfolio</a>
        <section class="auth-card">
            <div class="auth-content">
                <span class="auth-eyebrow">DANIELA CIUBARI · PORTFOLIO</span>
                <h1>{{ title }}</h1>
                <h2 v-if="subtitle">{{ subtitle }}</h2>
                <slot />
            </div>
            <img class="auth-flower" src="/portfolio/images/flowers/flower-4.png" alt="" />
        </section>
    </main>
</template>
