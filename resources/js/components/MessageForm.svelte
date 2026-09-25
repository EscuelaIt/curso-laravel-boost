<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { store } from '@/routes/messages';

    let {
        recipientId,
        onCancel,
        onSent,
    }: {
        recipientId: number;
        onCancel?: () => void;
        onSent?: () => void;
    } = $props();

    const uid = $props.id();

    const form = useForm<{
        recipient_id?: number;
        subject: string;
        body: string;
    }>({
        subject: '',
        body: '',
    });

    function submit(event: SubmitEvent): void {
        event.preventDefault();

        form.transform((data) => ({
            ...data,
            recipient_id: recipientId,
        })).submit(store(), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                onSent?.();
            },
        });
    }
</script>

<form onsubmit={submit} class="space-y-4">
    <InputError message={form.errors.recipient_id} />

    <div class="grid gap-2">
        <Label for="{uid}-subject">Subject</Label>
        <Input
            id="{uid}-subject"
            name="subject"
            bind:value={form.subject}
            required
            placeholder="Subject"
        />
        <InputError message={form.errors.subject} />
    </div>

    <div class="grid gap-2">
        <Label for="{uid}-body">Message</Label>
        <textarea
            id="{uid}-body"
            name="body"
            bind:value={form.body}
            required
            rows="4"
            placeholder="Write your message..."
            class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
        ></textarea>
        <InputError message={form.errors.body} />
    </div>

    <div class="flex justify-end gap-2">
        <Button type="button" variant="ghost" onclick={onCancel}>
            Cancel
        </Button>
        <Button type="submit" disabled={form.processing}>Send</Button>
    </div>
</form>
