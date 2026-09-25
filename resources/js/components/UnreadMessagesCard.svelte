<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import MessageSquare from '@lucide/svelte/icons/message-square';
    import { Skeleton } from '@/components/ui/skeleton';
    import { index } from '@/routes/messages';

    let {
        unreadMessagesCount,
    }: {
        unreadMessagesCount?: number;
    } = $props();
</script>

<Link
    href={index()}
    class="flex h-full flex-col justify-between gap-4 rounded-xl p-4 transition-colors hover:bg-accent/50"
>
    <span class="flex items-center gap-2 text-sm text-muted-foreground">
        <MessageSquare class="size-4 shrink-0" />
        Unread messages
    </span>

    {#if unreadMessagesCount === undefined}
        <div class="space-y-2">
            <Skeleton class="h-8 w-12" />
            <Skeleton class="h-4 w-3/4" />
        </div>
    {:else}
        <div class="space-y-1">
            <span
                class="block text-3xl leading-none font-semibold tabular-nums"
            >
                {unreadMessagesCount}
            </span>
        </div>
    {/if}
</Link>
