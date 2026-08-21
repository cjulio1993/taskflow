<script setup lang="ts">
import { ref } from 'vue';
import type { ProjectMember } from '../api/members-api';
import BaseModal from './BaseModal.vue';

defineProps<{ members: ProjectMember[]; canManage: boolean; saving: boolean; error: string }>();
const emit = defineEmits<{ add: [email: string]; remove: [member: ProjectMember]; close: [] }>();
const email = ref('');
function submit(): void { emit('add', email.value); email.value = ''; }
</script>

<template>
    <BaseModal title="Project members" @close="$emit('close')">
        <form v-if="canManage" class="mb-5 flex gap-2" @submit.prevent="submit"><input v-model="email" required type="email" placeholder="member@example.com" class="min-w-0 flex-1 rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-white outline-none focus:border-cyan-300"><button :disabled="saving" class="rounded-md bg-cyan-300 px-3 py-2 text-sm font-semibold text-slate-950 disabled:opacity-60">Add</button></form>
        <p v-if="error" class="mb-4 rounded-md border border-rose-500/40 bg-rose-500/10 p-3 text-sm text-rose-200">{{ error }}</p>
        <ul class="divide-y divide-slate-800"><li v-for="member in members" :key="member.id" class="flex items-center justify-between gap-3 py-3"><div class="min-w-0"><p class="truncate text-sm font-medium text-white">{{ member.name }}</p><p class="truncate text-xs text-slate-400">{{ member.email }}</p></div><div class="flex items-center gap-3"><span class="rounded bg-slate-800 px-2 py-1 text-xs capitalize text-slate-300">{{ member.role }}</span><button v-if="canManage && member.role !== 'owner'" class="text-xs text-rose-300 hover:text-rose-200" @click="$emit('remove', member)">Remove</button></div></li></ul>
    </BaseModal>
</template>
