<template>
    <div class="relative h-full w-full overflow-hidden" :class="containerClass" @mouseenter="pause" @mouseleave="resume">
        <Transition name="image-fade" mode="out-in">
            <img v-if="activeImage" :key="activeImage" :src="assetUrl(activeImage)" :alt="alt" :class="imageClass" loading="lazy" decoding="async" />
            <span v-else :key="'empty-image'" class="flex h-full w-full items-center justify-center text-4xl text-brand-300">{{ fallback }}</span>
        </Transition>

        <template v-if="images.length > 1">
            <button type="button" @click.stop="previous" class="absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-brand-700 shadow-soft transition hover:bg-white" aria-label="Previous image">‹</button>
            <button type="button" @click.stop="next" class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-brand-700 shadow-soft transition hover:bg-white" aria-label="Next image">›</button>
            <div class="absolute bottom-2 left-1/2 flex -translate-x-1/2 items-center gap-1.5 rounded-full bg-brand-950/45 px-2 py-1">
                <button v-for="(_, index) in images" :key="index" type="button" @click.stop="goTo(index)" :class="index === currentIndex ? 'w-4 bg-white' : 'w-1.5 bg-white/60'" class="h-1.5 rounded-full transition-all" :aria-label="`Show image ${index + 1}`"></button>
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    images: { type: Array, default: () => [] },
    alt: { type: String, default: '' },
    imageClass: { type: String, default: 'h-full w-full object-cover' },
    containerClass: { type: String, default: '' },
    fallback: { type: String, default: '🍰' },
    autoPlay: { type: Boolean, default: true },
    interval: { type: Number, default: 4200 },
});

const currentIndex = ref(0);
let timer = null;
const images = computed(() => (props.images || []).filter(Boolean));
const activeImage = computed(() => images.value[currentIndex.value] || images.value[0] || '');
const assetUrl = path => path?.startsWith('http') ? path : '/storage/' + path;

const goTo = index => { currentIndex.value = Math.max(0, Math.min(index, images.value.length - 1)); };
const next = () => { if (images.value.length) currentIndex.value = (currentIndex.value + 1) % images.value.length; };
const previous = () => { if (images.value.length) currentIndex.value = (currentIndex.value - 1 + images.value.length) % images.value.length; };
const pause = () => { if (timer) { clearInterval(timer); timer = null; } };
const resume = () => { if (props.autoPlay && images.value.length > 1 && !timer) timer = window.setInterval(next, props.interval); };

watch(images, () => { if (currentIndex.value >= images.value.length) currentIndex.value = 0; resume(); }, { deep: true });
onMounted(resume);
onBeforeUnmount(pause);
</script>
