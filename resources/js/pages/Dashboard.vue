<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { BriefcaseBusiness, ChevronLeft, CirclePlus, ExternalLink, FolderKanban, LogOut, Mail, Pencil, Plus, Sparkles, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { portfolioImageUrl } from '@/lib/portfolioImageUrl';
import '../../css/dashboard.css';

type Section = 'overview' | 'projects' | 'skills' | 'facts' | 'contacts';
type Item = { id: number; title?: string | null; link?: string | null; categories?: string[] | string | null; desk_img?: string | null; mobile_img?: string | null; image?: string | null; text?: string | null; name?: string; email?: string; created_at?: string };
const props = defineProps<{ section: Section; items: Item[]; counts: Record<string, number>; user: { name: string; email: string; avatar_url?: string | null } }>();

const nav = [
    { key: 'overview', label: 'Home', icon: BriefcaseBusiness, href: '/dashboard' },
    { key: 'contacts', label: 'Contact Form', icon: Mail, href: '/dashboard/contacts' },
    { key: 'projects', label: 'Projects', icon: FolderKanban, href: '/dashboard/projects' },
    { key: 'skills', label: 'Skills', icon: Sparkles, href: '/dashboard/skills' },
    { key: 'facts', label: 'Facts', icon: CirclePlus, href: '/dashboard/facts' },
] as const;
const sidebarOpen = ref(true);
const editing = ref<number | null>(null);
const formOpen = ref(false);
const form = useForm({
    title: '', link: '', categories: '', desk_img: '', mobile_img: '', image: '', text: '',
    desk_upload: null as File | null, mobile_upload: null as File | null, image_upload: null as File | null,
});
const currentTitle = computed(() => nav.find((item) => item.key === props.section)?.label ?? 'Dashboard');
const emptyText = computed(() => props.section === 'contacts' ? 'No messages yet.' : `No ${props.section} yet. Add the first one.`);

watch(() => props.section, () => { formOpen.value = false; editing.value = null; form.reset(); });
function openForm(item?: Item) {
    form.reset();
    form.clearErrors();
    editing.value = item?.id ?? null;
    form.title = item?.title ?? '';
    form.link = item?.link ?? '';
    form.categories = Array.isArray(item?.categories) ? item.categories.join(', ') : parseCategories(item?.categories).join(', ');
    form.desk_img = item?.desk_img ?? '';
    form.mobile_img = item?.mobile_img ?? '';
    form.image = item?.image ?? '';
    form.text = item?.text ?? '';
    formOpen.value = true;
}
function parseCategories(value: Item['categories']): string[] {
    if (Array.isArray(value)) return value;
    if (!value) return [];
    try {
        const parsed: unknown = JSON.parse(value);
        return Array.isArray(parsed) ? parsed.map(String) : [value];
    } catch { return [value]; }
}
function plainText(value: string | null | undefined): string {
    if (!value) return '';
    const element = document.createElement('div');
    element.innerHTML = value;
    return element.textContent ?? '';
}
function onFile(event: Event, field: 'desk_upload' | 'mobile_upload' | 'image_upload') {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form[field] = file;
}
function submit() {
    const options = { preserveScroll: true, onSuccess: () => { formOpen.value = false; editing.value = null; form.reset(); } };
    if (editing.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/${props.section}/${editing.value}`, options);
    } else {
        form.transform((data) => data).post(`/dashboard/${props.section}`, options);
    }
}
function remove(item: Item) {
    if (window.confirm(`Delete ${item.title || item.name || 'this message'}?`)) {
        router.delete(`/dashboard/${props.section}/${item.id}`, { preserveScroll: true });
    }
}
function logout() { router.post('/logout'); }
</script>

<template>
    <Head :title="section === 'overview' ? 'Dashboard' : currentTitle" />
    <div class="dashboard-wrapper" :class="{ 'sidebar-collapsed': !sidebarOpen }">
        <aside class="dashboard-sidebar" aria-label="Dashboard navigation">
            <button class="sidebar-toggle" type="button" :aria-label="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'" @click="sidebarOpen = !sidebarOpen">
                <ChevronLeft :size="24" :class="{ rotated: !sidebarOpen }" />
            </button>
            <nav>
                <Link v-for="item in nav" :key="item.key" :href="item.href" :class="{ active: section === item.key }" :aria-label="item.label">
                    <component :is="item.icon" :size="22" :stroke-width="2" />
                    <span>{{ item.label }}</span>
                </Link>
            </nav>
            <Link class="visit-link" href="/" aria-label="Visit portfolio"><ExternalLink :size="20" /><span>Visit website</span></Link>
        </aside>

        <div class="dashboard-main">
            <header class="dashboard-header">
                <div class="dashboard-wordmark">Daniela <span>CMS</span></div>
                <div class="dashboard-header-actions">
                    <div class="dashboard-user">
                        <img v-if="user.avatar_url" :src="user.avatar_url" alt="" />
                        <span v-else class="dashboard-avatar">{{ user.name.split(' ').map((word) => word[0]).join('').slice(0, 2).toUpperCase() }}</span>
                        <span class="user-label"><strong>{{ user.name }}</strong><small>Admin</small></span>
                    </div>
                    <button type="button" class="logout-button" @click="logout"><LogOut :size="20" /><span>Logout</span></button>
                </div>
            </header>

            <main class="dashboard-content">
                <template v-if="section === 'overview'">
                    <div class="dashboard-heading"><div><span class="eyebrow">PORTFOLIO ADMIN</span><h1>Welcome back, {{ user.name.split(' ')[0] }}.</h1><p>Manage the content shown on your portfolio.</p></div></div>
                    <div class="dashboard-stats">
                        <Link v-for="item in nav.slice(1)" :key="item.key" :href="item.href" class="stat-card">
                            <component :is="item.icon" :size="26" /><strong>{{ counts[item.key] ?? 0 }}</strong><span>{{ item.label }}</span>
                        </Link>
                    </div>
                    <section class="dashboard-panel intro-panel"><h2>Your workspace</h2><p>Choose a section from the sidebar to update your projects, skills and facts, or read messages from the contact form.</p><Link href="/" class="dashboard-primary">View portfolio <ExternalLink :size="16" /></Link></section>
                </template>
                <template v-else>
                    <div class="dashboard-heading">
                        <div><span class="eyebrow">PORTFOLIO ADMIN</span><h1>{{ section === 'contacts' ? 'Contact Form' : currentTitle }}</h1><p>{{ counts[section] ?? 0 }} {{ section === 'contacts' ? 'messages' : 'items' }}</p></div>
                        <button v-if="section !== 'contacts'" class="dashboard-primary" type="button" @click="openForm()"><Plus :size="19" /> Add {{ section.slice(0, -1) }}</button>
                    </div>

                    <section v-if="formOpen" class="dashboard-panel editor-panel">
                        <div class="panel-heading"><h2>{{ editing ? 'Edit' : 'Add' }} {{ section.slice(0, -1) }}</h2><button type="button" class="text-button" @click="formOpen = false">Cancel</button></div>
                        <form class="dashboard-form" @submit.prevent="submit">
                            <label>Title{{ section === 'skills' ? ' (optional)' : '' }} <input v-model="form.title" type="text" :required="section !== 'skills'" maxlength="255" /><small v-if="form.errors.title" class="form-error">{{ form.errors.title }}</small></label>
                            <template v-if="section === 'projects'">
                                <label>Project link <input v-model="form.link" type="url" placeholder="https://..." /><small v-if="form.errors.link" class="form-error">{{ form.errors.link }}</small></label>
                                <label>Categories <input v-model="form.categories" type="text" placeholder="Design, Development" /><small>Separate categories with commas.</small><small v-if="form.errors.categories" class="form-error">{{ form.errors.categories }}</small></label>
                                <label>Desktop image URL <input v-model="form.desk_img" type="text" placeholder="/portfolio/assets/projects/example.webp" /></label>
                                <label>Or upload a desktop image <input type="file" accept="image/*" @change="onFile($event, 'desk_upload')" /><small v-if="form.errors.desk_upload" class="form-error">{{ form.errors.desk_upload }}</small></label>
                                <label>Mobile image URL <input v-model="form.mobile_img" type="text" placeholder="/portfolio/assets/projects/example.webp" /></label>
                                <label>Or upload a mobile image <input type="file" accept="image/*" @change="onFile($event, 'mobile_upload')" /><small v-if="form.errors.mobile_upload" class="form-error">{{ form.errors.mobile_upload }}</small></label>
                            </template>
                            <template v-else-if="section === 'skills'">
                                <label>Image URL <input v-model="form.image" type="text" placeholder="/portfolio/assets/skills/example.svg" /></label>
                                <label>Or upload an image <input type="file" accept="image/*" @change="onFile($event, 'image_upload')" /><small v-if="form.errors.image_upload" class="form-error">{{ form.errors.image_upload }}</small></label>
                            </template>
                            <label v-else>Text <textarea v-model="form.text" rows="8" placeholder="<p>First paragraph</p><p>Second paragraph</p>" /><small>Basic HTML is supported: p, br, strong, em, ul, ol and li.</small><small v-if="form.errors.text" class="form-error">{{ form.errors.text }}</small></label>
                            <div class="form-actions"><button class="dashboard-primary" type="submit" :disabled="form.processing">{{ form.processing ? 'Saving…' : editing ? 'Save changes' : 'Create' }}</button></div>
                        </form>
                    </section>

                    <section class="dashboard-panel list-panel">
                        <div class="panel-heading"><h2>All {{ section === 'contacts' ? 'messages' : section }}</h2><span>{{ items.length }} shown</span></div>
                        <p v-if="!items.length" class="empty-state">{{ emptyText }}</p>
                        <div v-else class="dashboard-list">
                            <article v-for="item in items" :key="item.id" class="dashboard-row">
                                <div v-if="section === 'projects' || section === 'skills'" class="row-image">
                                    <img v-if="section === 'projects' ? item.desk_img : item.image" :src="portfolioImageUrl(section === 'projects' ? item.desk_img : item.image)" alt="" loading="eager" />
                                    <FolderKanban v-else :size="22" />
                                </div>
                                <div class="row-text">
                                    <strong>{{ item.title || item.name || 'Untitled skill' }}</strong>
                                    <span v-if="section === 'projects'">{{ parseCategories(item.categories).join(' · ') || 'No categories' }}</span>
                                    <span v-else-if="section === 'contacts'">{{ item.email }}</span>
                                    <span v-else-if="section === 'facts'">{{ plainText(item.text) }}</span>
                                    <span v-if="section === 'contacts'" class="message-text">{{ item.text }}</span>
                                </div>
                                <div class="row-actions">
                                    <button v-if="section !== 'contacts'" type="button" :aria-label="`Edit ${item.title || 'skill'}`" @click="openForm(item)"><Pencil :size="18" /></button>
                                    <button type="button" :aria-label="`Delete ${item.title || item.name || 'skill'}`" @click="remove(item)"><Trash2 :size="18" /></button>
                                </div>
                            </article>
                        </div>
                    </section>
                </template>
            </main>
        </div>
    </div>
</template>
