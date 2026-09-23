<script setup lang="ts">
import { ArrowRight } from 'lucide-vue-next';
import type { Skill } from '@/types/portfolio';
import { portfolioImageUrl } from '@/lib/portfolioImageUrl';

defineProps<{ skills: Skill[] }>();

</script>

<template>
    <section class="skills" aria-label="Coding skills">
      <div class="container">
        <div class="marquee-container">
            <p>Coding Skills:</p>
            <div v-if="skills.length" class="marquee marquee--reverse">
                <div v-for="copy in 2" :key="copy" class="marquee__group" :aria-hidden="copy === 2">
                    <div v-for="skill in skills" :key="skill.id" class="logo-item"><img v-if="skill.image" :src="portfolioImageUrl(skill.image)" :alt="skill.title || 'Skill logo'" loading="eager" decoding="async" /><span v-if="skill.title" class="logo-title">{{ skill.title }}</span></div>
                </div>
            </div>
        </div>
        <div class="conect-box"><h3>Ready to get started?</h3><a class="btn" href="#getInTouch" style="background-color: rgba(208, 150, 223, 1)">Get in Touch <ArrowRight :size="24" /></a></div>
      </div>
    </section>
</template>
