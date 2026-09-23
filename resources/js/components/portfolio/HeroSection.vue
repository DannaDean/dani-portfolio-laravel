<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { gsap } from 'gsap';
import { ArrowRight } from 'lucide-vue-next';
import PortfolioBox from './PortfolioBox.vue';
import { useFlowerMotion } from '@/composables/useFlowerMotion';

const root = ref<HTMLElement | null>(null);
useFlowerMotion(root, 'rotate');
let entrance: gsap.core.Timeline | null = null;

onMounted(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const letters = root.value?.querySelectorAll('.name .char');
    const box = root.value?.querySelector('.box');
    if (!letters || !box) return;
    entrance = gsap.timeline();
    entrance.to(letters, { opacity: 1, y: 0, duration: 0.8, ease: 'back.out(1.7)', stagger: { amount: 0.6, from: 'start' } });
    entrance.to(box, { opacity: 1, y: 0, duration: 1, ease: 'power3.out' }, 0);
});

onUnmounted(() => entrance?.kill());

const cards = [
    { image: '/portfolio/images/daniela.png', title: 'Home of Ciubari Daniela', text: 'An independent web developer building performant, scalable websites and apps.' },
    { image: '/portfolio/images/desktop.png', title: 'Web Development', text: 'Clean, efficient code built for performance, security, and long-term maintainability.' },
    { image: '/portfolio/images/mobile.png', title: 'Front-End & Back-End', text: 'Responsive interfaces and powerful back-end logic that bring your ideas to life.' },
];
</script>

<template>
    <section id="top" ref="root" class="hero" aria-labelledby="hero-title">
      <div class="container">
        <h1 id="hero-title" class="name"><span v-for="(char, index) in 'Daniela Ciubari'.split('')" :key="index" class="char">{{ char === ' ' ? '\u00a0' : char }}</span></h1>
        <PortfolioBox color="#f889e7" flower="/portfolio/images/flowers/flower-1.png">
            <template #title>Here to bring your ideas to life <br /> and drive business success.</template>
            <template #text>Web applications that convert, built to perform seamlessly. <br /> Scalable, reliable, and crafted with precision.</template>
            <div class="box-btn"><a class="btn" href="#getInTouch">Get in Touch <ArrowRight :size="24" /></a></div>
        </PortfolioBox>
        <div class="hero-cards">
            <article v-for="card in cards" :key="card.title" class="hero-card">
                <img class="hero-card-image" :src="card.image" :alt="card.title" />
                <div class="hero-card-text"><h3>{{ card.title }}</h3><p>{{ card.text }}</p></div>
            </article>
        </div>
      </div>
    </section>
</template>
