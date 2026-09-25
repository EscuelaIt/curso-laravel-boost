<script module lang="ts">
    import { index } from '@/routes/messages';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Messages',
                href: index(),
            },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import UserListItem from '@/components/UserListItem.svelte';
    import type { User } from '@/types';

    let {
        users,
    }: {
        users: Pick<User, 'id' | 'name' | 'email'>[];
    } = $props();
</script>

<AppHead title="Messages" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
    <Heading
        title="Messages"
        description="Choose a user to send them a message"
    />

    {#if users.length === 0}
        <p class="text-sm text-muted-foreground">
            There are no other users yet.
        </p>
    {:else}
        <ul
            class="divide-y divide-sidebar-border/70 rounded-xl border border-sidebar-border/70 dark:divide-sidebar-border dark:border-sidebar-border"
        >
            {#each users as user (user.id)}
                <UserListItem {user} />
            {/each}
        </ul>
    {/if}
</div>
