import { toast } from 'svelte-sonner';

type CrudFeedbackDetail = {
    msg?: string;
    previousDetail?: unknown;
};

type CrudFeedbackEvent = {
    name: string;
    type: 'success' | 'error';
    defaultMessage: string;
};

const crudFeedbackEvents: CrudFeedbackEvent[] = [
    {
        name: 'delete-success',
        type: 'success',
        defaultMessage: 'Deleted successfully.',
    },
    {
        name: 'delete-error',
        type: 'error',
        defaultMessage: 'The item could not be deleted.',
    },
    {
        name: 'crud-action-success',
        type: 'success',
        defaultMessage: 'Action completed successfully.',
    },
    {
        name: 'crud-action-error',
        type: 'error',
        defaultMessage: 'The action could not be completed.',
    },
    {
        name: 'relation-success-save',
        type: 'success',
        defaultMessage: 'Relation updated.',
    },
    {
        name: 'relation-error-save',
        type: 'error',
        defaultMessage: 'The relation could not be updated.',
    },
    {
        name: 'relation-error-get',
        type: 'error',
        defaultMessage: 'The related items could not be loaded.',
    },
];

export function initializeCrudFeedback(): void {
    if (typeof window === 'undefined') {
        return;
    }

    crudFeedbackEvents.forEach(({ name, type, defaultMessage }) => {
        window.addEventListener(name, (event) => {
            const detail = (event as CustomEvent<CrudFeedbackDetail | null>)
                .detail;

            toast[type](detail?.msg || defaultMessage);
        });
    });
}
