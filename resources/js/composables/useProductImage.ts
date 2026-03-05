/**
 * Helper to get product image URL.
 *
 * Priority order:
 * 1. product.images[] - Filament FileUpload stores paths as JSON array in the `images` column
 *    e.g. ["products/01K7A59EX5G...png"]
 * 2. product.productImages[] - ProductImage relation (HasMany) objects with image_url accessor
 *    e.g. [{ id: 1, image: "...", image_url: "http://..." }]
 *
 * NOTE: The relation was renamed from images() to productImages() to avoid Laravel's
 * serialization conflict where relations override same-named JSON columns.
 */
export function getProductImage(
    product: any,
    fallback = 'https://placehold.co/400x400/f3f4f6/9ca3af?text=Sin+imagen'
): string {
    // Priority 1: Filament images column (array of path strings)
    if (Array.isArray(product?.images) && product.images.length > 0) {
        const first = product.images[0];
        if (typeof first === 'string' && first.length > 0) {
            return '/storage/' + first;
        }
        // Shouldn't happen with Filament, but handle objects too
        if (first && typeof first === 'object') {
            if (first.image_url) return first.image_url;
            if (first.image) return '/storage/' + first.image;
        }
    }

    // Priority 2: ProductImage relation (productImages)
    if (Array.isArray(product?.productImages) && product.productImages.length > 0) {
        const first = product.productImages[0];
        if (first?.image_url) return first.image_url;
        if (first?.image) return '/storage/' + first.image;
    }

    return fallback;
}

export function getAllProductImages(product: any): string[] {
    const urls: string[] = [];

    // From Filament images column
    if (Array.isArray(product?.images) && product.images.length > 0) {
        for (const img of product.images) {
            if (typeof img === 'string' && img.length > 0) {
                urls.push('/storage/' + img);
            } else if (img?.image_url) {
                urls.push(img.image_url);
            } else if (img?.image) {
                urls.push('/storage/' + img.image);
            }
        }
    }

    // From ProductImage relation
    if (urls.length === 0 && Array.isArray(product?.productImages)) {
        for (const img of product.productImages) {
            if (img?.image_url) urls.push(img.image_url);
            else if (img?.image) urls.push('/storage/' + img.image);
        }
    }

    return urls;
}
