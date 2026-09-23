export function portfolioImageUrl(value: string | null | undefined): string {
    if (!value) return '';
    try {
        const url = new URL(value);
        if (['localhost', '127.0.0.1', '[::1]'].includes(url.hostname) && url.pathname.startsWith('/storage/')) {
            return url.pathname;
        }
    } catch {
        // Relative paths are handled below.
    }
    if (/^https?:\/\//i.test(value) || value.startsWith('/storage/') || value.startsWith('/portfolio/')) return value;
    return `/portfolio/${value.replace(/^\/+/, '')}`;
}
