<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { AxiosError } from 'axios';
import { createProject, listProjects, type Project } from '../api/projects-api';
import BaseModal from '../components/BaseModal.vue';

const projects = ref<Project[]>([]); const loading = ref(true); const saving = ref(false); const error = ref(''); const showCreate = ref(false); const name = ref(''); const description = ref('');
function message(exception: unknown, fallback: string): string { return exception instanceof AxiosError && exception.response?.status === 422 ? 'Please check the information provided.' : fallback; }
async function load(): Promise<void> { loading.value = true; error.value = ''; try { projects.value = await listProjects(); } catch (exception) { error.value = message(exception, 'Unable to load projects.'); } finally { loading.value = false; } }
async function submit(): Promise<void> { saving.value = true; error.value = ''; try { const project = await createProject({ name: name.value, description: description.value || null }); projects.value.unshift(project); showCreate.value = false; name.value = ''; description.value = ''; } catch (exception) { error.value = message(exception, 'Unable to create the project.'); } finally { saving.value = false; } }
onMounted(load);
</script>

<template>
    <section>
        <div class="flex flex-wrap items-start justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-[0.18em] text-cyan-300">Workspace</p><h1 class="mt-2 text-3xl font-bold">Projects</h1><p class="mt-2 text-slate-400">Projects you own or collaborate on.</p></div><button class="rounded-md bg-cyan-300 px-4 py-2.5 text-sm font-semibold text-slate-950 hover:bg-cyan-200" @click="showCreate = true">New project</button></div>
        <p v-if="error && !showCreate" class="mt-6 rounded-md border border-rose-500/40 bg-rose-500/10 p-3 text-sm text-rose-200">{{ error }}</p>
        <div v-if="loading" class="mt-8 text-sm text-slate-400">Loading projects…</div>
        <div v-else-if="projects.length === 0" class="mt-8 rounded-xl border border-dashed border-slate-700 p-10 text-center"><h2 class="font-semibold">No projects yet</h2><p class="mt-2 text-sm text-slate-400">Create your first project to start planning work.</p></div>
        <div v-else class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3"><RouterLink v-for="project in projects" :key="project.id" :to="{ name: 'project-board', params: { id: project.id } }" class="rounded-xl border border-slate-800 bg-slate-900 p-5 transition hover:border-cyan-400/60 hover:bg-slate-800"><h2 class="font-semibold text-white">{{ project.name }}</h2><p class="mt-2 line-clamp-3 text-sm text-slate-400">{{ project.description || 'No description provided.' }}</p><p class="mt-5 text-xs font-medium text-cyan-300">Open board →</p></RouterLink></div>
        <BaseModal v-if="showCreate" title="Create project" @close="showCreate = false"><form class="space-y-4" @submit.prevent="submit"><p v-if="error" class="rounded-md border border-rose-500/40 bg-rose-500/10 p-3 text-sm text-rose-200">{{ error }}</p><label class="block text-sm text-slate-300">Name<input v-model="name" required maxlength="255" class="mt-1.5 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white"></label><label class="block text-sm text-slate-300">Description<textarea v-model="description" rows="4" class="mt-1.5 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white" /></label><div class="flex justify-end gap-2"><button type="button" class="rounded-md px-3 py-2 text-sm text-slate-300" @click="showCreate = false">Cancel</button><button :disabled="saving" class="rounded-md bg-cyan-300 px-4 py-2 text-sm font-semibold text-slate-950 disabled:opacity-60">{{ saving ? 'Creating…' : 'Create project' }}</button></div></form></BaseModal>
    </section>
</template>
