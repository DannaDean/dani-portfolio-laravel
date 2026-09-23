<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';
import SiteHeader from '@/components/portfolio/SiteHeader.vue';
import HeroSection from '@/components/portfolio/HeroSection.vue';
import ProjectsSection from '@/components/portfolio/ProjectsSection.vue';
import AboutSection from '@/components/portfolio/AboutSection.vue';
import SkillsSection from '@/components/portfolio/SkillsSection.vue';
import FactsSection from '@/components/portfolio/FactsSection.vue';
import ContactSection from '@/components/portfolio/ContactSection.vue';
import Preloader from '@/components/portfolio/Preloader.vue';
import type { Project, Skill, Fact } from '@/types/portfolio';
import '../../css/portfolio.scss';

defineProps<{ projects: Project[]; skills: Skill[]; facts: Fact[] }>();

const dark = ref(false);
const introExpiryKey = 'portfolio-intro-expires-at';
const introInterval = 30 * 24 * 60 * 60 * 1000;

function shouldPlayIntro(): boolean {
    try {
        const expiresAt = Number(localStorage.getItem(introExpiryKey));
        return !Number.isFinite(expiresAt) || expiresAt <= Date.now();
    } catch {
        return true;
    }
}

const loading = ref(shouldPlayIntro());

if (loading.value) {
    try {
        localStorage.setItem(introExpiryKey, String(Date.now() + introInterval));
    } catch {
        // The animation still runs when browser storage is unavailable.
    }
}

function finishIntro() {
    loading.value = false;
    try {
        localStorage.setItem(introExpiryKey, String(Date.now() + introInterval));
    } catch {
        // The page still works when browser storage is unavailable.
    }
}

onMounted(() => {
    try {
        dark.value = localStorage.getItem('theme') === 'dark';
    } catch {
        dark.value = false;
    }
    document.body.classList.toggle('dark', dark.value);
    document.body.classList.toggle('light', !dark.value);
});
onUnmounted(() => document.body.classList.remove('dark', 'light'));
function toggleTheme() {
    dark.value = !dark.value;
    try {
        localStorage.setItem('theme', dark.value ? 'dark' : 'light');
    } catch {
        // Theme changes still work for this visit.
    }
    document.body.classList.toggle('dark', dark.value);
    document.body.classList.toggle('light', !dark.value);
}
</script>

<template>
    <Head title="Home | My Portfolio">
        <meta name="description" content="Daniela Ciubari is a full stack web developer creating responsive, accessible websites and applications." />
    </Head>
    <div class="inner-wrapper">
        <SiteHeader :dark="dark" @toggle-theme="toggleTheme" />
        <Preloader v-if="loading" @complete="finishIntro" />
        <main>
            <HeroSection />
            <ProjectsSection :projects="projects" />
            <AboutSection />
            <SkillsSection :skills="skills" />
            <FactsSection :facts="facts" />
        </main>
        <ContactSection />
    </div>
</template>
