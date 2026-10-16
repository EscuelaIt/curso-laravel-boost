<script lang="ts">
    import { router, setLayoutProps } from '@inertiajs/svelte';
    import { onMount } from 'svelte';
    import { toast } from 'svelte-sonner';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { dashboard } from '@/routes';
    import { index, show } from '@/routes/projects';

    let { projectId }: { projectId: number } = $props();

    $effect(() => {
        setLayoutProps({
            breadcrumbs: [
                {
                    title: 'Projects',
                    href: index(),
                },
                {
                    title: 'Project detail',
                    href: show(projectId),
                },
            ],
        });
    });

    function handleLoadError() {
        toast.error('The requested project does not exist.');
        router.visit(dashboard());
    }

    onMount(async () => {
        await import('@/boostrap.js');
        await import('@/components/project/eit-project-single.js');
    });
</script>

<AppHead title="Project detail" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
    <Heading
        title="Project detail"
        description="Information about the project and its users"
    />

    <eit-project-single
        relatedId={projectId}
        language="es"
        oncrud-item-detail-load-error={handleLoadError}
    ></eit-project-single>
</div>
