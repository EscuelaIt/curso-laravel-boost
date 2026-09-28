<script module lang="ts">
    import { index } from '@/routes/users';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Users',
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
        await import('@/components/user/eit-user-crud.js');
    });
</script>

<AppHead title="Users" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
    <Heading title="Users" description="Manage the users of the project" />

    <eit-user-crud language="es"></eit-user-crud>
</div>
