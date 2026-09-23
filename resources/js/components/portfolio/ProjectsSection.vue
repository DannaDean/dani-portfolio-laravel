<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { gsap } from 'gsap';
import { ArrowRight } from 'lucide-vue-next';
import PortfolioBox from './PortfolioBox.vue';
import type { Project } from '@/types/portfolio';
import { useFlowerMotion } from '@/composables/useFlowerMotion';
import { portfolioImageUrl } from '@/lib/portfolioImageUrl';

const props = defineProps<{ projects: Project[] }>();
const visibleCount = ref(2);
const visibleProjects = computed(() => props.projects.slice(0, visibleCount.value));
const root = ref<HTMLElement | null>(null);
useFlowerMotion(root, 'parallax');

function reveal(start: number) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const cards = Array.from(root.value?.querySelectorAll('.project-card') ?? []).slice(start);
    gsap.fromTo(cards, { opacity: 0, y: 20 }, { opacity: 1, y: 0, stagger: 0.1, duration: 0.5 });
}

onMounted(() => reveal(0));
watch(visibleCount, async (count, previous) => {
    if (count <= previous) return;
    await nextTick();
    reveal(previous);
});
onUnmounted(() => gsap.killTweensOf(root.value?.querySelectorAll('.project-card') ?? []));

function categories(value: Project['categories']): string[] {
    if (Array.isArray(value)) return value;
    if (!value) return [];
    try { return JSON.parse(value) as string[]; } catch { return []; }
}

</script>

<template>
    <section id="projects" ref="root" class="projects" aria-label="Projects">
      <div class="container">
        <PortfolioBox color="#faebc5" flower="/portfolio/images/flowers/flower-2.png" title="Development is in the details." text="My work is rooted in clean architecture, scalable code, and proven UX patterns. Every line is written with purpose — ensuring your project not only works but works beautifully." />
        <div class="projects-container">
            <component :is="project.link ? 'a' : 'article'" v-for="project in visibleProjects" :key="project.id" class="project-card" :href="project.link || undefined" :target="project.link ? '_blank' : undefined" :rel="project.link ? 'noopener noreferrer' : undefined">
                <div class="images-block">
                    <div class="proj-img-one"><img :src="portfolioImageUrl(project.desk_img) || '/portfolio/images/default.jpg'" :alt="`${project.title} desktop view`" loading="eager" /></div>
                    <div v-if="project.mobile_img" class="proj-img-two"><img :src="portfolioImageUrl(project.mobile_img)" :alt="`${project.title} mobile view`" loading="eager" /></div>
                </div>
                <div class="project-content"><h3>{{ project.title }}</h3><div class="categories"><span v-for="category in categories(project.categories)" :key="category" class="category">{{ category }}</span></div></div>
            </component>
        </div>
        <div v-if="projects.length > 2" class="projects-btn"><button class="btn" type="button" @click="visibleCount = visibleCount >= projects.length ? 2 : visibleCount + 2">{{ visibleCount >= projects.length ? 'Show Less' : 'Load More' }} <ArrowRight :size="24" /></button></div>
      </div>
    </section>
</template>
