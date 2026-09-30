<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, home } from '@/routes';

    let { status }: { status: number } = $props();

    const titles: Record<number, string> = {
        404: 'Page not found',
    };

    const descriptions: Record<number, string> = {
        404: 'Sorry, the page you are looking for could not be found.',
    };

    const title = $derived(titles[status] ?? 'Error');
    const description = $derived(
        descriptions[status] ?? 'Sorry, something went wrong.',
    );
    const backUrl = $derived(page.props.auth?.user ? dashboard() : home());
</script>

<AppHead title={title} />

<div
    class="flex min-h-screen flex-col items-center justify-center gap-4 bg-background p-6 text-center"
>
    <p class="text-6xl font-semibold text-muted-foreground">{status}</p>
    <h1 class="text-2xl font-semibold">{title}</h1>
    <p class="text-muted-foreground">{description}</p>
    <Link href={toUrl(backUrl)} class="text-sm underline underline-offset-4">
        Go back
    </Link>
</div>
