<script setup lang="ts">
import type { ProjectMember } from '../api/members-api';
import type { Task } from '../api/tasks-api';

const props = defineProps<{ task: Task; members: ProjectMember[] }>();
defineEmits<{ select: [task: Task] }>();

const priorityClasses = { low: 'bg-sky-500/15 text-sky-200', medium: 'bg-amber-500/15 text-amber-200', high: 'bg-rose-500/15 text-rose-200' };

function assigneeName(): string | null {
    return props.members.find((member) => member.id === props.task.assigned_to)?.name ?? null;
}
</script>

<template>
    <button class="w-full rounded-lg border border-slate-700 bg-slate-900 p-4 text-left shadow-sm transition hover:border-cyan-400/60 hover:bg-slate-800" @click="$emit('select', task)">
        <div class="flex items-start justify-between gap-3"><h4 class="font-medium text-slate-100">{{ task.title }}</h4><span class="shrink-0 rounded px-2 py-0.5 text-xs font-medium capitalize" :class="priorityClasses[task.priority]">{{ task.priority }}</span></div>
        <p v-if="assigneeName()" class="mt-3 text-xs text-slate-400">Assigned to {{ assigneeName() }}</p>
        <p v-if="task.due_date" class="mt-1 text-xs text-slate-500">Due {{ task.due_date }}</p>
    </button>
</template>
