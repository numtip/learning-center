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
