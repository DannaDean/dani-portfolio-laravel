<script setup lang="ts">
import { ref } from 'vue';
import { Moon, Sun } from 'lucide-vue-next';

defineProps<{ dark: boolean }>();
const emit = defineEmits<{ toggleTheme: [] }>();
const open = ref(false);
const links = [
    { label: 'Works', href: '#projects' },
    { label: 'Meet Daniela', href: '#meet' },
    { label: 'Facts', href: '#facts' },
    { label: 'Get in Touch', href: '#getInTouch' },
];
</script>

<template>
    <header>
        <div class="container">
            <nav id="site-navigation" :class="open ? 'open' : 'hidden'" aria-label="Main navigation">
                <a v-for="link in links" :key="link.href" class="category" :href="link.href" @click="open = false">{{ link.label }}</a>
                <button class="category theme-toggle" type="button" :aria-label="dark ? 'Use light theme' : 'Use dark theme'" @click="emit('toggleTheme')">
                    <Sun v-if="dark" :size="18" /><Moon v-else :size="18" />
                </button>
            </nav>
            <button class="lines" :class="{ active: open }" type="button" :aria-expanded="open" aria-controls="site-navigation" :aria-label="open ? 'Close menu' : 'Open menu'" @click="open = !open"><span class="line"></span><span class="line"></span><span class="line"></span></button>
        </div>
    </header>
</template>
