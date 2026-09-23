<script setup lang="ts">
import { ref } from 'vue';
import { Plus } from 'lucide-vue-next';
import type { Fact } from '@/types/portfolio';

defineProps<{ facts: Fact[] }>();
const openId = ref<number | null>(null);
</script>

<template>
    <section id="facts" class="fact" aria-labelledby="facts-title">
      <div class="container">
        <h2 id="facts-title">Facts</h2>
        <div class="fact-container">
          <div v-for="column in 2" :key="column" class="fact-section">
            <div v-for="fact in facts.filter((_, index) => index % 2 === column - 1)" :key="fact.id" class="fact-item">
                <button class="fact-title" type="button" :aria-expanded="openId === fact.id" @click="openId = openId === fact.id ? null : fact.id"><h3>{{ fact.title }}</h3><span :class="{ active: openId === fact.id }"><Plus :size="24" /></span></button>
                <div class="fact-content" :class="{ show: openId === fact.id }" :style="{ maxHeight: openId === fact.id ? '2000px' : '0' }"><div class="fact-rich-text" v-html="fact.text"></div></div>
            </div>
          </div>
        </div>
      </div>
    </section>
</template>
