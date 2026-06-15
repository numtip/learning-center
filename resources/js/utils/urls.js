/**
 * Path prefix when the app is served under a subpath (from Ziggy / APP_URL).
 */
export function getAppPathPrefix() {
    if (typeof window === 'undefined' || !window.Ziggy?.url) {
        return '';
    }

    try {
        const pathname = new URL(window.Ziggy.url).pathname.replace(/\/$/, '');
        return pathname === '/' ? '' : pathname;
    } catch {
        return '';
    }
}

/**
 * Build an internal app URL for Inertia navigation and router calls.
 */
export function appUrl(path) {
    if (!path || typeof path !== 'string') {
        return path;
    }

    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('//')) {
        return path;
    }

    const prefix = getAppPathPrefix();
    const normalized = path.startsWith('/') ? path : `/${path}`;

    if (prefix && (normalized === prefix || normalized.startsWith(`${prefix}/`))) {
        return normalized;
    }

    return `${prefix}${normalized}`;
}

/**
 * Build a public asset URL respecting ASSET_URL / subpath deployment.
 */
export function assetUrl(path) {
    const base = typeof window !== 'undefined' && window.Ziggy?.url
        ? String(window.Ziggy.url).replace(/\/$/, '')
        : '';

    const normalized = path.startsWith('/') ? path : `/${path}`;

    return `${base}${normalized}`;
}

/**
 * Build a storage URL for uploaded files.
 */
export function storageUrl(relativePath) {
    if (!relativePath) {
        return '';
    }

    if (relativePath.startsWith('http') || relativePath.startsWith('data:image')) {
        return relativePath;
    }

    return assetUrl(`/storage/${relativePath.replace(/^\/+/, '')}`);
}
