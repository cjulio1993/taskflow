<script setup lang="ts">
import { ref, watch } from 'vue';
import type { ProjectMember } from '../api/members-api';
import type { Task, TaskPayload } from '../api/tasks-api';
import BaseModal from './BaseModal.vue';

const props = defineProps<{ task: Task | null; members: ProjectMember[]; saving: boolean; error: string }>();
const emit = defineEmits<{ save: [payload: TaskPayload]; remove: []; close: [] }>();
const title = ref(''); const description = ref(''); const status = ref<TaskPayload['status']>('todo'); const priority = ref<TaskPayload['priority']>('medium'); const assignedTo = ref<number | null>(null); const dueDate = ref('');

function resetForm(task: Task | null): void { title.value = task?.title ?? ''; description.value = task?.description ?? ''; status.value = task?.status ?? 'todo'; priority.value = task?.priority ?? 'medium'; assignedTo.value = task?.assigned_to ?? null; dueDate.value = task?.due_date ?? ''; }
watch(() => props.task, resetForm, { immediate: true });
function submit(): void { emit('save', { title: title.value, description: description.value || null, status: status.value, priority: priority.value, assigned_to: assignedTo.value, due_date: dueDate.value || null }); }
</script>

<template>
    <BaseModal :title="task ? 'Edit task' : 'Create task'" @close="$emit('close')">
        <form class="space-y-4" @submit.prevent="submit">
            <p v-if="error" class="rounded-md border border-rose-500/40 bg-rose-500/10 p-3 text-sm text-rose-200">{{ error }}</p>
            <label class="block text-sm text-slate-300">Title<input v-model="title" required maxlength="255" class="mt-1.5 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none focus:border-cyan-300"></label>
            <label class="block text-sm text-slate-300">Description<textarea v-model="description" rows="3" class="mt-1.5 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none focus:border-cyan-300" /></label>
            <div class="grid gap-4 sm:grid-cols-2"><label class="block text-sm text-slate-300">Status<select v-model="status" class="mt-1.5 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white"><option value="todo">Todo</option><option value="in_progress">In progress</option><option value="done">Done</option></select></label><label class="block text-sm text-slate-300">Priority<select v-model="priority" class="mt-1.5 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select></label></div>
            <div class="grid gap-4 sm:grid-cols-2"><label class="block text-sm text-slate-300">Assignee<select v-model="assignedTo" class="mt-1.5 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white"><option :value="null">Unassigned</option><option v-for="member in members" :key="member.id" :value="member.id">{{ member.name }}</option></select></label><label class="block text-sm text-slate-300">Due date<input v-model="dueDate" type="date" class="mt-1.5 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-white"></label></div>
            <div class="flex items-center justify-between gap-3 pt-2"><button v-if="task" type="button" :disabled="saving" class="text-sm text-rose-300 hover:text-rose-200 disabled:opacity-60" @click="$emit('remove')">Delete task</button><span v-else /><div class="flex gap-2"><button type="button" class="rounded-md px-3 py-2 text-sm text-slate-300 hover:bg-slate-800" @click="$emit('close')">Cancel</button><button :disabled="saving" class="rounded-md bg-cyan-300 px-4 py-2 text-sm font-semibold text-slate-950 disabled:opacity-60">{{ saving ? 'Saving…' : task ? 'Save changes' : 'Create task' }}</button></div></div>
        </form>
    </BaseModal>
</template>
