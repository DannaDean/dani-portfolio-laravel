<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import PortfolioBox from './PortfolioBox.vue';

gsap.registerPlugin(ScrollTrigger);
const root = ref<HTMLElement | null>(null);
let labelsAnimation: gsap.core.Tween | null = null;
let flowerAnimation: gsap.core.Tween | null = null;

onMounted(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const flowerCard = root.value?.querySelector('.about-one');
    const flower = flowerCard?.querySelector('.flower-1');
    if (flower && flowerCard) {
        flowerAnimation = gsap.fromTo(flower, { y: 180 }, {
            y: -20, ease: 'none',
            scrollTrigger: { trigger: flowerCard, start: 'top 95%', end: 'top 35%', scrub: 0.5, invalidateOnRefresh: true },
        });
    }
    const labels = root.value?.querySelectorAll('.about-three .category');
    if (!labels?.length) return;
    labelsAnimation = gsap.fromTo(labels, { y: -1080, opacity: 0, visibility: 'visible' }, {
        y: 0, opacity: 1, duration: 1.2, ease: 'bounce.out', stagger: 0.08,
        scrollTrigger: { trigger: root.value, start: 'top 70%', toggleActions: 'play none none none' },
    });
});

onUnmounted(() => {
    flowerAnimation?.scrollTrigger?.kill();
    flowerAnimation?.kill();
    labelsAnimation?.scrollTrigger?.kill();
    labelsAnimation?.kill();
});

const traits = ['Curiosity', 'Responsability', 'Adaptability', 'Empathy', 'Creativity', 'Innovation', 'Dedication', 'Perseverance'];
</script>

<template>
    <section id="meet" ref="root" class="about" aria-label="Meet Daniela">
      <div class="container">
        <div class="about-img"><img src="/portfolio/images/daniela.png" alt="Ciubari Daniela" loading="lazy" /></div>
        <PortfolioBox 
            class="about-one" 
            color="#ffdf2b" flower="/portfolio/images/flowers/flower-3.png" 
            title="Nice to meet you!" subtitle="I'm Ciubari Daniela, a Full Stack Web Developer!" 
            text="My mission is to help companies like yours achieve their goals through modern and efficient web development solutions. I specialize in crafting responsive, user-centered interfaces with clean code and modern frameworks. While I can build across the stack, my strength lies in frontend development — turning designs into smooth, accessible, and engaging digital experiences." 
        />
        <PortfolioBox 
            class="about-two" 
            color="#eeee39"
            subtitle="The perfect blend of code and UX." text="I transform designs into fast, accessible, and intuitive interfaces, ensuring a seamless user experience that not only looks great but also performs flawlessly across all devices."
         />
        <PortfolioBox 
            class="about-three" 
            color="#feadd2"
            subtitle="What you can count on from me."
        >
                <div class="traits"><span v-for="trait in traits" :key="trait" class="category">{{ trait }}</span></div>
        </PortfolioBox>
      </div>
    </section>
</template>
