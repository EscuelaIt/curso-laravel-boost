<script lang="ts">
    import Send from '@lucide/svelte/icons/send';
    import { slide } from 'svelte/transition';
    import MessageForm from '@/components/MessageForm.svelte';
    import { Button } from '@/components/ui/button';
    import UserInfo from '@/components/UserInfo.svelte';
    import type { User } from '@/types';

    let {
        user,
    }: {
        user: Pick<User, 'id' | 'name' | 'email'>;
    } = $props();

    let isMessageFormOpen = $state(false);
</script>

<li class="p-4">
    <div class="flex items-center gap-3">
        <UserInfo user={user as User} showEmail />
        <Button
            variant="outline"
            size="sm"
            onclick={() => (isMessageFormOpen = !isMessageFormOpen)}
            aria-expanded={isMessageFormOpen}
        >
            <Send />
            Send message
        </Button>
    </div>

    {#if isMessageFormOpen}
        <div transition:slide={{ duration: 250 }} class="pt-4">
            <MessageForm
                recipientId={user.id}
                onCancel={() => (isMessageFormOpen = false)}
                onSent={() => (isMessageFormOpen = false)}
            />
        </div>
    {/if}
</li>
