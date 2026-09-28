import { AxiosInstanceBuilder } from '@dile/crud/lib/AxiosInstanceBuilder';

if (typeof window !== 'undefined') {
    new AxiosInstanceBuilder({
        withCredentials: true,
        withXSRFToken: true,
        baseURL: '/'
    });
}
