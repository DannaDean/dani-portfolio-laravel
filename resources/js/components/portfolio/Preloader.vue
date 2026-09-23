<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { gsap } from 'gsap';

const emit = defineEmits<{ complete: [] }>();
const root = ref<HTMLElement | null>(null);
let timeline: gsap.core.Timeline | null = null;
let fallbackTimer: number | null = null;
let completed = false;

function complete() {
    if (completed) return;
    completed = true;
    emit('complete');
}

onMounted(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        complete();
        return;
    }

    const bars = Array.from(root.value?.querySelectorAll<HTMLElement>('.bar') ?? []);
    if (!bars.length) {
        complete();
        return;
    }

    fallbackTimer = window.setTimeout(complete, 4500);
    gsap.set(bars, { yPercent: -100 });
    timeline = gsap.timeline({ onComplete: complete });
    timeline.to({}, { duration: 0.3 });
    timeline.to(bars, { yPercent: 0, duration: 0.8, stagger: { amount: 0.8, from: 'start' }, ease: 'power3.out' });
    timeline.to([...bars].reverse(), { yPercent: -100, duration: 0.8, stagger: { amount: 0.8, from: 'start' }, ease: 'power3.inOut' });
});

onUnmounted(() => {
    timeline?.kill();
    if (fallbackTimer !== null) window.clearTimeout(fallbackTimer);
});
</script>

<template>
    <div ref="root" class="preloader" aria-hidden="true">
        <div class="preloader-bars"><div v-for="index in 6" :key="index" class="bar" :class="`bar-${index - 1}`" /></div>
    </div>
</template>
