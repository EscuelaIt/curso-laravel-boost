<script module lang="ts">
    import { index } from '@/routes/projects';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Projects',
                href: index(),
            },
        ],
    };
</script>

<script lang="ts">
    import { onMount } from 'svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';

    onMount(async () => {
        await import('@/boostrap.js');
        await import('@/components/project/eit-project-crud.js');
    });
</script>

<AppHead title="Projects" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
    <Heading
        title="Projects"
        description="Manage the projects and the users working on them"
    />

    <eit-project-crud language="es"></eit-project-crud>
</div>
