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

                            <div v-else-if="setting.key === 'mail_mailer'">
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">Mail transport</label>
                                <select v-model="settingValues[setting.key]" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none">
                                    <option value="smtp">SMTP</option>
                                    <option value="log">Log only (testing)</option>
                                </select>
                            </div>

                            <div v-else-if="setting.key === 'mail_scheme'">
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">SMTP encryption</label>
                                <select v-model="settingValues[setting.key]" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none">
                                    <option value="smtps">SSL / SMTPS (port 465)</option>
                                    <option value="smtp">TLS / SMTP (port 587)</option>
                                </select>
                            </div>

                            <div v-else-if="setting.key === 'mail_password'">
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">SMTP password</label>
                                <input v-model="settingValues[setting.key]" type="password" autocomplete="new-password" placeholder="Leave blank to keep the saved password" class="w-full rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-3 text-brand-900 transition outline-none" />
                                <p class="text-xs text-brand-400 mt-1">The password is encrypted before it is stored.</p>
                            </div>

                            <div v-else-if="setting.key === 'mail_order_recipients'">
                                <label class="block text-sm font-medium text-brand-700 mb-1.5">Order notification recipients</label>
                                <p class="text-xs text-brand-400 mb-3">Every address below receives new order and custom cake notifications.</p>
                                <div v-for="(email, i) in recipientEmails" :key="i" class="flex gap-2 mb-2">
                                    <input v-model="recipientEmails[i]" type="email" placeholder="name@example.com" class="flex-1 rounded-xl border-2 border-brand-100 focus:border-gold-400 focus:ring-2 focus:ring-gold-200 bg-cream-50 px-4 py-2.5 text-brand-900 transition outline-none text-sm" />
                                    <button type="button" @click="recipientEmails.splice(i, 1)" class="rounded-xl px-3 text-red-500 hover:bg-red-50">×</button>
                                </div>
                                <button type="button" @click="recipientEmails.push('')" class="text-sm text-gold-600 font-bold hover:text-gold-700">+ Add email address</button>
                            </div>

                            <div v-else-if="['mail_order_notifications', 'mail_customer_notifications'].includes(setting.key)" class="flex items-center gap-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" :checked="settingValues[setting.key] === '1' || settingValues[setting.key] === true" @change="settingValues[setting.key] = $event.target.checked ? '1' : '0'" class="w-5 h-5 rounded text-gold-500 focus:ring-gold-400" />
                                    <span class="ml-2 text-sm font-medium text-brand-700">{{ setting.key === 'mail_order_notifications' ? 'Email new orders to the admin recipients' : 'Send confirmation to customers who provide an email' }}</span>
                                </label>
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

            <div v-if="activeTab === 'mailer'" class="mt-6 bg-brand-950 text-cream-50 rounded-2xl p-6">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div><h3 class="font-serif font-bold text-lg">Send a test email</h3><p class="text-sm text-brand-200 mt-1">Save the SMTP settings first, then verify delivery to any mailbox.</p></div>
                    <form @submit.prevent="testMail" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <input v-model="testEmail" type="email" required placeholder="test@example.com" class="rounded-xl border-0 bg-white text-brand-900 px-4 py-2.5 text-sm outline-none w-full sm:w-64" />
                        <button type="submit" :disabled="testingMail" class="rounded-xl bg-gold-500 text-brand-950 px-4 py-2.5 text-sm font-bold hover:bg-gold-400 disabled:opacity-60">{{ testingMail ? 'Sending...' : 'Send test' }}</button>
                    </form>
                </div>
                <p v-if="$page.props.errors?.mail_test" class="text-red-300 text-sm mt-3">{{ $page.props.errors.mail_test }}</p>
            </div>
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
    mailer: 'Mailer',
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
    mailer: 'SMTP sender, order recipients, and customer email confirmations',
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
    'mail_host', 'mail_username', 'mail_from_address', 'mail_from_name', 'mail_reply_to',
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
    mail_mailer: 'mailer', mail_host: 'mailer', mail_port: 'mailer', mail_scheme: 'mailer',
    mail_username: 'mailer', mail_password: 'mailer', mail_from_address: 'mailer',
    mail_from_name: 'mailer', mail_reply_to: 'mailer', mail_order_recipients: 'mailer',
    mail_order_notifications: 'mailer', mail_customer_notifications: 'mailer',
};

const settingValues = ref({});
const heroImages = ref([]);
const recipientEmails = ref([]);
const testEmail = ref('metasoftinfo@gmail.com');
const testingMail = ref(false);

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
                } else if (setting.key === 'mail_order_recipients') {
                    try {
                        recipientEmails.value = JSON.parse(setting.value || '[]');
                    } catch {
                        recipientEmails.value = [];
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
    settingValues.value['mail_order_recipients'] = JSON.stringify(recipientEmails.value.map(email => email.trim()).filter(Boolean));

    const settingsArray = Object.entries(settingValues.value).map(([key, value]) => ({
        key, value, group: groupMap[key] || 'general',
    }));
    router.post(route('admin.settings.update'), { settings: settingsArray });
};

const testMail = () => {
    testingMail.value = true;
    router.post(route('admin.settings.test-mail'), { email: testEmail.value }, { onFinish: () => testingMail.value = false });
};
</script>
