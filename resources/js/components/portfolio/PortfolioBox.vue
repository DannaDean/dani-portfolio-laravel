<script setup lang="ts">
defineProps<{ title?: string; subtitle?: string; text?: string; flower?: string; color?: string }>();
</script>

<template>
    <div class="box" :style="{ backgroundColor: color }">
        <div class="box-content">
            <h2 v-if="title || $slots.title"><slot name="title">{{ title }}</slot></h2>
            <h3 v-if="subtitle">{{ subtitle }}</h3>
            <p v-if="text || $slots.text"><slot name="text">{{ text }}</slot></p>
            <slot />
        </div>
        <img v-if="flower" class="flower-1" :src="flower" alt="" aria-hidden="true" />
    </div>
</template>
