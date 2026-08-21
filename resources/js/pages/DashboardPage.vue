<template>
    <section class="rounded-xl border border-slate-800 bg-slate-900 p-8">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-cyan-300">TaskFlow workspace</p>
        <h1 class="mt-3 text-3xl font-bold">Keep project work clear and focused.</h1>
        <p class="mt-3 max-w-2xl text-slate-400">Create a project, add collaborators, and track work on a simple task board.</p>
        <RouterLink :to="{ name: 'projects' }" class="mt-6 inline-flex rounded-md bg-cyan-300 px-4 py-2.5 text-sm font-semibold text-slate-950 hover:bg-cyan-200">View projects</RouterLink>
    </section>
</template>
