/**
 * Resolve the Ziggy config object regardless of how @routes injects it.
 *
 * Laravel's @routes directive uses `const Ziggy = {...}` in a classic <script>
 * tag. `const` at the top level of a classic script does NOT become a property
 * of `window`, but IS accessible as a global identifier.  We therefore try both
 * `window.Ziggy` (older Ziggy / manual assignment) and the bare `Ziggy` global.
 */
function getZiggyConfig() {
    if (typeof window === 'undefined') return null;
    // eslint-disable-next-line no-undef
    return window.Ziggy ?? (typeof Ziggy !== 'undefined' ? Ziggy : null);
}

/**
 * Path prefix when the app is served under a subpath (from Ziggy / APP_URL).
 */
export function getAppPathPrefix() {
    try {
        const cfg = getZiggyConfig();
        if (!cfg?.url) return '';
        const pathname = new URL(cfg.url).pathname.replace(/\/$/, '');
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
    try {
        const cfg = getZiggyConfig();
        const base = cfg?.url ? String(cfg.url).replace(/\/$/, '') : '';
        const normalized = path.startsWith('/') ? path : `/${path}`;
        return `${base}${normalized}`;
    } catch {
        const normalized = path.startsWith('/') ? path : `/${path}`;
        return normalized;
    }
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
