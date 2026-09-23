<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ArrowRight, X } from 'lucide-vue-next';
import PortfolioBox from './PortfolioBox.vue';
import { useFlowerMotion } from '@/composables/useFlowerMotion';

const open = ref(false);
const sent = ref(false);
const copied = ref(false);
const root = ref<HTMLElement | null>(null);
useFlowerMotion(root, 'rotate');
const form = useForm({ name: '', email: '', text: '' });

async function copyEmail() {
    await navigator.clipboard.writeText('dciubari@gmail.com');
    copied.value = true;
    window.setTimeout(() => { copied.value = false; }, 2000);
}

function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => { form.reset(); open.value = false; sent.value = true; },
    });
}
</script>

<template>
    <footer ref="root">
      <div id="getInTouch" class="container">
        <PortfolioBox color="rgba(255, 157, 101, 1)" flower="/portfolio/images/flowers/flower-4.png" title="Thanks for stopping by!" subtitle="I’d love to chat with you about how I can help. Get in touch!">
            <div class="footer-boxes">
                <div class="footer-box"><p>Email</p><div class="copy-email"><button class="copy-label" type="button" @click="copyEmail">{{ copied ? 'Copied' : 'Copy' }}</button><p>dciubari@gmail.com</p></div></div>
                <button class="footer-box" type="button" @click="open = true"><p>Send a message</p><ArrowRight :size="22" /></button>
                <a class="footer-box" href="https://www.linkedin.com/in/daniela-ciubari-615236257/" target="_blank" rel="noopener noreferrer"><p>Linkedin</p><ArrowRight :size="22" /></a>
                <a class="footer-box" href="#top"><p>Back to top</p><ArrowRight :size="22" /></a>
            </div>
        </PortfolioBox>
        <p v-if="sent" class="contact-success" role="status">Thanks! Your message has been received.</p>
      </div>
        <Transition name="portfolio-popup"><div v-if="open" class="popup-overlay" @click.self="open = false">
            <div class="popup-content" role="dialog" aria-modal="true" aria-labelledby="contact-title">
                <button class="popup-close" type="button" aria-label="Close contact form" @click="open = false"><X :size="32" /></button>
                <h2 id="contact-title">Send a message</h2>
                <form @submit.prevent="submit">
                    <label class="custom-field"><input v-model="form.name" type="text" required maxlength="255" autocomplete="name" placeholder="Your Name" /><small v-if="form.errors.name">{{ form.errors.name }}</small></label>
                    <label class="custom-field"><input v-model="form.email" type="email" required maxlength="255" autocomplete="email" placeholder="Your Email" /><small v-if="form.errors.email">{{ form.errors.email }}</small></label>
                    <label class="custom-field"><textarea v-model="form.text" rows="5" required maxlength="10000" placeholder="Your Message" /><small v-if="form.errors.text">{{ form.errors.text }}</small></label>
                    <button class="btn" type="submit" :disabled="form.processing">{{ form.processing ? 'Sending…' : 'Send' }} <ArrowRight :size="24" /></button>
                </form>
            </div>
        </div></Transition>
    </footer>
</template>
