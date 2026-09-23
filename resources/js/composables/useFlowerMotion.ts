import { onMounted, onUnmounted, type Ref } from 'vue';
import { gsap } from 'gsap';

export function useFlowerMotion(root: Ref<HTMLElement | null>, mode: 'rotate' | 'parallax') {
    let flower: HTMLElement | null = null;
    let frame = 0;

    function update() {
        frame = 0;
        if (!flower) return;

        if (mode === 'rotate') {
            const maxScroll = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
            gsap.to(flower, { rotation: (window.scrollY / maxScroll) * 360, duration: 0.1, ease: 'none', overwrite: true });
        } else {
            gsap.to(flower, { y: -window.scrollY * 0.3, duration: 0.1, ease: 'none', overwrite: true });
        }
    }

    function onScroll() {
        if (!frame) frame = requestAnimationFrame(update);
    }

    onMounted(() => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        flower = root.value?.querySelector('.flower-1') ?? null;
        if (!flower) return;
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    });

    onUnmounted(() => {
        window.removeEventListener('scroll', onScroll);
        if (frame) cancelAnimationFrame(frame);
        if (flower) gsap.killTweensOf(flower);
    });
}
