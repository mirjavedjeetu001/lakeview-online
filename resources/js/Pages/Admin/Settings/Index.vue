<template>
    <AdminLayout active-menu="settings" page-title="Settings">
        <div class="max-w-4xl">
            <!-- Tab Navigation -->
            <div class="flex gap-1 bg-white rounded-xl p-1.5 shadow-sm border border-brand-100 mb-6 overflow-x-auto">
                <button v-for="(group, key) in groupedSettings" :key="key"
                    @click="activeTab = key"
                    :class="activeTab === key ? 'bg-gold-500 text-brand-950' : 'text-brand-600 hover:bg-brand-50'"
                    class="px-5 py-2.5 rounded-lg text-sm font-bold transition whitespace-nowrap flex items-center gap-2">
                    <span>{{ tabIcons[key] || '⚙️' }}</span>
                    {{ tabLabels[key] || key }}
                </button>
            </div>

            <!-- Settings Form -->
            <form @submit.prevent="saveSettings" class="space-y-6">
                <div v-for="(group, groupName) in groupedSettings" :key="groupName" v-show="activeTab === groupName" class="bg-white rounded-2xl shadow-sm border border-brand-100 p-6">
                    <h3 class="font-serif font-bold text-brand-900 text-lg mb-1">{{ tabLabels[groupName] || groupName }}</h3>
                    <p class="text-sm text-brand-400 mb-5">{{ tabDescriptions[groupName] || '' }}</p>
                    <div class="space-y-5">
                        <div v-for="setting in group" :key="setting.key">
                            <!-- Hero Images: Multiple image URLs -->
                            <div v-if="setting.key === 'hero_images'">
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">Hero Images (Multiple)</label>
                                <p class="text-xs text-brand-400 mb-3">Add multiple hero banner images. They will rotate on the homepage.</p>
                                <div v-for="(img, i) in heroImages" :key="i" class="flex items-center gap-2 mb-2">
                                    <input v-model="heroImages[i]" type="url" placeholder="https://example.com/image.jpg" class="flex-1 rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-2.5 text-brand-900 transition outline-none text-sm" />
                                    <div v-if="heroImages[i]" class="w-10 h-10 rounded-lg overflow-hidden border border-brand-100 flex-shrink-0">
                                        <img :src="heroImages[i]" class="w-full h-full object-cover" @error="heroImages[i] = ''" />
                                    </div>
                                    <button type="button" @click="heroImages.splice(i, 1)" class="text-red-500 hover:text-red-700 px-2">✕</button>
                                </div>
                                <button type="button" @click="heroImages.push('')" class="text-sm text-gold-600 font-bold hover:text-gold-700 transition">+ Add Image</button>
                            </div>

                            <!-- Boolean settings: Toggle -->
                            <div v-else-if="setting.key === 'promo_banner_active'" class="flex items-center gap-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="settingValues[setting.key]" :checked="settingValues[setting.key] === '1' || settingValues[setting.key] === true" @change="settingValues[setting.key] = $event.target.checked ? '1' : '0'" class="w-5 h-5 rounded text-gold-500 focus:ring-gold-400" />
                                    <span class="ml-2 text-sm font-medium text-brand-700">Enable Promo Banner</span>
                                </label>
                            </div>

                            <!-- Image URL fields: with preview -->
                            <div v-else-if="setting.key === 'site_logo' || setting.key === 'hero_image'">
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">{{ setting.key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) }}</label>
                                <div class="flex items-center gap-3">
                                    <input v-model="settingValues[setting.key]" type="url" placeholder="https://example.com/image.jpg" class="flex-1 rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                                    <div v-if="settingValues[setting.key]" class="w-12 h-12 rounded-lg overflow-hidden border border-brand-100 flex-shrink-0">
                                        <img :src="settingValues[setting.key]" class="w-full h-full object-cover" @error="settingValues[setting.key] = ''" />
                                    </div>
                                </div>
                                <p v-if="settingHints[setting.key]" class="text-xs text-brand-400 mt-1">{{ settingHints[setting.key] }}</p>
                            </div>

                            <!-- Short text fields: single line input -->
                            <div v-else-if="shortTextFields.includes(setting.key)">
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">{{ setting.key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) }}</label>
                                <input v-model="settingValues[setting.key]" type="text" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                                <p v-if="settingHints[setting.key]" class="text-xs text-brand-400 mt-1">{{ settingHints[setting.key] }}</p>
                            </div>

                            <!-- Number fields -->
                            <div v-else-if="setting.key.startsWith('min_order')">
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">{{ setting.key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) }} (৳)</label>
                                <input v-model="settingValues[setting.key]" type="number" min="0" step="50" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                                <p v-if="settingHints[setting.key]" class="text-xs text-brand-400 mt-1">{{ settingHints[setting.key] }}</p>
                            </div>

                            <!-- Long text: textarea -->
                            <div v-else>
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">{{ setting.key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) }}</label>
                                <textarea v-model="settingValues[setting.key]" rows="2" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none resize-y"></textarea>
                                <p v-if="settingHints[setting.key]" class="text-xs text-brand-400 mt-1">{{ settingHints[setting.key] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save button -->
                <div class="sticky bottom-4 z-10">
                    <button type="submit" class="bg-gold-500 text-brand-950 px-8 py-3.5 rounded-xl font-bold hover:bg-gold-400 transition shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ settings: Object });

const groupedSettings = computed(() => {
    const groups = {};
    if (props.settings) {
        for (const [groupName, settings] of Object.entries(props.settings)) {
            groups[groupName] = settings;
        }
    }
    return groups;
});

const tabLabels = {
    general: 'General',
    homepage: 'Homepage',
    social: 'Social & Contact',
    delivery: 'Delivery',
};

const tabIcons = {
    general: '🏪',
    homepage: '🏠',
    social: '📱',
    delivery: '🛵',
};

const tabDescriptions = {
    general: 'Basic store information and content',
    homepage: 'Hero section, banner images and homepage content',
    social: 'Social media links and contact numbers',
    delivery: 'Delivery charges and minimum order amounts',
};

const settingHints = {
    site_logo: 'URL to your logo image (optional)',
    hero_image: 'Single hero background image (optional). Use Hero Images above for multiple.',
    hero_images: 'Add multiple image URLs for a rotating hero banner',
    whatsapp_number: 'Include country code, e.g. +8801722554400',
    min_order_amount: 'Minimum order amount for delivery (in Taka)',
    min_order_pickup: 'Minimum order for pickup (0 = no minimum)',
    min_order_sadar: 'Minimum order for Sadar area delivery (in Taka)',
    min_order_outside: 'Minimum order for Outside Sadar delivery (in Taka)',
    merchant_number: 'Mobile banking merchant number for advance payment',
    merchant_name: 'Name shown on merchant account',
    payment_instructions: 'Instructions shown to customer during advance payment',
    about_text: 'About us text shown on the About page',
    custom_cake_info: 'Instructions shown on the Custom Cake order page',
    opening_hours: 'e.g. Open Daily 9:00 AM - 11:00 PM',
    featured_section_title: 'Title for the featured products section on homepage',
    featured_section_subtitle: 'Subtitle for the featured products section',
    promo_banner_text: 'Text for the promotional banner on homepage',
    promo_banner_active: 'Show or hide the promo banner',
};

const shortTextFields = [
    'site_name', 'site_tagline', 'opening_hours', 'hero_title', 'hero_subtitle',
    'facebook_url', 'instagram_url', 'whatsapp_number',
    'featured_section_title', 'featured_section_subtitle', 'promo_banner_text',
];

const groupMap = {
    site_name: 'general', site_tagline: 'general', site_description: 'general', site_logo: 'general',
    about_text: 'general', opening_hours: 'general', custom_cake_info: 'general',
    hero_title: 'homepage', hero_subtitle: 'homepage', hero_image: 'homepage', hero_images: 'homepage',
    featured_section_title: 'homepage', featured_section_subtitle: 'homepage',
    promo_banner_text: 'homepage', promo_banner_active: 'homepage',
    facebook_url: 'social', instagram_url: 'social', whatsapp_number: 'social',
    min_order_amount: 'delivery', min_order_pickup: 'delivery',
    min_order_sadar: 'delivery', min_order_outside: 'delivery',
    merchant_number: 'delivery', merchant_name: 'delivery', payment_instructions: 'delivery',
};

const settingValues = ref({});
const heroImages = ref([]);

const initValues = () => {
    if (props.settings) {
        for (const [groupName, settings] of Object.entries(props.settings)) {
            for (const setting of settings) {
                if (setting.key === 'hero_images') {
                    try {
                        heroImages.value = JSON.parse(setting.value || '[]');
                        if (!heroImages.value.length) heroImages.value = [''];
                    } catch {
                        heroImages.value = [''];
                    }
                } else {
                    settingValues.value[setting.key] = setting.value || '';
                }
            }
        }
    }
};

initValues();

const activeTab = ref(Object.keys(groupedSettings.value)[0] || 'general');

const saveSettings = () => {
    // Save hero images as JSON
    settingValues.value['hero_images'] = JSON.stringify(heroImages.value.filter(u => u.trim()));

    const settingsArray = Object.entries(settingValues.value).map(([key, value]) => ({
        key, value, group: groupMap[key] || 'general',
    }));
    router.post(route('admin.settings.update'), { settings: settingsArray });
};
</script>
