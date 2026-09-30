<script lang="ts">
    import { router, setLayoutProps } from '@inertiajs/svelte';
    import { onMount } from 'svelte';
    import { toast } from 'svelte-sonner';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { dashboard } from '@/routes';
    import { index, show } from '@/routes/users';

    let { userId }: { userId: number } = $props();

    $effect(() => {
        setLayoutProps({
            breadcrumbs: [
                {
                    title: 'Users',
                    href: index(),
                },
                {
                    title: 'User detail',
                    href: show(userId),
                },
            ],
        });
    });

    function handleLoadError() {
        toast.error('The requested user does not exist.');
        router.visit(dashboard());
    }

    onMount(async () => {
        await import('@/boostrap.js');
        await import('@/components/user/eit-user-single.js');
    });
</script>

<AppHead title="User detail" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
    <Heading title="User detail" description="Information about the user" />

    <eit-user-single
        relatedId={userId}
        language="es"
        oncrud-item-detail-load-error={handleLoadError}
    ></eit-user-single>
</div>
